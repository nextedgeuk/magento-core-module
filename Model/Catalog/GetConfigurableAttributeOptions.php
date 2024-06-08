<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace NextEdge\Core\Model\Catalog;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;
use NextEdge\Core\Model\Eav\GetAttributeBackendTableInterface;
use NextEdge\Core\Model\Eav\GetEavAttributeOptionValueDataInterface;
use NextEdge\Core\Model\Utils\GetEntityMetadataInterface;

/**
 * @inheritDoc
 */
class GetConfigurableAttributeOptions implements GetConfigurableAttributeOptionsInterface
{
    /**
     * @var AdapterInterface
     */
    private AdapterInterface $connection;

    /**
     * @var GetAttributeBackendTableInterface
     */
    private GetAttributeBackendTableInterface $getAttributeBackendTable;

    /**
     * @var GetEavAttributeOptionValueDataInterface
     */
    private GetEavAttributeOptionValueDataInterface $getEavAttributeOptionValueData;

    /**
     * @var GetEntityMetadataInterface
     */
    private GetEntityMetadataInterface $getEntityMetadata;

    /**
     * @var array|string[]
     */
    private array $dataInMemory = [];

    /**
     * @param GetAttributeBackendTableInterface $getAttributeBackendTable
     * @param GetEavAttributeOptionValueDataInterface $getEavAttributeOptionValueData
     * @param GetEntityMetadataInterface $getEntityMetadata
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        GetAttributeBackendTableInterface $getAttributeBackendTable,
        GetEavAttributeOptionValueDataInterface $getEavAttributeOptionValueData,
        GetEntityMetadataInterface $getEntityMetadata,
        ResourceConnection $resourceConnection
    ) {
        $this->getAttributeBackendTable = $getAttributeBackendTable;
        $this->getEavAttributeOptionValueData = $getEavAttributeOptionValueData;
        $this->getEntityMetadata = $getEntityMetadata;
        $this->connection = $resourceConnection->getConnection();
    }

    /**
     * @inheritDoc
     */
    public function execute(int $productId): array
    {
        if (!isset($this->dataInMemory[$productId])) {
            $this->dataInMemory[$productId] = $this->getData($productId);
        }

        return $this->dataInMemory[$productId];
    }

    /**
     * @param int $productId
     * @return array
     * @throws LocalizedException
     */
    private function getData(int $productId): array
    {
        $select = $this->connection->select()
            ->from(
                $this->connection->getTableName('catalog_product_super_attribute'),
                'attribute_id'
            )
            ->where('product_id = ?', $productId);

        $result = [];
        foreach (array_map('intval', $this->connection->fetchCol($select)) as $attributeId) {
            if (!$attributeData = $this->getAttributeData($productId, $attributeId)) {
                continue;
            }

            $result[$attributeId]['product_id'] = $productId;
            $result[$attributeId]['attribute_id'] = $attributeId;

            $attributeOptions = $this->getEavAttributeOptionValueData->execute($attributeId);
            foreach ($attributeData as $index => $item) {
                if (isset($item['attribute_code'])) {
                    $result[$attributeId]['attribute_code'] = $item['attribute_code'];
                }

                if (isset($item['value_id']) && $value = $attributeOptions[$item['value_id']]['value'] ?? null) {
                    $attributeData[$index]['value_label'] = $value;
                }
            }

            $result[$attributeId]['values'] = $attributeData;
        }

        return $result;
    }

    /**
     * @param int $productId
     * @param int $attributeId
     * @return array
     * @throws LocalizedException
     */
    private function getAttributeData(int $productId, int $attributeId): array
    {
        $entityLinkField = $this->getEntityMetadata->getLinkField();
        $backendTable = $this->getAttributeBackendTable->execute(
            $attributeId,
            ProductAttributeInterface::ENTITY_TYPE_CODE
        );

        $select = $this->connection->select()
            ->from(
                ['cpsa' => $this->connection->getTableName('catalog_product_super_attribute')],
                [
                    'sku' => 'cpe_child.sku',
                    'entity_id' => 'cpe_child.entity_id',
                    'plenty_item_id' => 'cpe_child.plenty_item_id',
                    'plenty_variation_id' => 'cpe_child.plenty_variation_id',
                    'parent_entity_id' => 'cpe.entity_id',
                    'parent_plenty_item_id' => 'cpe.plenty_item_id',
                    'parent_plenty_variation_id' => 'cpe.plenty_variation_id',
                    'attribute_code' => 'ea.attribute_code',
                    'attribute_id' => 'ea.attribute_id',
                    'value_id' => 'cpev.value'
                ]
            )
            ->joinInner(
                ['cpe' => $this->connection->getTableName('catalog_product_entity')],
                "cpe.$entityLinkField = cpsa.product_id",
                null
            )
            ->joinInner(
                ['cpsl' => $this->connection->getTableName('catalog_product_super_link')],
                'cpsl.parent_id = cpsa.product_id',
                null
            )
            ->joinInner(
                ['ea' => $this->connection->getTableName('eav_attribute')],
                'ea.attribute_id = cpsa.attribute_id',
                null
            )
            ->joinInner(
                ['cpe_child' => $this->connection->getTableName('catalog_product_entity')],
                'cpe_child.entity_id = cpsl.product_id',
                null
            )
            ->joinInner(
                ['cpev' => $backendTable],
                "cpev.attribute_id = cpsa.attribute_id"
                . " AND cpev.store_id = 0"
                ." AND cpev.$entityLinkField = cpe_child.$entityLinkField",
                null
            )
            ->joinLeft(
                ['eao' => $this->connection->getTableName('eav_attribute_option')],
                'eao.option_id = cpev.value',
                null
            )
            ->order('eao.sort_order ASC')
            ->where('cpsa.product_id = ?', $productId)
            ->where('ea.attribute_id = ?', $attributeId);

        return $this->connection->fetchAll($select);
    }
}
