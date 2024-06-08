<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace NextEdge\Core\Ui\Component\Listing\Columns;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * @inheritDoc
 */
class JsonContentRenderer extends Column
{
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @param SerializerInterface $serializer
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        SerializerInterface $serializer,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->serializer = $serializer;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @inheritDoc
     */
    public function prepareDataSource(array $dataSource): array
    {
        $componentIndex = $this->getData('name');
        foreach ($dataSource['data']['items'] ?? [] as $index => $item) {
            if (!$data = $item[$componentIndex] ?? null) {
                continue;
            }

            try {
                $data = $this->serializer->unserialize($data);
            } catch (\InvalidArgumentException $e) {
                $data = [$data];
            }

            $dataSource['data']['items'][$index][$componentIndex] = implode(', ', $data);
        }

        return $dataSource;
    }
}
