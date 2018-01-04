<?php
/**
 * Icecube Business Extension 
 * 
 * Icecube_Business
 * 
 * PHP version 5.x
 *
 * @category  Save
 * @package   Icecube_Business
 * @author    Icecube Digital <support@icecubedigital.com>
 * @copyright 2016 Icecube Digital
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.icecubedigital.com
 */
namespace Icecube\Business\Controller\Adminhtml\Packages;

use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;

/**
 * Icecube_Business
 *
 * @category  Save
 * @package   Icecube_Business
 * @author    Icecube Digital <support@icecubedigital.com>
 * @copyright 2016 Icecube Digital
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.icecubedigital.com
 */
class Save extends \Icecube\Business\Controller\Adminhtml\Packages
{
	public function __construct(
        \Magento\Backend\App\Action\Context $context,        
        \Magento\Catalog\Model\ProductRepository $productRepository,
        array $data = []
    )
    {
        $this->_productRepository = $productRepository;
        parent::__construct($context);
    }
    public function getProductById($id)
    {
        return $this->_productRepository->getById($id);
    }
    
    public function getProductBySku($sku)
    {
        return $this->_productRepository->get($sku);
    }
    public function setCustomOption()
    {
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$packageCollection =  $objectManager->create('Icecube\Business\Model\ResourceModel\Packages\CollectionFactory');
		    $collectionPackage = $packageCollection->create()->load();
			$sku = 'business-packages';
			$_product = $this->getProductBySku($sku);
			$values = array();
			foreach($collectionPackage as $collection){
                    		$values[] = array(
			                        'title'=>$collection->getName(),
			                        'price'=>intval(str_replace(',','',$collection->getPrice())),
			                        'price_type'=>"fixed",
			                        'sort_order'=>1,
			                        'is_delete'=>0,
			                        'option_type_id'=>-1,
                    			);
                    		}
			$customOption = $objectManager->create('Magento\Catalog\Api\Data\ProductCustomOptionInterface');
			$customOption->setTitle('Package')
			    ->setType('drop_down')
			    ->setIsRequire(false)
			    ->setSortOrder(1)
			    ->setPriceType('fixed')
			    ->setValues($values)
			    ->setProductSku($_product->getSku());
			$customOptions[] = $customOption;
			$_product->setOptions($customOptions)->setCanSaveCustomOptions(true)->save();
	}
	public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->getResponse()->setRedirect($this->getUrl('*/packages'));
        }
        $template = $this->_objectManager->create('Icecube\Business\Model\Packages');
        
        $id = (int)$request->getParam('id');
        if ($id) {
            $template->load($id);
        }
		if($id) {
			$data = $request->getParams();
			if (isset($_FILES['icon']) && $_FILES['icon']['name'] != '') {
			try {
					$uploader = $this->_objectManager->get('Icecube\Business\Model\Theme\Upload');
					$imageModel = $this->_objectManager->get('Icecube\Business\Model\Theme\Image');
					$data['icon'] = $uploader->uploadFileAndGetName('icon', $imageModel->getBaseDir(), $data);
				} catch (Exception $e) {
					$data['icon'] = $_FILES['icon']['name'];
				}
			} 
			else {
				$data['icon'] = $template['icon'];
			}
			
            $template->setData('name',
					 $data['name']
				)->setData('icon',
            		$data['icon']
				)->setData('price',
            		$data['price']
				)->setData('box_count',
            		$data['box_count']
				)->setData('box_count_tooltip',
            		$data['box_count_tooltip']
				)->setData('box_advertisements_count',
            		$data['box_advertisements_count']
				)->setData('box_advertisements_count_tooltip',
            		$data['box_advertisements_count_tooltip']
				)->setData('business_profile_pages',
            		$data['business_profile_pages']
				)->setData('business_profile_pages_tooltip',
            		$data['business_profile_pages_tooltip']
				)->setData('business_landing_pages',
            		$data['business_landing_pages']
				)->setData('business_landing_pages_tooltip',
            		$data['business_landing_pages_tooltip']
				)->setData('online_ad_placement',
            		$data['online_ad_placement']
				)->setData('online_ad_placement_tooltip',
            		$data['online_ad_placement_tooltip']
        		)->setData('advertisements_per_box',
        			$data['advertisements_per_box']
        		)->setData('advertisements_per_box_tooltip',
        			$data['advertisements_per_box_tooltip']
        		)->setData('printing_shipping_included',
        			$data['printing_shipping_included']
        		)->setData('printing_shipping_included_tooltip',
        			$data['printing_shipping_included_tooltip']
        		)->setData('organic_seo',
        			$data['organic_seo']
        		)->setData('organic_seo_tooltip',
        			$data['organic_seo_tooltip']
        		)->setData('ad_offer_support',
        			$data['ad_offer_support']
        		)->setData('ad_offer_support_tooltip',
        			$data['ad_offer_support_tooltip']
        		)->setData('logo_design_assistance',
        			$data['logo_design_assistance']
        		)->setData('logo_design_assistance_tooltip',
        			$data['logo_design_assistance_tooltip']
        		)->setData('analytics_dashboard',
        			$data['analytics_dashboard']
        		)->setData('analytics_dashboard_tooltip',
        			$data['analytics_dashboard_tooltip']
				)->setData('status',
            		$data['status']
				)->setData('available_for_comparison',
            		$data['available_for_comparison']
				);
			$template->save();
			$this->setCustomOption();
            $this->messageManager->addSuccess(__('The Package has been Updated Successfully.'));
            $this->_getSession()->setFormData(false);
			return $resultRedirect->setPath('*/*/');
		} else {
		try {
				$data = $request->getParams();
				if (isset($_FILES['icon']) && $_FILES['icon']['name'] != '') {
				try {
						$uploader = $this->_objectManager->get('Icecube\Business\Model\Theme\Upload');
						$imageModel = $this->_objectManager->get('Icecube\Business\Model\Theme\Image');
						$data['icon'] = $uploader->uploadFileAndGetName('icon', $imageModel->getBaseDir(), $data);
					} catch (Exception $e) {
						$data['icon'] = $_FILES['icon']['name'];
					}
				} 
				else {
					$data['icon'] = $_FILES['icon']['name'];
				}
				$template->setData('name',
					 $data['name']
				)->setData('icon',
            		$data['icon']
				)->setData('price',
            		$data['price']
				)->setData('box_count',
            		$data['box_count']
				)->setData('box_count_tooltip',
            		$data['box_count_tooltip']
				)->setData('box_advertisements_count',
            		$data['box_advertisements_count']
				)->setData('box_advertisements_count_tooltip',
            		$data['box_advertisements_count_tooltip']
				)->setData('business_profile_pages',
            		$data['business_profile_pages']
				)->setData('business_profile_pages_tooltip',
            		$data['business_profile_pages_tooltip']
				)->setData('business_landing_pages',
            		$data['business_landing_pages']
				)->setData('business_landing_pages_tooltip',
            		$data['business_landing_pages_tooltip']
				)->setData('online_ad_placement',
            		$data['online_ad_placement']
				)->setData('online_ad_placement_tooltip',
            		$data['online_ad_placement_tooltip']
            	)->setData('advertisements_per_box',
        			$data['advertisements_per_box']
        		)->setData('advertisements_per_box_tooltip',
        			$data['advertisements_per_box_tooltip']
        		)->setData('printing_shipping_included',
        			$data['printing_shipping_included']
        		)->setData('printing_shipping_included_tooltip',
        			$data['printing_shipping_included_tooltip']
        		)->setData('organic_seo',
        			$data['organic_seo']
        		)->setData('organic_seo_tooltip',
        			$data['organic_seo_tooltip']
        		)->setData('ad_offer_support',
        			$data['ad_offer_support']
        		)->setData('ad_offer_support_tooltip',
        			$data['ad_offer_support_tooltip']
        		)->setData('logo_design_assistance',
        			$data['logo_design_assistance']
        		)->setData('logo_design_assistance_tooltip',
        			$data['logo_design_assistance_tooltip']
        		)->setData('analytics_dashboard',
        			$data['analytics_dashboard']
        		)->setData('analytics_dashboard_tooltip',
        			$data['analytics_dashboard_tooltip']
				)->setData('status',
            		$data['status']
				);
				$template->save();
				$this->setCustomOption();
				$this->messageManager->addSuccess(__('The Package has been saved.'));
				$this->_getSession()->setFormData(false);
			} catch (LocalizedException $e) {
				$this->messageManager->addError(nl2br($e->getMessage()));
				$this->_getSession()->setData('business_package_form_data', $this->getRequest()->getParams());
				return $resultRedirect->setPath('*/*/edit', ['id' => $template->getBusinesspackageId(), '_current' => true]);
			} catch (\Exception $e) {
				 $this->messageManager->addError(nl2br($e->getMessage()));
				$this->messageManager->addException($e, __('Something went wrong while saving this package.'));
				$this->_getSession()->setData('business_package_form_data', $this->getRequest()->getParams());
				return $resultRedirect->setPath('*/*/edit', ['id' => $template->getBusinesspackageId(), '_current' => true]);
			}
		} 
        return $resultRedirect->setPath('*/*/');
    }
}
