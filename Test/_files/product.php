<?php

/** @var \Magento\TestFramework\ObjectManager $objectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagement */
$categoryLinkManagement = $objectManager->create(\Magento\Catalog\Api\CategoryLinkManagementInterface::class);

/** @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryFactory */
$productRepositoryFactory = $objectManager->create(\Magento\Catalog\Api\ProductRepositoryInterface::class);

/** @var $product \Magento\Catalog\Model\Product */
$product = $objectManager->create(\Magento\Catalog\Model\Product::class);
$product->isObjectNew(true);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(666)
    ->setAttributeSetId(4)
    ->setWebsiteIds([1])
    ->setName('Simple Product 666')
    ->setSku('simple-666')
    ->setPrice(10)
    ->setTaxClassId(0)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setStockData(
        [
            'use_config_manage_stock' => 1,
            'qty' => isset($data['qty']) ? $data['qty'] : 100,
            'is_qty_decimal' => 0,
            'is_in_stock' => 1,
        ]
    )->setCanSaveCustomOptions(false)
    ->setQty(isset($data['qty']) ? $data['qty'] : 100)
    ->setHasOptions(false);

$productRepositoryFactory->save($product);

