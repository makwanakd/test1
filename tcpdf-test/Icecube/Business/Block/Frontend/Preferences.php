<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Icecube\Business\Block\Frontend;

/**
 * Order item render block
 */
class Preferences extends \Magento\Framework\View\Element\Template
{
	public function collectionFactory()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$preferenceCollection =  $objectManager->create('Icecube\Business\Model\ResourceModel\Preferences\CollectionFactory');
		return $collectionPackage = $preferenceCollection->create()->load();
	}
}
?>