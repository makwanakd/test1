<?php
/**
 * Icecube Business Extension 
 * 
 * Icecube_Business
 * 
 * PHP version 5.x
 *
 * @category  Edit
 * @package   Icecube_Business
 * @author    Icecube Digital <support@icecubedigital.com>
 * @copyright 2016 Icecube Digital
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.icecubedigital.com
 */
namespace Icecube\Business\Controller\Adminhtml\Packages;

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
class Edit extends \Icecube\Business\Controller\Adminhtml\Packages
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry Registry
     */
    protected $_coreRegistry = null;

    /**
     * Constructure
     * 
     * @param \Magento\Backend\App\Action\Context $context Context
     * @param \Magento\Framework\Registry $coreRegistry Core Registry
     */
    public function __construct(
    	\Magento\Backend\App\Action\Context $context, 
    	\Magento\Framework\Registry $coreRegistry
    )
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Edit Newsletter Template
     *
     * @return void
     */
    public function execute()
    {
        $model = $this->_objectManager->create('Icecube\Business\Model\Packages');
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model->load($id);
        }
        $this->_coreRegistry->register('_business_packages', $model);

        $this->_view->loadLayout();
        $this->_setActiveMenu('Icecube_Business::business_packages');
        if ($model->getId()) {
            $breadcrumbTitle = __('Edit Package');
            $breadcrumbLabel = $breadcrumbTitle;
        } else {
            $breadcrumbTitle = __('New Package');
            $breadcrumbLabel = __('Create Package');
        }
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $breadcrumbTitle
        );
        $this->_addBreadcrumb($breadcrumbLabel, $breadcrumbTitle);

        // restore data
        $values = $this->_getSession()->getData('business_packages_form_data', true);
        if ($values) {
            $model->addData($values);
        }
        $this->_view->renderLayout();
    }
}
