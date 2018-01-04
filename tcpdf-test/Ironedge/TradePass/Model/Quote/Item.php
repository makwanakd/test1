<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ironedge\TradePass\Model\Quote;

class Item extends \Magento\Quote\Model\Quote\Item
{
	public function setProduct($product)
    {
        if ($this->getQuote()) {
            $product->setStoreId($this->getQuote()->getStoreId());
            $product->setCustomerGroupId($this->getQuote()->getCustomerGroupId());
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$tmpproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($product->getId());
		$additionalWeight = 0;
        if($tmpproduct->getChargeableWeight()!=NULL && $tmpproduct->getChargeableWeight()!=''){
			$additionalWeight = $tmpproduct->getChargeableWeight();
		}
        $this->setData('product', $product)
            ->setProductId($product->getId())
            ->setProductType($product->getTypeId())
            ->setSku($this->getProduct()->getSku())
            ->setName($product->getName())
            ->setWeight($this->getProduct()->getWeight() + $additionalWeight)
            ->setTaxClassId($product->getTaxClassId())
            ->setBaseCost($product->getCost());

        $stockItem = $this->stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
        $this->setIsQtyDecimal($stockItem->getIsQtyDecimal());

        $this->_eventManager->dispatch(
            'sales_quote_item_set_product',
            ['product' => $product, 'quote_item' => $this]
        );

        return $this;
    }
}
