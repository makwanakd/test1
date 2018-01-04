<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ironedge\TradePass\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Moreinfo extends Column
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
               $tradePassCollection = $objectManager->create(
                            'Ironedge\TradePass\Model\TradePass'
	                    )->getCollection()
	                    ->addFieldToFilter('customer_id', $item['entity_id']);
				$moreinfo = '';
	            foreach ($tradePassCollection as $value){
					$moreinfo = $value->getMoreinfo();
				}
				
				
				
				if($moreinfo==1)
				{
					$isConfirmed = 'Yes';
				}else{
					$isConfirmed = 'No';
				}

                $item[$this->getData('name')] = $isConfirmed;
            }
        }
        return $dataSource;
    }
}