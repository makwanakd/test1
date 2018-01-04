<?php
/**
 * Icecube Business Extension 
 * 
 * Icecube_Business
 * 
 * PHP version 5.x
 *
 * @category  Form
 * @preference   Icecube_Business
 * @author    Icecube Digital <support@icecubedigital.com>
 * @copyright 2016 Icecube Digital
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.icecubedigital.com
 */
namespace Icecube\Business\Block\Adminhtml\Preferences\Edit;
//use Icecube\Business\Block\Adminhtml\Template\Helper\PDF as PDFHelper;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as Collection;
/**
 * Icecube_Business
 *
 * @category  Edit
 * @preference   Icecube_Business
 * @author    Icecube Digital <support@icecubedigital.com>
 * @copyright 2016 Icecube Digital
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.icecubedigital.com
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
	/**
 	* Business Form
 	*/
    /**
     * Constructure 
     * 
     * @param \Magento\Backend\Block\Template\Context $context 
     * context variable
     * @param \Magento\Framework\Registry $registry 
     * Registry Variable
     * @param \Magento\Framework\Data\FormFactory $formFactory form Factory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig for wysiwyg
     * @param array $data array of data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Retrieve template object
     *
     * @return \Magento\Newsletter\Model\Template
     */
    public function getModel()
    {
        return $this->_coreRegistry->registry('_business_preferences');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->getModel();
        /** 
        * Form Variable
        * 
        * @var \Magento\Framework\Data\Form $form  
        * 
        */
        $form = $this->_formFactory->create(
            ['data' => 
            	[
            		'id' => 'edit_form', 
            		'action' => $this->getData('action'), 
            		'method' => 'post'
            	]
            ]
        );
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Preference Information'), 'class' => 'fieldset-wide']
        );
        if ($model->getBusinesspreferenceId()) {
            $fieldset->addField(
	            'id', 
	            'hidden', 
	            [
		            'name' => 'id', 
		            'value' => $model->getBusinesspreferenceId()
	            ]
            );
        }
        $fieldset->addField(
            'preference_title',
            'text',
            [
                'name' => 'preference_title',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
				'class' => 'required-entry',
                'value' => $model->getPreferenceTitle()
            ]
        );
        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => true,
				'class' => 'required-entry',
				'options' => ['1' => __('Enable'), '0' => __('Disable')],
                'value' => $model->getStatus()
            ]
        );
        $form->setAction($this->getUrl('*/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
