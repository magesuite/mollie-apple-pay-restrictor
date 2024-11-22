<?php

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Directory\Model\CountryFactory $countryFactory */
$countryFactory = $objectManager->get(\Magento\Directory\Model\CountryFactory::class);
/** @var \Magento\Framework\App\Config\MutableScopeConfigInterface $mutableConfig */
$mutableConfig = $objectManager->get(\Magento\Framework\App\Config\MutableScopeConfigInterface::class);

$nlCountry = $countryFactory->create()->loadByCode('NL');

$mutableConfig->setValue('payment/mollie_general/apikey_test', $nlCountry->getId(), \Magento\Store\Model\ScopeInterface::SCOPE_STORE, 'default');
