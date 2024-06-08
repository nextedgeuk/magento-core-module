<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace NextEdge\Core\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use NextEdge\Core\Framework\DataMap\StatusToFaMappingInterface;
use NextEdge\Core\Framework\DataMap\StatusToLabelMappingInterface;

/**
 * @inheritDoc
 */
class StatusRenderer extends Column
{
    /**
     * @var StatusToFaMappingInterface
     */
    private StatusToFaMappingInterface $statusToFaMapping;

    /**
     * @var StatusToLabelMappingInterface
     */
    private StatusToLabelMappingInterface $statusToLabelMapping;

    /**
     * @param StatusToFaMappingInterface $statusToFaMapping
     * @param StatusToLabelMappingInterface $statusToLabelMapping
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        StatusToFaMappingInterface $statusToFaMapping,
        StatusToLabelMappingInterface $statusToLabelMapping,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->statusToFaMapping = $statusToFaMapping;
        $this->statusToLabelMapping = $statusToLabelMapping;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @inheritDoc
     */
    public function prepareDataSource(array $dataSource): array
    {
        $componentIndex = $this->getData('name');
        foreach ($dataSource['data']['items'] ?? [] as $index => $item) {
            if (!isset($item[$componentIndex])) {
                continue;
            }

            $value = $item[$componentIndex];
            $dataSource['data']['items'][$index][$componentIndex] = $this->statusToLabelMapping->execute($value);
            $dataSource['data']['items'][$index]['cell_status'] = $value;
            $dataSource['data']['items'][$index]['cell_attribute'] = $this->statusToFaMapping->execute($value);
        }

        return $dataSource;
    }
}
