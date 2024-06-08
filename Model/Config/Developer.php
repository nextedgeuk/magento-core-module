<?php
/**
 * Copyright © Soft Commerce Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace NextEdge\Core\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * @inheritDoc
 */
class Developer implements DeveloperInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var string
     */
    private string $entity;

    /**
     * Developer constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param string $entityType
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        string $entityType = 'softcommerce_core'
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->entity = $entityType;
    }

    /**
     * @inheritDoc
     */
    public function isActiveLog(): bool
    {
        return (bool) $this->scopeConfig->getValue($this->entity . self::XML_PATH_IS_ACTIVE_LOG);
    }

    /**
     * @inheritDoc
     */
    public function shouldLogPrintToArray(): bool
    {
        return (bool) $this->scopeConfig->getValue($this->entity . self::XML_PATH_SHOULD_LOG_PRINT_TO_ARRAY);
    }

    /**
     * @inheritDoc
     */
    public function getLogLevel(): array
    {
        return explode(
            ',',
            $this->scopeConfig->getValue($this->entity . self::XML_PATH_LOG_LEVEL) ?: ''
        ) ?: [];
    }

    /**
     * @inheritDoc
     */
    public function isActiveEmailLog(): bool
    {
        return (bool) $this->scopeConfig->getValue($this->entity . self::XML_PATH_IS_ACTIVE_MAIL_LOG);
    }

    /**
     * @inheritDoc
     */
    public function getMailLogRecipient(): ?string
    {
        return $this->scopeConfig->getValue($this->entity . self::XML_PATH_MAIL_LOG_RECIPIENT);
    }

    /**
     * @inheritDoc
     */
    public function getMailLogEmailIdentity(): ?string
    {
        return $this->scopeConfig->getValue($this->entity . self::XML_PATH_MAIL_LOG_EMAIL_IDENTITY);
    }

    /**
     * @inheritDoc
     */
    public function getMailLogEmailTemplate(): ?string
    {
        return $this->scopeConfig->getValue($this->entity . self::XML_PATH_MAIL_LOG_EMAIL_TEMPLATE);
    }
}
