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
 // @codingStandardsIgnoreFile
namespace Icecube\Businesses\Model;

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
class Reviews extends \Magento\Framework\Model\AbstractModel
{
    /**
     * CMS page cache tag
     */
    const CACHE_TAG = 'reviews';

    /**
     * Importer's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * Constructure 
     * 
     * @param \Magento\Framework\Model\Context $context Context
     * @param \Magento\Framework\Registry $registry Registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource Resourse
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection Resourse Collection
     * @param \Magento\Framework\UrlInterface $urlBuilder Builder
     * @param array $data
     * data
     */
    /*function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [])
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }*/

    /**
     * Constructure Initialization
     * 
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Icecube\Businesses\Model\ResourceModel\Reviews');
    }
    
       
    /**
     * Prepare post's statuses.
     * Available event blog_post_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }
    
    


}
