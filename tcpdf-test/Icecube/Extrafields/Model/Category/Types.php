<?php

namespace Icecube\Extrafields\Model\Category;

use Magento\Customer\Api\GroupManagementInterface;

class Types extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    protected $_groupManagement;

    protected $_converter;

    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        GroupManagementInterface $groupManagement,
        \Magento\Framework\Convert\DataObject $converter
    ) {
        $this->_groupManagement = $groupManagement;
        $this->_converter = $converter;
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
    }

    public function getAllOptions()
    {
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$preferenceCollection =  $objectManager->create('Icecube\Business\Model\ResourceModel\Preferences\CollectionFactory');
		$collectionPreference = $preferenceCollection->create()->load();
		$this->_options = array(array('value'=>'0', 'label'=>'Make a selection'));
		
		foreach($collectionPreference as $preference){
			if($preference->getStatus()=='1'){
				$this->_options[] = array('value'=>$preference->getId(), 'label'=>$preference->getPreferenceTitle());			
			}			
		}
        return $this->_options;
    }
}