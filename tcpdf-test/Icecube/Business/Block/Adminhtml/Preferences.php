<?php
/**
 * Icecube Business Extension 
 * 
 * Icecube_Business
 * 
 * PHP version 5.x
 *
 * @category  Template
 * @package   Icecube_Business
 * @author    Icecube Digital <support@icecubedigital.com>
 * @copyright 2016 Icecube Digital
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.icecubedigital.com
 */

/**
 * Newsletter templates page content block
 *
 * @author      Icecube Digital <support@icecubedigital.com>
 */
namespace Icecube\Business\Block\Adminhtml;
/**
 * Icecube_Business
 *
 * @category  Template
 * @package   Icecube_Business
 * @author    Icecube Digital <support@icecubedigital.com>
 * @copyright 2016 Icecube Digital
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.icecubedigital.com
 */
class Preferences extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $template = 'preferences/list.phtml';

    
    /**
     * @return $this
     */
    protected function prepareLayout()
    {
        $this->getToolbar()->addChild(
            'add_button',
            'Magento\Backend\Block\Widget\Button',
            [
                'label' => __('Add New Business'),
                'onclick' => "window.location='" . $this->getCreateUrl() . "'",
                'class' => 'add primary add-template'
            ]
        );

        return parent::prepareLayout();
    }

    /**
     * Get the url for create	
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new');
    }

    /**
     * Get header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __(' Business');
    }
}
