<?php

namespace MageSuite\MollieApplePayRestrictor\Plugin\Mollie\Payment\Block\Product\View\ApplePay;

class RestrictButtonVisibility
{
    protected \Magento\Framework\App\RequestInterface $request;

    protected \MageSuite\MollieApplePayRestrictor\Helper\Configuration $configuration;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \MageSuite\MollieApplePayRestrictor\Helper\Configuration $configuration
    )
    {
        $this->request = $request;
        $this->configuration = $configuration;
    }

    public function aroundIsEnabled(\Mollie\Payment\Block\Product\View\ApplePay $subject, callable $proceed)
    {
        if ($this->configuration->isButtonFullyVisible()) {
            return $proceed();
        }

        $urlParameterKey = $this->configuration->getParameterKey();
        if ($urlParameterKey && !empty($this->request->getParam($urlParameterKey))) {
            return $proceed();
        }

        return false;
    }
}
