<?php

namespace Icecube\Extrafields\Model;

use Magento\Customer\Api\GroupManagementInterface;

class Boxesfor extends \Magento\Eav\Model\Entity\Attribute\Source\Table
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
        $this->_options = array(array('value'=>'0', 'label'=>'Make a selection'),array('value'=>'1', 'label'=>'Moving'),array('value'=>'2', 'label'=>'Returns'),array('value'=>'3', 'label'=>'Both'));
        return $this->_options;
    }
}