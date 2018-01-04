<?php
/**
 * Icecube Business Extension 
 * 
 * Icecube_Business
 * 
 * PHP version 5.x
 *
 * @category  New_Action
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
 * @category  New_Action
 * @package   Icecube_Business
 * @author    Icecube Digital <support@icecubedigital.com>
 * @copyright 2016 Icecube Digital
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.icecubedigital.com
 */
class NewAction extends \Icecube\Business\Controller\Adminhtml\Packages
{
    /**
     * Create new Newsletter Packages
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
