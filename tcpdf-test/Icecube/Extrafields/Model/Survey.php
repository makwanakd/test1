<?php

namespace Icecube\Extrafields\Model;

use Magento\Customer\Api\GroupManagementInterface;

class Survey extends \Magento\Eav\Model\Entity\Attribute\Source\Table
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
        $this->_options = array(array('value'=>'0', 'label'=>'Make a selection'),array('value'=>'1', 'label'=>'Online'),array('value'=>'2', 'label'=>'Social Media'),array('value'=>'3', 'label'=>'Email Newsletter'),array('value'=>'4', 'label'=>'Search Engine'),array('value'=>'5', 'label'=>'Friend/Family'),array('value'=>'6', 'label'=>'Referral'),array('value'=>'7', 'label'=>'Other'));
        return $this->_options;
    }
}