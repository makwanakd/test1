<?php

namespace Icecube\Extrafields\Model;

use Magento\Customer\Api\GroupManagementInterface;

class Zipcode extends \Magento\Eav\Model\Entity\Attribute\Source\Table
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
    		$resources = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection');
			$connection = $resources->getConnection();
			$themeTable = $resources->getTableName('zipcodes');
			$sql = "SELECT * FROM ".$themeTable;			
			//$connection->query($sql);
			$rows = $connection->fetchAll($sql);			
	        $this->_options = $rows;
        	return $this->_options;
    }
}