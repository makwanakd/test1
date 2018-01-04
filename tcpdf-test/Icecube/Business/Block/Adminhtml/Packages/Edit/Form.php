<?php
/**
 * Icecube Business Extension 
 * 
 * Icecube_Business
 * 
 * PHP version 5.x
 *
 * @category  Form
 * @package   Icecube_Business
 * @author    Icecube Digital <support@icecubedigital.com>
 * @copyright 2016 Icecube Digital
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.icecubedigital.com
 */
namespace Icecube\Business\Block\Adminhtml\Packages\Edit;
//use Icecube\Business\Block\Adminhtml\Template\Helper\PDF as PDFHelper;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as Collection;
/**
 * Icecube_Business
 *
 * @category  Edit
 * @package   Icecube_Business
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
        return $this->_coreRegistry->registry('_business_packages');
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
            ['legend' => __('Package Information'), 'class' => 'fieldset-wide']
        );
        if ($model->getBusinesspackageId()) {
            $fieldset->addField(
	            'id', 
	            'hidden', 
	            [
		            'name' => 'id', 
		            'value' => $model->getBusinesspackageId()
	            ]
            );
        }
        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
				'class' => 'required-entry',
                'value' => $model->getName()
            ]
        );
        $fieldset->addType('image', 'Icecube\Business\Block\Adminhtml\Packages\Helper\Image');
        $fieldset->addField(
            'icon',
            'image',
            [
                'name' => 'icon',
                'label' => __('Icon'),
                'title' => __('Icon'),
                'required' => true,
				'class' => 'required-entry',
                'value' => $model->getIcon()
            ]
        );
        $fieldset->addField(
            'price',
            'text',
            [
                'name' => 'price',
                'label' => __('Price'),
                'title' => __('PRice'),
                'required' => true,
				'class' => 'required-entry',
                'value' => $model->getPrice()
            ]
        );
        $fieldset->addField(
            'box_count',
            'text',
            [
                'name' => 'box_count',
                'label' => __('Box Count'),
                'title' => __('Box Count'),
                'required' => true,
				'class' => 'required-entry',
                'value' => $model->getBoxCount()
            ]
        );
        $fieldset->addField(
            'box_count_tooltip',
            'text',
            [
                'name' => 'box_count_tooltip',
                'label' => __('Box Count Tooltip'),
                'title' => __('Box Count Tooltip'),
                'required' => true,
				'class' => 'required-entry',
                'value' => $model->getBoxCountTooltip()
            ]
        );
        $fieldset->addField(
            'box_advertisements_count',
            'text',
            [
                'name' => 'box_advertisements_count',
                'label' => __('Box Advertisements Count'),
                'title' => __('Box Advertisements Count'),
                'required' => true,
				'class' => 'required-entry',
                'value' => $model->getBoxAdvertisementsCount()
            ]
        );
        $fieldset->addField(
            'box_advertisements_count_tooltip',
            'text',
            [
                'name' => 'box_advertisements_count_tooltip',
                'label' => __('Box Advertisements Count Tooltip'),
                'title' => __('Box Advertisements Count Tooltip'),
                'required' => true,
				'class' => 'required-entry',
                'value' => $model->getBoxAdvertisementsCountTooltip()
            ]
        );
        $fieldset->addField(
            'business_profile_pages',
            'text',
            [
                'name' => 'business_profile_pages',
                'label' => __('Business Profile Pages'),
                'title' => __('Business Profile Pages'),
                'required' => true,
				'class' => 'required-entry',
                'value' => $model->getBusinessProfilePages()
            ]
        );
        $fieldset->addField(
            'business_profile_pages_tooltip',
            'text',
            [
                'name' => 'business_profile_pages_tooltip',
                'label' => __('Business Profile Pages Tooltip'),
                'title' => __('Business Profile Pages Tooltip'),
                'required' => true,
				'class' => 'required-entry',
                'value' => $model->getBusinessProfilePagesTooltip()
            ]
        );
        $fieldset->addField(
            'business_landing_pages',
            'text',
            [
                'name' => 'business_landing_pages',
                'label' => __('Business Landing Pages'),
                'title' => __('Business Landing Pages'),
                'required' => true,
				'class' => 'required-entry',
                'value' => $model->getBusinessLandingPages()
            ]
        );
        $fieldset->addField(
            'business_landing_pages_tooltip',
            'text',
            [
                'name' => 'business_landing_pages_tooltip',
                'label' => __('Business Landing Pages Tooltip'),
                'title' => __('Business Landing Pages Tooltip'),
                'required' => true,
				'class' => 'required-entry',
                'value' => $model->getBusinessLandingPagesTooltip()
            ]
        );
        $fieldset->addField(
            'online_ad_placement',
            'text',
            [
                'name' => 'online_ad_placement',
                'label' => __('Online Ad Placement'),
                'title' => __('Online Ad Placement'),
                'required' => true,
				'class' => 'required-entry',
                'value' => $model->getOnlineAdPlacement()
            ]
        );
        $fieldset->addField(
            'online_ad_placement_tooltip',
            'text',
            [
                'name' => 'online_ad_placement_tooltip',
                'label' => __('Online Ad Placement Tooltip'),
                'title' => __('Online Ad Placement Tooltip'),
                'required' => true,
				'class' => 'required-entry',
                'value' => $model->getOnlineAdPlacementTooltip()
            ]
        );
        $fieldset->addField(
            'advertisements_per_box',
            'text',
            [
                'name' => 'advertisements_per_box',
                'label' => __('Advertisements Per Box'),
                'title' => __('Advertisements Per Box'),
                'required' => true,
				'class' => 'required-entry',
                'value' => $model->getAdvertisementsPerBox()
            ]
        );
        $fieldset->addField(
            'advertisements_per_box_tooltip',
            'text',
            [
                'name' => 'advertisements_per_box_tooltip',
                'label' => __('Advertisements Per Box Tooltip'),
                'title' => __('Advertisements Per Box Tooltip'),
                'required' => true,
				'class' => 'required-entry',
                'value' => $model->getAdvertisementsPerBoxTooltip()
            ]
        );
        $fieldset->addField(
            'printing_shipping_included',
            'select',
            [
                'name' => 'printing_shipping_included',
                'label' => __('Printing & Shipping Included'),
                'title' => __('Printing & Shipping Included'),
                'required' => true,
				'class' => 'required-entry',
				'options' => ['1' => __('Yes'), '0' => __('No')],
                'value' => $model->getPrintingShippingIncluded()
            ]
        );
        $fieldset->addField(
            'printing_shipping_included_toolip',
            'text',
            [
                'name' => 'printing_shipping_included_tooltip',
                'label' => __('Printing & Shipping Included Tooltip'),
                'title' => __('Printing & Shipping Included Tooltip'),
                'required' => true,
				'class' => 'required-entry',
                'value' => $model->getPrintingShippingIncludedTooltip()
            ]
        );
        $fieldset->addField(
            'organic_seo',
            'select',
            [
                'name' => 'organic_seo',
                'label' => __('Organic SEO'),
                'title' => __('Organic SEO'),
                'required' => true,
				'class' => 'required-entry',
				'options' => ['1' => __('Yes'), '0' => __('No')],
                'value' => $model->getOrganicSeo()
            ]
        );
        $fieldset->addField(
            'organic_seo_tooltip',
            'text',
            [
                'name' => 'organic_seo_tooltip',
                'label' => __('Organic SEO Tooltip'),
                'title' => __('Organic SEO Tooltip'),
                'required' => true,
				'class' => 'required-entry',
                'value' => $model->getOrganicSeoTooltip()
            ]
        );
        $fieldset->addField(
            'ad_offer_support',
            'select',
            [
                'name' => 'ad_offer_support',
                'label' => __('Ad/Offer Support'),
                'title' => __('Ad/Offer Support'),
                'required' => true,
				'class' => 'required-entry',
				'options' => ['1' => __('Yes'), '0' => __('No')],
                'value' => $model->getAdOfferSupport()
            ]
        );
        $fieldset->addField(
            'ad_offer_support_tooltip',
            'text',
            [
                'name' => 'ad_offer_support_tooltip',
                'label' => __('Ad/Offer Support Tooltip'),
                'title' => __('Ad/Offer Support Tooltip'),
                'required' => true,
				'class' => 'required-entry',
                'value' => $model->getAdOfferSupportTooltip()
            ]
        );
        $fieldset->addField(
            'logo_design_assistance',
            'select',
            [
                'name' => 'logo_design_assistance',
                'label' => __('Logo Design Assistance'),
                'title' => __('Logo Design Assistance'),
                'required' => true,
				'class' => 'required-entry',
				'options' => ['1' => __('Yes'), '0' => __('No')],
                'value' => $model->getLogoDesignAssistance()
            ]
        );
        $fieldset->addField(
            'logo_design_assistance_tooltip',
            'text',
            [
                'name' => 'logo_design_assistance_tooltip',
                'label' => __('Logo Design Assistance Tooltip'),
                'title' => __('Logo Design Assistance Tooltip'),
                'required' => true,
				'class' => 'required-entry',
                'value' => $model->getLogoDesignAssistanceTooltip()
            ]
        );
        $fieldset->addField(
            'analytics_dashboard',
            'select',
            [
                'name' => 'analytics_dashboard',
                'label' => __('Analytics Dashboard'),
                'title' => __('Analytics Dashboard'),
                'required' => true,
				'class' => 'required-entry',
				'options' => ['1' => __('Yes'), '0' => __('No')],
                'value' => $model->getAnalyticsDashboard()
            ]
        );
        $fieldset->addField(
            'analytics_dashboard_tooltip',
            'text',
            [
                'name' => 'analytics_dashboard_tooltip',
                'label' => __('Analytics Dashboard Tooltip'),
                'title' => __('Analytics Dashboard Tooltip'),
                'required' => true,
				'class' => 'required-entry',
                'value' => $model->getAnalyticsDashboardTooltip()
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
        $fieldset->addField(
            'available_for_comparison',
            'select',
            [
                'name' => 'available_for_comparison',
                'label' => __('Available for Comparison'),
                'title' => __('Available for Comparison'),
                'required' => true,
				'class' => 'required-entry',
				'options' => ['1' => __('Yes'), '0' => __('No')],
                'value' => $model->getAvailableForComparison()
            ]
        );
        //$fieldset->addType('pdf', 'Icecube\Business\Block\Adminhtml\Template\Helper\PDF');
        /*if ($model->getBusinesspackageId()) {
            $fieldset->addField(
	            'id', 
	            'hidden', 
	            [
		            'name' => 'id', 
		            'value' => $model->getBusinesspackageId()
	            ]
            );
        }*/
        /*$fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('NR'),
                'title' => __('NR'),
                'required' => true,
				'class' => 'required-entry',
                'value' => $model->getName()
            ]
        );
        $fieldset->addField(
            'mvs',
            'pdf',
            [
                'name' => 'mvs',
                'label' => __('MDS certificate'),
                'title' => __('MDS certificate'),
                'required' => true,
				'class' => 'required-entry',
                'value' => $model->getMvs()
            ]
        );
        $fieldset->addField(
            'specification',
            'pdf',
            [
                'name' => 'specification',
                'label' => __('Product documents'),
                'title' => __('Product documents'),
                'required' => true,
				'class' => 'required-entry',
                'value' => $model->getSpecification()
            ]
        );
        $fieldset->addField(
            'date',
            'date',
            [
                'name' => 'date',
                'label' => __('Date'),
                'title' => __('Date'),
                'required' => true,
				'class' => 'required-entry',
                'date_format' => 'dd-MM-yyyy',
                'value' => $model->getDate()
            ]
        );*/
        /*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
		$collection = $productCollection->create()
		            ->addAttributeToSelect('*')
		            ->load();
		$optionArray = array();	            
		foreach ($collection as $product) {
		     $optionArray[] = array(
			     'value'=>$product->getSku(), 
			     'label'=>$product->getSku()
		     );
		}
        $fieldset->addField(
            'casnr_list',
            'multiselect',
            [
                'name' => 'casnr_list[]',
	            'label'=> __('SKU'),
	            'title' => __('SKU'),
	            'values'=>$optionArray,
	            'value' => $model->getCasnr_list() 
            ]
        );*/
        $form->setAction($this->getUrl('*/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
