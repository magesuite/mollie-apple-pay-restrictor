<?php

namespace MageSuite\MollieApplePayRestrictor\Observer;

class AddMollieApplePayVisibilityClass implements \Magento\Framework\Event\ObserverInterface
{
    public const CHECKOUT_INDEX_ACTION_NAME = 'checkout_index_index';
    public const APPLE_PAY_VISIBILITY_BODY_CLASS_SUFIX = 'hidden';

    protected \Magento\Framework\View\Page\Config $pageConfig;

    protected \Magento\Framework\App\RequestInterface $request;

    protected \MageSuite\MollieApplePayRestrictor\Helper\Configuration $configuration;

    public function __construct(
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Framework\App\RequestInterface $request,
        \MageSuite\MollieApplePayRestrictor\Helper\Configuration $configuration
    ) {
        $this->pageConfig = $pageConfig;
        $this->request = $request;
        $this->configuration = $configuration;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $observer->getEvent()->getLayout();
        $action = $observer->getData('full_action_name');

        if ($action !== self::CHECKOUT_INDEX_ACTION_NAME) {
            return $this;
        }

        if ($this->configuration->isButtonFullyVisible()) {
            return $this;
        }

        $urlParameterKey = $this->configuration->getParameterKey();
        if (!$urlParameterKey || empty($this->request->getParam($urlParameterKey))) {
            $this->pageConfig->addBodyClass($this->getApplePayVisibilityBodyClass());
        }

        return $this;
    }

    public function getApplePayVisibilityBodyClass(): string
    {
        return sprintf('%s_%s', \Mollie\Payment\Model\Methods\ApplePay::CODE, self::APPLE_PAY_VISIBILITY_BODY_CLASS_SUFIX);
    }
}
