<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Icecube\Contact\Block;

use Magento\Framework\View\Element\Template;

/**
 * Main contact form block
 */
class ContactForm extends Template
{
    /**
     * Returns action url for contact form
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('contact/index/submit', ['_secure' => true]);
    }
}
