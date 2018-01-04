<?php
/**
 * Copyright � 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ironedge\TradePass\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class IsTradepassConfirmed extends Column
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
                $customer = $objectManager->create('Magento\Customer\Model\Customer')->load($item['entity_id']);
				if($customer->getIsTradepassConfirmed())
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
	/*public function toOptionArray()
    {
        $tradepassInfo = array();
		$tradepassInfo[] = array('value'=>0,'label'=>'No');
		$tradepassInfo[] = array('value'=>1,'label'=>'Yes');
        
		return $tradepassInfo;
    }*/
}