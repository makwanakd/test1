<?php
namespace Icecube\Businesses\Controller\Coupondetails;

class Index extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
    	/*$tcpdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
				
		$text = '<div>testttt</div>';
		$tcpdf->AddPage();
		$tcpdf->writeHTML($text, true, false, true, false, '');
		$tcpdf->lastPage();
		$fn = 'coupon_'.time().'.pdf';
		//$tcpdf->Output($fn, 'F');
		$tcpdf->Output($fn, 'I');*/
		
    	$this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
    
}