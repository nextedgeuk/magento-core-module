<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace NextEdge\Core\Model\Utils;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * Interface GetEntityMetadataInterface
 * used to obtain entity metadata info.
 */
interface GetEntityMetadataInterface
{
    /**
     * @param string $entityType
     * @return string
     */
    public function getLinkField(string $entityType = ProductInterface::class): string;

    /**
     * @param string $entityType
     * @return string
     * @throws \Exception
     */
    public function getIdentifierField(string $entityType = ProductInterface::class): string;

    /**
     * @param string $entityType
     * @return int
     * @throws \Exception
     */
    public function generateIdentifier(string $entityType = ProductInterface::class): int;
}
