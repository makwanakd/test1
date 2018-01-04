<?php

namespace Ironedge\TradePass\Model;

class Tradepasslabel implements \Magento\Framework\Data\OptionSourceInterface
{
	public function toOptionArray()
    {
        $tradepassInfo = array();
		$tradepassInfo[] = array('value'=>0,'label'=>'No');
		$tradepassInfo[] = array('value'=>1,'label'=>'Yes');        
		return $tradepassInfo;
    }
}