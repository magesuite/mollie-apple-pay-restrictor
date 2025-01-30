<?php

declare(strict_types=1);

namespace MageSuite\MollieApplePayRestrictor\Test\Integration\Observer;

class AddMollieApplePayVisibilityClassTest extends \Magento\TestFramework\TestCase\AbstractController
{
    protected ?\Magento\Framework\App\Config\Storage\WriterInterface $configWriter = null;
    protected ?\Magento\Store\Model\StoreManagerInterface $storeManager = null;
    protected ?\Magento\Quote\Api\CartManagementInterface $cartManagement = null;
    protected ?\Magento\Quote\Api\CartRepositoryInterface $cartRepository = null;
    protected ?\Magento\Customer\Api\CustomerRepositoryInterface $customerRepository = null;
    protected ?\Magento\Customer\Model\Session $customerSession = null;
    protected ?\Magento\Catalog\Api\ProductRepositoryInterface $productRepository = null;
    protected ?\Magento\Checkout\Model\ShippingInformationManagement $shippingInformationManagement = null;
    protected ?\MageSuite\MollieApplePayRestrictor\Observer\AddMollieApplePayVisibilityClass $addMollieApplePayVisibilityClass = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->configWriter = $this->_objectManager->create(\Magento\Framework\App\Config\Storage\WriterInterface::class);
        $this->configWriter->save("payment/mollie_general/apikey_test", "test_123456789012345678901234567890");

