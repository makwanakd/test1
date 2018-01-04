<?php

namespace Ironedge\TradePass\Block\Adminhtml\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;

Class TradePass extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface {

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
    \Magento\Backend\Block\Template\Context $context, 
            \Magento\Framework\Registry $registry, 
            \Magento\Framework\Data\FormFactory $formFactory, 
            \Magento\Framework\ObjectManagerInterface $objectManager,
            array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_objectManager = $objectManager;        
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return string|null
     */
    public function getCustomerId() {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel() {
        return __("Trade Pass");
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle() {
        return __("TradePass  Information");
    }

    /**
     * @return bool
     */
    public function canShowTab() {
        if ($this->getCustomerId()) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isHidden() {
        if ($this->getCustomerId()) {
            return false;
        }
        return true;
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass() {
        return '';
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl() {
        //replace the tab with the url you want
        return '';
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded() {
        return false;
    }

    public function initForm() {
        $customerId = $this->getRequest()->getParam('id');
        if (!$this->canShowTab()) {
            return $this;
        }
        $data = array('company'=>'', 'address'=>'', 'industry'=>'');
        $collection = $this->_objectManager
                ->create('Ironedge\TradePass\Model\TradePass')->getCollection()
                ->addFieldToFilter('customer_id', $customerId);
        foreach ($collection as $record) {
            $data = $record->getData();
        }
        

        /*         * @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('tradepass_');
        $fieldset = $form->addFieldset(
                'base_fieldset', ['legend' => __('TradePass Information')]
        );

        $fieldset->addField(
                'company', 'text', [
            'name' => 'company',
            'data-form-part' => $this->getData('target_form'),
            'label' => __('Company'),
            'title' => __('Company'),
            'value' => $data['company'],
                ]
        );
        $fieldset->addField(
                'address', 'textarea', [
            'name' => 'address',
            'data-form-part' => $this->getData('target_form'),
            'label' => __('Address'),
            'title' => __('Address'),
            'value' => $data['address']
                ]
        );

        $fieldset->addField(
                'industry', 'select', [
            'name' => 'industry',
            'data-form-part' => $this->getData('target_form'),
            'label' => __('Industry'),
            'title' => __('Industry'),
            'values' => array(
                array(
                    'value' => 1,
                    'label' => __('ADF'),
                ),
                array(
                    'value' => 2,
                    'label' => __('Commercial Gym'),
                ),
                array(
                    'value' => 3,
                    'label' => __('Cross Fit'),
                ),
                array(
                    'value' => 4,
                    'label' => __('Emergency Services'),
                ),
                array(
                    'value' => 5,
                    'label' => __('High Emergency Gym'),
                ),
                array(
                    'value' => 6,
                    'label' => __('Medical Practice/Physical Therapy'),
                ),
                array(
                    'value' => 7,
                    'label' => __('Personal Training'),
                ),
                array(
                    'value' => 8,
                    'label' => __('Professional Sports Organization'),
                ),
                array(
                    'value' => 9,
                    'label' => __('School'),
                ),
                array(
                    'value' => 10,
                    'label' => __('Not Listed'),
                )
            ),
            'value' => $data['industry']
                ]
        );
        $this->setForm($form);

        return $this;
    }

    /**
     * @return string
     */
    protected function _toHtml() {
        if ($this->canShowTab()) {
            $this->initForm();

            return parent::_toHtml();
        } else {
            return '';
        }
    }

}
