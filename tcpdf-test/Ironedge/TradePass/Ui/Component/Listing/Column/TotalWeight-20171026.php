<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ironedge\TradePass\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class TotalWeight extends Column
{
    /**
     * 
     * @param ContextInterface   $context           
     * @param UiComponentFactory $uiComponentFactory   
     * @param array              $components        
     * @param array              $data              
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) 
            {

                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $product = $objectManager->create('Magento\Catalog\Model\Product')->load($item['entity_id']);
				$totalWeight = '';
				if($product->getTypeId()=='bundle')
				{
					$collection = $product->getTypeInstance(true)->getSelectionsCollection($product->getTypeInstance(true)->getOptionsIds($product), $product);

		         $itemIds = array();
				$totalWeight = 0;
		         foreach ($collection as $items) {
				 	$simpleProduct = $objectManager->create('Magento\Catalog\Model\Product')->load($items->getId());
		            //$itemIds[] = $items->getId();
					$totalWeight += $simpleProduct->getWeight();
		         }
		         //var_dump($itemIds);
					
					//$totalWeight = $itemIds;
				}else{
					$totalWeight = $product->getWeight();
				}

                $item[$this->getData('name')] = $totalWeight;
            }
        }
        return $dataSource;
    }
	/*public function toOptionArray()
    {
        $tradepassInfo = array();
		$tradepassInfo[] = array('value'=>0,'label'=>'No');
		$tradepassInfo[] = array('value'=>1,'label'=>'Yes');
        
		return $tradepassInfo;
    }*/
}