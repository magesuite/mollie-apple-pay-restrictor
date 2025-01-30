<?php

declare(strict_types=1);

namespace MageSuite\MollieApplePayRestrictor\Test\Integration\Plugin\Mollie\Payment\Block\Product\View\ApplePay;

class RestrictButtonVisibilityTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     * @magentoDataFixture MageSuite_MollieApplePayRestrictor::Test/_files/product.php
     * @magentoConfigFixture default_store applepay_restrictions/mollie_apple_pay_restrictions/full_visibility_enabled 0
     * @magentoConfigFixture default_store payment/mollie_methods_applepay/active 1
     * @magentoConfigFixture default_store payment/mollie_methods_applepay/enable_buy_now_button 1
     * @magentoConfigFixture default_store payment/mollie_general/type live
     */
    public function testItShowsApplePayButtonOnPdpWhenUrlParameterSet(): void
    {
        $uri = sprintf('catalog/product/view/id/%s?ap_button_show=1', 666);
        $this->dispatch($uri);
        $body = $this->getResponse()->getBody();

        $this->assertStringContainsString('product-page-mollie-apple-pay-button', $body);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     * @magentoDataFixture MageSuite_MollieApplePayRestrictor::Test/_files/product.php
     * @magentoConfigFixture default_store applepay_restrictions/mollie_apple_pay_restrictions/full_visibility_enabled 0
     * @magentoConfigFixture default_store payment/mollie_methods_applepay/active 1
     * @magentoConfigFixture default_store payment/mollie_methods_applepay/enable_buy_now_button 1
     * @magentoConfigFixture default_store payment/mollie_general/type live
     */
    public function testItDoesNotShowApplePayButtonOnPdpWhenUrlParameterNotSet(): void
    {
        $uri = sprintf('catalog/product/view/id/%s', 666);
        $this->dispatch($uri);
        $body = $this->getResponse()->getBody();

        $this->assertStringNotContainsString('product-page-mollie-apple-pay-button', $body);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     * @magentoDataFixture MageSuite_MollieApplePayRestrictor::Test/_files/product.php
     * @magentoConfigFixture default_store applepay_restrictions/mollie_apple_pay_restrictions/full_visibility_enabled 1
     * @magentoConfigFixture default_store payment/mollie_methods_applepay/active 1
     * @magentoConfigFixture default_store payment/mollie_methods_applepay/enable_buy_now_button 1
     * @magentoConfigFixture default_store payment/mollie_general/type live
     */
    public function testItShowsApplePayButtonOnPdpWhenFullyVisibilityEnabled(): void
    {
        $uri = sprintf('catalog/product/view/id/%s', 666);
        $this->dispatch($uri);
        $body = $this->getResponse()->getBody();

        $this->assertStringContainsString('product-page-mollie-apple-pay-button', $body);
    }
}
