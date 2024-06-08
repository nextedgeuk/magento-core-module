<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace NextEdge\Core\Model\Utils;

use Magento\Catalog\Model\Product;

/**
 * Interface GetEntityTypeIdInterface
 * used to obtain entity type ID.
 * @deprecated in favour
 * @see \NextEdge\Core\Model\Eav\GetEntityTypeIdInterface
 */
interface GetEntityTypeIdInterface
{
    /**
     * @param string $entityTypeCode
     * @return int
     */
    public function execute(string $entityTypeCode = Product::ENTITY): int;
}
