<?php

namespace MageSuite\MollieApplePayRestrictor\Test\Integration\Observer;

class AddMollieApplePayVisibilityClassTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Quote\Api\CartManagementInterface
     */
    protected $cartManagement;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Checkout\Model\ShippingInformationManagement
     */
    protected $shippingInformationManagement;

    /**
     * @var \MageSuite\MollieApplePayRestrictor\Observer\AddMollieApplePayVisibilityClass
     */
    protected $addMollieApplePayVisibilityClass;

    public function setUp(): void
    {
        parent::setUp();
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
     * @magentoDataFixture loadProduct
     * @magentoConfigFixture default_store applepay_restrictions/mollie_apple_pay_restrictions/full_visibility_enabled 0
     */
    public function testItAddsApplePayHiddenClassToPageBodyForGuestCustomerWhenUrlParameterIsNotSet()
    {
        $customer = $this->createGuestCustomer('user@sample.com');
        $quote = $this->createQuote($customer);

        /** @var \Magento\Checkout\Model\Session $checkoutSession */
        $checkoutSession = $this->_objectManager->get(\Magento\Checkout\Model\Session::class);
        $checkoutSession->clearQuote();
        $checkoutSession->setQuoteId($quote->getId());

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
     * @magentoDataFixture loadProduct
     * @magentoDataFixture loadCustomer
     * @magentoConfigFixture default_store applepay_restrictions/mollie_apple_pay_restrictions/full_visibility_enabled 0
     */
    public function testItAddsApplePayHiddenClassToPageBodyForLoggedInCustomerWhenUrlParameterIsNotSet()
    {
        $customer = $this->customerRepository->get('user24@example.com');
        $quote = $this->createQuote($customer);

        /** @var \Magento\Checkout\Model\Session $checkoutSession */
        $checkoutSession = $this->_objectManager->get(\Magento\Checkout\Model\Session::class);
        $checkoutSession->clearQuote();
        $checkoutSession->setQuoteId($quote->getId());

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
     * @magentoDataFixture loadProduct
     * @magentoDataFixture loadCustomer
     * @magentoConfigFixture default_store applepay_restrictions/mollie_apple_pay_restrictions/full_visibility_enabled 0
     */
    public function testItDoesNotAddApplePayHiddenClassToPageBodyWhenUrlParameterIsSet()
    {
        $customer = $this->customerRepository->get('user24@example.com');
        $quote = $this->createQuote($customer);

        /** @var \Magento\Checkout\Model\Session $checkoutSession */
        $checkoutSession = $this->_objectManager->get(\Magento\Checkout\Model\Session::class);
        $checkoutSession->clearQuote();
        $checkoutSession->setQuoteId($quote->getId());

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
     * @magentoDataFixture loadProduct
     * @magentoDataFixture loadCustomer
     * @magentoConfigFixture default_store applepay_restrictions/mollie_apple_pay_restrictions/full_visibility_enabled 1
     */
    public function testItDoesNotAddApplePayHiddenClassToPageBodyWhenFullVisibilityIsEnabled()
    {
        $customer = $this->customerRepository->get('user24@example.com');
        $quote = $this->createQuote($customer);

        /** @var \Magento\Checkout\Model\Session $checkoutSession */
        $checkoutSession = $this->_objectManager->get(\Magento\Checkout\Model\Session::class);
        $checkoutSession->clearQuote();
        $checkoutSession->setQuoteId($quote->getId());

        $this->getRequest()
            ->setMethod(\Magento\Framework\App\Request\Http::METHOD_GET);
        $this->dispatch('checkout/index/index');

        $body = $this->getResponse()->getBody();

        $this->assertStringNotContainsString($this->addMollieApplePayVisibilityClass->getApplePayVisibilityBodyClass(), $body);
    }

    public function createQuote($customer): \Magento\Quote\Model\Quote
    {
        $address = $this->getAddressData();
        $guestCustomer = !$customer->getId() ? true : false;

        /** @var Magento\Catalog\Model\Product $product */
        $product = $this->productRepository->get('simple-666');
        $store = $this->storeManager->getStore();

        if (!$guestCustomer) {
            $this->customerSession->setCustomerDataAsLoggedIn($customer);
        }

        $cartId = $this->cartManagement->createEmptyCart();

        /** @var Magento\Quote\Model\Quote $quote */
        $quote = $this->cartRepository->get($cartId);

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
            ->setReservedOrderId(3736)
            ->setStore($store)
            ->setCurrency()
            ->setCustomerEmail($customer->getEmail())
            ->setCustomerIsGuest($guestCustomer)
            ->addItem($quoteItem)
            ->setExtensionAttributes($extensionAttributes);

        if (!$guestCustomer) {
            $quote->setCustomerId($customer->getId());
        }

        $quote->setShippingAddress($shippingAddress);
        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)
            ->collectShippingRates()
            ->setShippingMethod('flatrate_flatrate');

        $billingAddress = $this->createBillingAddress($address);
        $quote->setBillingAddress($billingAddress);

        /** @var Magento\Quote\Model\Quote\Payment $payment */
        $payment = $this->_objectManager->create(\Magento\Quote\Api\Data\PaymentInterface::class, ['data' => ['is_available' => true]]);
        $quote->setPayment($payment);
        $quote->setInventoryProcessed(false);
        $quote->getPayment()->setMethod('checkmo');
        $quote->collectTotals();
        $quote->save();

        return $quote;
    }

    /**
     * @param string $countryCode
     * @param string $postcode
     * @return array
     */
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

    /**
     * @param string $email
     * @return \Magento\Customer\Model\Customer
     */
    protected function createGuestCustomer(string $email): \Magento\Customer\Model\Customer
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->_objectManager->create(\Magento\Customer\Model\Customer::class);
        $customer->setEmail($email);

        return $customer;
    }

    /**
     * @param array $shippingAddressData
     * @return \Magento\Quote\Api\Data\AddressInterface
     */
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

    /**
     * @param array $billingAddressData
     * @return \Magento\Quote\Api\Data\AddressInterface
     */
    protected function createBillingAddress(array $billingAddressData): \Magento\Quote\Api\Data\AddressInterface
    {
        /** @var $billingAddress \Magento\Quote\Api\Data\AddressInterface */
        $billingAddress = $this->_objectManager->create(\Magento\Quote\Api\Data\AddressInterface::class, ['data' => $billingAddressData]);
        $billingAddress->setAddressType('billing');

        return $billingAddress;
    }

    public static function loadProduct()
    {
        include __DIR__ . '/../../_files/product.php';
    }

    public static function loadCustomer()
    {
        include __DIR__ . '/../../_files/customer.php';
    }
}
