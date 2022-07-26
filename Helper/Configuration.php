<?php

namespace MageSuite\MollieApplePayRestrictor\Helper;

class Configuration extends \Magento\Framework\App\Helper\AbstractHelper
{
    public const APPLE_PAY_FULLY_VISIBILITY_ENABLED_XML_PATH = 'applepay_restrictions/mollie_apple_pay_restrictions/full_visibility_enabled';
    public const APPLE_PAY_URL_PARAMETER_KEY_XML_PATH = 'applepay_restrictions/mollie_apple_pay_restrictions/url_parameter_key';

    public function isButtonFullyVisible(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::APPLE_PAY_FULLY_VISIBILITY_ENABLED_XML_PATH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getParameterKey(): ?string
    {
        return $this->scopeConfig->getValue(self::APPLE_PAY_URL_PARAMETER_KEY_XML_PATH, \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }
}
