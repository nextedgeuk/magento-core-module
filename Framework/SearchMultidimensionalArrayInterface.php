<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace NextEdge\Core\Framework;

/**
 * Interface SearchMultidimensionalArrayInterface
 * used to search data within multi-dimensional array
 */
interface SearchMultidimensionalArrayInterface
{
    /**
     * @param int|string|mixed $needle
     * @param array $haystack
     * @param string $columnName
     * @param null $columnId
     * @return false|int|string
     */
    public function execute(mixed $needle, array $haystack, string $columnName, $columnId = null): bool|int|string;
}
