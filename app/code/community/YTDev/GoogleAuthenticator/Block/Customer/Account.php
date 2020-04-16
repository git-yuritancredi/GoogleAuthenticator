<?php
class YTDev_GoogleAuthenticator_Block_Customer_Account extends Mage_Customer_Block_Account
{
	protected $_customer;

	public function getSecretInfo(){
		$_info 		= new Varien_Object();
		$_current	= $this->getCurrentCustomer();
		$_helper 	= Mage::helper('google_oauth/authenticator');
		$_config 	= Mage::helper('google_oauth');

		if($_current->isGoogleOauthEnabled()){
			$_secret = $_current->getOauthSecret();
			$_info->setEnabled($_secret->getEnabled());
			$_info->setSecret($_secret->getSecret());
			$_info->setQrCode($_helper->getQRCodeGoogleUrl($_current->getEmail(), $_secret->getSecret(), $_config->getCodeName()));
		}else{
			$_info->setEnabled(false);
		}

		return $_info;
	}

	protected function getCurrentCustomer(){
		if(!$this->_customer){
			$this->_customer = Mage::getSingleton('customer/session')->getCustomer();
		}
		return $this->_customer;
	}
}