<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace NextEdge\Core\Model\Eav;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * @inheritDoc
 */
class GetAttributeSetData implements GetAttributeSetDataInterface
{
    /**
     * @var AdapterInterface
     */
    private AdapterInterface $connection;

    /**
     * @var array|string[]
     */
    private array $dataInMemory;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->connection = $resourceConnection->getConnection();
    }

    /**
     * @inheritDoc
     */
    public function execute(string $entityTypeCode): array
    {
        if (!isset($this->dataInMemory[$entityTypeCode])) {
            $this->dataInMemory[$entityTypeCode] = $this->getData($entityTypeCode);
        }

        return $this->dataInMemory[$entityTypeCode] ?? [];
    }

    /**
     * @param string $entityTypeCode
     * @return array
     */
    public function getData(string $entityTypeCode): array
    {
        return $this->connection->fetchAssoc(
            $this->connection->select()
                ->from(['eas_tb' => $this->connection->getTableName('eav_attribute_set')])
                ->joinLeft(
                    ['eat_tb' => $this->connection->getTableName('eav_entity_type')],
                    'eat_tb.entity_type_id = eas_tb.entity_type_id',
                    null
                )
                ->where('eat_tb.entity_type_code = ?', $entityTypeCode)
        );
    }
}
