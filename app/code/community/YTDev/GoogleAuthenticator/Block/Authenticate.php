<?php
class YTDev_GoogleAuthenticator_Block_Authenticate extends Mage_Customer_Block_Account
{
	public function getLogoUrl(){
		$_src = Mage::getStoreConfig('design/header/logo_src');
		return $this->getSkinUrl($_src);
	}

}