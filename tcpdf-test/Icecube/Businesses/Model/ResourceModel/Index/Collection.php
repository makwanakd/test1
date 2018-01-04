<?php
/**
 * Icecube Businesses Extension 
 * 
 * Icecube_Businesses
 * 
 * PHP version 5.x
 *
 * @category  Collection
 * @package   Icecube_Businesses
 * @author    Icecube Digital <support@icecubedigital.com>
 * @copyright 2016 Icecube Digital
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.icecubedigital.com
 */

// @codingStandardsIgnoreFile

/**
 * Directory Country Resource Collection
 */
namespace Icecube\Businesses\Model\ResourceModel\Index;

/**
 * Class Collection
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
/**
 * Icecube_Businesses
 *
 * @category  Collection
 * @package   Icecube_Businesses
 * @author    Icecube Digital <support@icecubedigital.com>
 * @copyright 2016 Icecube Digital
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.icecubedigital.com
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    
    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Icecube\Businesses\Model\Index', 'Icecube\Businesses\Model\ResourceModel\Index');
    }
}
