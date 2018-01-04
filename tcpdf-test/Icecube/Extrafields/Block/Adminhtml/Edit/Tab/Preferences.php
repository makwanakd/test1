<?php

namespace Icecube\Extrafields\Block\Adminhtml\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

Class Preferences extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface {

    protected $uploaderFactory;
    protected $allowedExtensions = ['csv', 'pdf', 'jpg', 'gif', 'xls', 'docx','png'];
    protected $fileId = 'file';

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
    \Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Framework\ObjectManagerInterface $objectManager
    , UploaderFactory $uploaderFactory, array $data = []
    ) {
        $this->uploaderFactory = $uploaderFactory;
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
        return __("Ad/Offer Preferences");
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle() {
        return __("Ad/Offer Preferences");
    }

    /**
     * @return bool
     */
    public function canShowTab() {
        /*if ($this->getCustomerId()) {
			$tradePassCollection = $this->_objectManager->create(
                            'Ironedge\TradePass\Model\TradePass'
                    )->getCollection()
                    ->addFieldToFilter(
                    'customer_id', $this->getCustomerId()
            );
            $tradePassId = '';
            foreach ($tradePassCollection as $value){
				$tradePassId = $value->getId();
			}
                
			if($tradePassId!=''){
            	return true;
			}else{
				return false;
			}
        }
        return false;*/
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
        /*if (!$this->canShowTab()) {
            return $this;
        }*/
        $data = array('referrer' => '', 'abn' => '', 'industry' => '','file'=>'','is_confirmed'=>'confirmed');
        /*$collection = $this->_objectManager
                ->create('Ironedge\TradePass\Model\TradePass')->getCollection()
                ->addFieldToFilter('customer_id', $customerId);
        foreach ($collection as $record) {
            $data = $record->getData();
        }*/


        /*         * @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('tradepass_');
        $fieldset = $form->addFieldset(
                'base_fieldset', ['legend' => __('TradePass Information')]
        );
        echo "te";
        die;

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
                    'label' => __('High Performance Gym'),
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
		/*if(isset($data['moreinfo'])){
			if($data['moreinfo']==1){
				$fieldset->addField('link', 'note', array(
		            'text' => __('More information email has already been sent')
		        ));
			}else{
				$fieldset->addField('link', 'link', array(
		            'label' => __('More Information'),
		            'style' => "",
		            'href' => $this->getUrl('tradepass/doc/moreinfo',['customer_id'=>$customerId]),
		            'value' =>  'More Information',
		            'after_element_html' => ''
		        ));
			}
		}else{
			$fieldset->addField('link', 'link', array(
	            'label' => __('More Information'),
	            'style' => "",
	            'href' => $this->getUrl('tradepass/doc/moreinfo',['customer_id'=>$customerId]),
	            'value' =>  'More Information',
	            'after_element_html' => ''
	        ));	
		}*/		
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

    /*public function getDestinationPath() {
        return $this->_filesystem
                        ->getDirectoryWrite(DirectoryList::MEDIA)
                        ->getAbsolutePath('/trade-pass/');
    }*/

}
