<?php

/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ironedge\TradePass\Controller\Adminhtml\Doc;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;

class Download extends \Magento\Backend\App\Action {

    protected $fileFactory;

    public function __construct(
    \Magento\Framework\Controller\Result\RawFactory $resultRawFactory, \Magento\Framework\App\Response\Http\FileFactory $fileFactory, Filesystem $fileSystem, \Magento\Backend\App\Action\Context $context
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->fileFactory = $fileFactory;

        $this->fileSystem = $fileSystem;
        parent::__construct($context);
    }

    public function execute() {

        //do your custom stuff here
        $fileName = $this->getRequest()->getParam('file');

        $abslouteFilePath = $this->fileSystem
                ->getDirectoryWrite(DirectoryList::MEDIA)
                ->getAbsolutePath('/trade-pass/');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        header('Content-Type: application/octet-stream'); // or application/force-download
        echo file_get_contents($abslouteFilePath.$fileName);

    }

}
