<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace NextEdge\Core\Framework\MessageStorage;

use NextEdge\Core\Model\Source\StatusInterface;

/**
 * Interface StatusPredictionInterface used to
 * predict output status.
 */
interface StatusPredictionInterface
{
    /**
     * @param array $data
     * @param string $fallback
     * @return string
     */
    public function execute(array $data, string $fallback = StatusInterface::SUCCESS): string;
}
