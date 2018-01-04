<?php
namespace Icecube\Businesses\Controller\Analytics;

class Mailchimp extends \Magento\Framework\App\Action\Action
{
	protected $_helper;
    
    protected $_customer;
    
    protected $_api = null;

    public function __construct(
    	\Magento\Framework\App\Action\Context $context,
        \Ebizmarts\MailChimp\Helper\Data $helper
    ) {
    	parent::__construct($context);
        $this->_helper          = $helper;
        $this->_api             = $this->_helper->getApi();
    }
    public function execute()
    {
            //$email = 'magento.icecube@gmail.com';
            $email = 'test8.capital@gmail.com';
            //$mergeVars = array("FNAME" => "test", "LNAME" => "test2", "STATE" => "wyoming", "MMERGE4" => "4546465");
            $mergeVars = array("FNAME" => "test", "LNAME" => "test2", "GENDER" => "Female", "DOB" => "01/01", "BTELEPHONE" => "7798798", "STELEPHONE" => "7798798", "CGROUP" => "General", "STOREID" => "1");
            $api = $this->_api;
            $api = $this->_api;
            
            $status = 'pending';
            //$status = 'subscribed';
            try {
                $emailHash = md5(strtolower($email));
                    $return = $api->lists->members->addOrUpdate('86d7f645c4', $emailHash, null, $status, $mergeVars, null, null, null, null, $email, $status);
            } catch (\Exception $e) {
                $this->_helper->log($e->getMessage());
            }
      echo "successfully subscribed";
    }
    
}