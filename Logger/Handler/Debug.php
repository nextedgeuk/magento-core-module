<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace NextEdge\Core\Logger\Handler;

use Magento\Framework\Logger\Handler\Debug as DebugHandler;

/**
 * @inheritDoc
 */
class Debug extends DebugHandler
{
    /**
     * @inheritDoc
     */
    protected $fileName = '/var/log/softcommerce/core.log';
}
