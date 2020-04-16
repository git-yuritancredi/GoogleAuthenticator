<?php
class YTDev_GoogleAuthenticator_Helper_Config extends Mage_Core_Helper_Abstract
{
	const CONFIG_PATH 		= "ytdev_google_oauth/settings/";
	const ADMIN_ENABLED 	= "enabled_admin";
	const CUSTOMER_ENABLED	= "enabled_customer";
	const CODE_NAME			= "code_name";
	const SECRET_LENGTH		= "secret_length";
	const EMAIL_TEMPLATE	= "email_template";

	protected function getConfig($what){
		return Mage::getStoreConfig(self::CONFIG_PATH.$what);
	}
}