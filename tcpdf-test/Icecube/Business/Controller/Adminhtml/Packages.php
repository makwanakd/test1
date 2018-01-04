<?php
/**
 * Icecube Business Extension 
 * 
 * Icecube_Business
 * 
 * PHP version 5.x
 *
 * @category  Tempalte_Abstaract
 * @package   Icecube_Batchfinder
 * @author    Icecube Digital <support@icecubedigital.com>
 * @copyright 2016 Icecube Digital
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.icecubedigital.com
 */
namespace Icecube\Business\Controller\Adminhtml;
/**
 * Icecube_Batchfinder
 *
 * @category  Tempalte_Abstaract
 * @package   Icecube_Business
 * @author    Icecube Digital <support@icecubedigital.com>
 * @copyright 2016 Icecube Digital
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.icecubedigital.com
 */
abstract class Packages extends \Magento\Backend\App\Action
{
    /**
     * Retrieve well-formed admin user data from the form input
     *
     * @param array $data array data
     * 
     * @return array
     */
  
    /**
     * Is Allow Function
     *  
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Icecube_Business::business_packages');
    }
     
}