        $this->storeManager = $this->_objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);
        $this->cartManagement = $this->_objectManager->get(\Magento\Quote\Api\CartManagementInterface::class);
        $this->cartRepository = $this->_objectManager->get(\Magento\Quote\Api\CartRepositoryInterface::class);
        $this->customerRepository = $this->_objectManager->get(\Magento\Customer\Api\CustomerRepositoryInterface::class);
        $this->customerSession = $this->_objectManager->create(\Magento\Customer\Model\Session::class);
        $this->productRepository = $this->_objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $this->addMollieApplePayVisibilityClass = $this->_objectManager->create(\MageSuite\MollieApplePayRestrictor\Observer\AddMollieApplePayVisibilityClass::class);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     * @magentoDataFixture MageSuite_MollieApplePayRestrictor::Test/_files/product.php
     * @magentoConfigFixture default_store applepay_restrictions/mollie_apple_pay_restrictions/full_visibility_enabled 0
     * @magentoConfigFixture default_store payment/mollie_general/apikey_test test_123456789012345678901234567890
     * @magentoConfigFixture default_store payment/mollie_general/type test
     */
    public function testItAddsApplePayHiddenClassToPageBodyForGuestCustomerWhenUrlParameterIsNotSet(): void
    {
        $customer = $this->createGuestCustomer('user@sample.com');
        $quote = $this->createQuote($customer);

        /** @var \Magento\Checkout\Model\Session $checkoutSession */
        $checkoutSession = $this->_objectManager->get(\Magento\Checkout\Model\Session::class);
        $checkoutSession->clearQuote();
        $checkoutSession->setQuoteId((int) $quote->getId());

        $this->getRequest()
            ->setMethod(\Magento\Framework\App\Request\Http::METHOD_GET);
        $this->dispatch('checkout/index/index');

        $body = $this->getResponse()->getBody();

        $this->assertStringContainsString($this->addMollieApplePayVisibilityClass->getApplePayVisibilityBodyClass(), $body);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     * @magentoDataFixture MageSuite_MollieApplePayRestrictor::Test/_files/product.php
     * @magentoDataFixture MageSuite_MollieApplePayRestrictor::Test/_files/customer.php
     * @magentoConfigFixture default_store applepay_restrictions/mollie_apple_pay_restrictions/full_visibility_enabled 0
     * @magentoConfigFixture default_store payment/mollie_general/apikey_test test_123456789012345678901234567890
     * @magentoConfigFixture default_store payment/mollie_general/type test
     */
    public function testItAddsApplePayHiddenClassToPageBodyForLoggedInCustomerWhenUrlParameterIsNotSet(): void
    {
        $customer = $this->customerRepository->get('user24@example.com');
        $quote = $this->createQuote($customer);

        /** @var \Magento\Checkout\Model\Session $checkoutSession */
        $checkoutSession = $this->_objectManager->get(\Magento\Checkout\Model\Session::class);
        $checkoutSession->clearQuote();
        $checkoutSession->setQuoteId((int) $quote->getId());

        $this->getRequest()
            ->setMethod(\Magento\Framework\App\Request\Http::METHOD_GET);
        $this->dispatch('checkout/index/index');

        $body = $this->getResponse()->getBody();

        $this->assertStringContainsString($this->addMollieApplePayVisibilityClass->getApplePayVisibilityBodyClass(), $body);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     * @magentoDataFixture MageSuite_MollieApplePayRestrictor::Test/_files/product.php
     * @magentoDataFixture MageSuite_MollieApplePayRestrictor::Test/_files/customer.php
     * @magentoConfigFixture default_store applepay_restrictions/mollie_apple_pay_restrictions/full_visibility_enabled 0
     * @magentoConfigFixture default_store payment/mollie_general/apikey_test test_123456789012345678901234567890
     * @magentoConfigFixture default_store payment/mollie_general/type test
     */
    public function testItDoesNotAddApplePayHiddenClassToPageBodyWhenUrlParameterIsSet(): void
    {
        $customer = $this->customerRepository->get('user24@example.com');
        $quote = $this->createQuote($customer);

        /** @var \Magento\Checkout\Model\Session $checkoutSession */
        $checkoutSession = $this->_objectManager->get(\Magento\Checkout\Model\Session::class);
        $checkoutSession->clearQuote();
        $checkoutSession->setQuoteId((int) $quote->getId());

        $this->getRequest()
            ->setMethod(\Magento\Framework\App\Request\Http::METHOD_GET)
            ->setParams(['ap_button_show' => 1]);
        $this->dispatch('checkout/index/index');

        $body = $this->getResponse()->getBody();

        $this->assertStringNotContainsString($this->addMollieApplePayVisibilityClass->getApplePayVisibilityBodyClass(), $body);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoAppArea frontend
     * @magentoDataFixture MageSuite_MollieApplePayRestrictor::Test/_files/product.php
     * @magentoDataFixture MageSuite_MollieApplePayRestrictor::Test/_files/customer.php
     * @magentoConfigFixture default_store applepay_restrictions/mollie_apple_pay_restrictions/full_visibility_enabled 1
     * @magentoConfigFixture default_store payment/mollie_general/apikey_test test_123456789012345678901234567890
     * @magentoConfigFixture default_store payment/mollie_general/type test
     */
    public function testItDoesNotAddApplePayHiddenClassToPageBodyWhenFullVisibilityIsEnabled(): void
    {
        $customer = $this->customerRepository->get('user24@example.com');
        $quote = $this->createQuote($customer);

        /** @var \Magento\Checkout\Model\Session $checkoutSession */
        $checkoutSession = $this->_objectManager->get(\Magento\Checkout\Model\Session::class);
        $checkoutSession->clearQuote();
        $checkoutSession->setQuoteId((int) $quote->getId());

        $this->getRequest()
            ->setMethod(\Magento\Framework\App\Request\Http::METHOD_GET);
        $this->dispatch('checkout/index/index');

        $body = $this->getResponse()->getBody();

        $this->assertStringNotContainsString($this->addMollieApplePayVisibilityClass->getApplePayVisibilityBodyClass(), $body);
    }

    public function createQuote(\Magento\Customer\Model\Data\Customer|\Magento\Customer\Model\Customer $customer): \Magento\Quote\Model\Quote
    {
        $address = $this->getAddressData();
        $guestCustomer = !$customer->getId() ? true : false;

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productRepository->get('simple-666');
        $store = $this->storeManager->getStore();

        if (!$guestCustomer) {
            $this->customerSession->setCustomerDataAsLoggedIn($customer);
        }

        $cartId = $this->cartManagement->createEmptyCart();

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->cartRepository->get((int) $cartId);

        $cartItemFactory = $this->_objectManager->create(\Magento\Quote\Api\Data\CartItemInterfaceFactory::class);
        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
        $quoteItem = $cartItemFactory->create();
        $quoteItem->setProduct($product);
        $quoteItem->setQty(1);

        $shippingAddress = $this->createShippingAddress($address);

        $shipping = $this->_objectManager->get(\Magento\Quote\Api\Data\ShippingInterface::class);
        $shipping->setAddress($shippingAddress);
        $shippingAssignment = $this->_objectManager->get(\Magento\Quote\Api\Data\ShippingAssignmentInterface::class);
        $shippingAssignment->setItems([]);
        $shippingAssignment->setShipping($shipping);
        $extensionAttributes = $this->_objectManager->get(\Magento\Quote\Api\Data\CartExtension::class);
        $extensionAttributes->setShippingAssignments([$shippingAssignment]);
        $quote
            ->setReservedOrderId('3736')
            ->setStore($store)
            ->setCurrency()
            ->setCustomerEmail($customer->getEmail())
            ->setCustomerIsGuest($guestCustomer)
            ->addItem($quoteItem)
            ->setExtensionAttributes($extensionAttributes);

        if (!$guestCustomer) {
            $quote->setCustomerId((int) $customer->getId());
        }

        $quote->setShippingAddress($shippingAddress);
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod('flatrate_flatrate');

        $billingAddress = $this->createBillingAddress($address);
        $quote->setBillingAddress($billingAddress);

        /** @var \Magento\Quote\Model\Quote\Payment $payment */
        $payment = $this->_objectManager->create(\Magento\Quote\Api\Data\PaymentInterface::class, ['data' => ['is_available' => true]]);
        $quote->setPayment($payment);
        $quote->setInventoryProcessed(false);
        $quote->getPayment()->setMethod('checkmo');
        $quote->collectTotals();
        $quote->save();

        return $quote;
    }

    public function getAddressData(): array
    {
        return [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'street' => 'street',
            'city' => 'AM',
            'country_id' => 'US',
            'region' => 'RR',
            'postcode' => '12345',
            'telephone' => '123456789',
            'save_in_address_book' => 0
        ];
    }

    protected function createGuestCustomer(string $email): \Magento\Customer\Model\Customer
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->_objectManager->create(\Magento\Customer\Model\Customer::class);
        $customer->setEmail($email);

        return $customer;
    }

    protected function createShippingAddress(array $shippingAddressData): \Magento\Quote\Api\Data\AddressInterface
    {
        /** @var $shipppingAddress \Magento\Quote\Api\Data\AddressInterface */
        $shippingAddress = $this->_objectManager->create(\Magento\Quote\Api\Data\AddressInterface::class, ['data' => $shippingAddressData]);
        $shippingAddress->setAddressType('shipping');

        /** @var Magento\Quote\Model\Quote\Address\Rate $shippingRate */
        $shippingRate = $this->_objectManager->get(\Magento\Quote\Model\Quote\Address\Rate::class);
        $shippingRate->setCode('flatrate_flatrate')->getPrice(0);
        $shippingAddress->addShippingRate($shippingRate);

        return $shippingAddress;
    }

    protected function createBillingAddress(array $billingAddressData): \Magento\Quote\Api\Data\AddressInterface
    {
        /** @var $billingAddress \Magento\Quote\Api\Data\AddressInterface */
        $billingAddress = $this->_objectManager->create(\Magento\Quote\Api\Data\AddressInterface::class, ['data' => $billingAddressData]);
        $billingAddress->setAddressType('billing');

        return $billingAddress;
    }
}
