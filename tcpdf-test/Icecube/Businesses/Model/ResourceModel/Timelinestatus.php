<?php
/**
 * Icecube Businesses Extension 
 * 
 * Icecube_Businesses
 * 
 * PHP version 5.x
 *
 * @category  Importer
 * @package   Icecube_Businesses
 * @author    Icecube Digital <support@icecubedigital.com>
 * @copyright 2016 Icecube Digital
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.icecubedigital.com
 */
namespace Icecube\Businesses\Model\ResourceModel;

/**
 * Directory Country Resource Model
 */
/**
 * Icecube_Businesses
 *
 * @category  Importer
 * @package   Icecube_Businesses
 * @author    Icecube Digital <support@icecubedigital.com>
 * @copyright 2016 Icecube Digital
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.icecubedigital.com
 */
class Timelinestatus extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string|null $resourcePrefix
     */
    /*public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);     
    }*/
    
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('business_timeline', 'id');
    }
}
