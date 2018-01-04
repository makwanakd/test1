<?php
/**
 * Icecube Business Extension 
 * 
 * Icecube_Business
 * 
 * PHP version 5.x
 *
 * @category  PDF
 * @package   Icecube_Business
 * @author    Icecube Digital <support@icecubedigital.com>
 * @copyright 2016 Icecube Digital
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://www.icecubedigital.com
 */
namespace Icecube\Business\Block\Adminhtml\Packages\Helper;

use Magento\Framework\Data\Form\Element\Image as ImageField;
use Magento\Framework\Data\Form\Element\Factory as ElementFactory;
use Magento\Framework\Data\Form\Element\CollectionFactory as ElementCollection;
use Magento\Framework\Escaper;
use Icecube\Business\Model\Theme\Image as ImageFile;
use Magento\Framework\UrlInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Get value of string
 * 
 * @method string getValue()
 */
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
class Image extends ImageField
{
    /**
     * PDF model
     *
     * @var \Icecube\Business\Model\Theme\PDF
     */
    protected $imageModel;
	
	protected $_urlBuilder;

    /**
     * Constructure
     *  
     * @param AuthorImage $imageModel image model
     * @param ElementFactory $factoryElement factory element
     * @param ElementCollectionFactory $factoryCollection factory collection
     * @param Escaper $escaper esacaper
     * @param UrlInterface $urlBuilder url builder
     * @param array $data array data
     */
    public function __construct(
        ImageFile $imageModel,
        ElementFactory $factoryElement,
        ElementCollection $factoryCollection,
        Escaper $escaper,
        UrlInterface $urlBuilder,
        $data = []
    )
    {
		
        $this->imageModel = $imageModel;
        parent::__construct (
        	$factoryElement, 
        	$factoryCollection, 
        	$escaper, 
        	$urlBuilder, 
        	$data);
    }

    /**
     * 
     * Get pdf download url
     *
     * @return string
     * 
     */
	 public function getElementHtml()
    {
        $html = '';
        if($this->getValue() == ""):
        	$this->setClass('input-file required-entry');
		else:
			$this->setClass('input-file');
		endif;
        $html = $this->_getElementHtml();
        if ((string)$this->getValue()) {
            $url = $this->_getUrl();

            if (!preg_match("/^http\:\/\/|https\:\/\//", $url)) {
                $url = $this->_urlBuilder->getBaseUrl (
                	['_type' => UrlInterface::URL_TYPE_MEDIA]
                ) . $url;
            }
            $html .= '<img src="'.$url .'"'.$this->_getUiId('link') .' height="20" width="20" style="margin-right: 10px;"/>';
        }
		
        
        $html .= $this->_getDeleteCheckbox();
        return $html;
    }
    
	/**
	* 
	* Url of the media
	* 
	* @return url 
	*/
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = $this->imageModel->getBaseUrl().$this->getValue();
        }
        return $url;
    }
	
	/**
	* 
	* Html of file upload
	* 
	* @return string 
	*/
	public function _getElementHtml()
    {
        $html = '';
        $htmlId = $this->getHtmlId();

        if (($beforeElementHtml = $this->getBeforeElementHtml())) {
            $html .= '<label class="addbefore" for="' .
                $htmlId .
                '">' .
                $beforeElementHtml .
                '</label>';
        }

        $html .= '<input id="' .
            $htmlId .
            '" name="' .
            $this->getName() .
            '" ' .
            $this->_getUiId() .
            ' value="' .
            $this->getEscapedValue() .
            '" ' .
            $this->serialize(
                $this->getHtmlAttributes()
            ) . '/>';

        if (($afterElementJs = $this->getAfterElementJs())) {
            $html .= $afterElementJs;
        }

        if (($afterElementHtml = $this->getAfterElementHtml())) {
            $html .= '<label class="addafter" for="' .
                $htmlId .
                '">' .
                $afterElementHtml .
                '</label>';
        }

        return $html;
    }
    
	/**
	* 
	* Delete pdf checkbox
	* 
	* @return string 
	*/
	public function _getDeleteCheckbox()
    {
        $html = '';
        if ($this->getValue()) {
            $label = (string)new \Magento\Framework\Phrase('Delete Icon');
            $html .= '<span class="delete-image">';
            $html .= '<input type="checkbox"' .
                ' name="' .
                parent::getName() .
                '[delete]" value="1" class="checkbox"' .
                ' id="' .
                $this->getHtmlId() .
                '_delete"' .
                ($this->getDisabled() ? ' disabled="disabled"' : '') .
                '/>';
            $html .= '<label for="' .
                $this->getHtmlId() .
                '_delete"' .
                ($this->getDisabled() ? ' class="disabled"' : '') .
                '> ' .
                $label .
                '</label>';
            $html .= $this->_getHiddenInput();
            $html .= '</span>';
        }

        return $html;
    }
}