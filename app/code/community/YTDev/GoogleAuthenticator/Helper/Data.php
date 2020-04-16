<?php
class YTDev_GoogleAuthenticator_Helper_Data extends YTDev_GoogleAuthenticator_Helper_Config
{
	public function isCustomerEnabled(){
		return $this->getConfig(self::CUSTOMER_ENABLED);
	}

	public function isAdminEnabled(){
		return $this->getConfig(self::ADMIN_ENABLED);
	}

	public function getSecretLength(){
		return $this->getConfig(self::SECRET_LENGTH);
	}

	public function getCodeName(){
		return $this->getConfig(self::CODE_NAME);
	}

	public function getEmailTemplate(){
		$_template = $this->getConfig(self::EMAIL_TEMPLATE);
		return $_template == 'ytdev_google_oauth_settings_email_template' ? 'google_oauth_email_template' : $_template;
	}

	public function sendRecoveryMail(Varien_Object $_user, $_recovery){
		if($_user->getEmail()){
			$translate  	= Mage::getSingleton('core/translate');
			$_checkEmail 	= Mage::getModel('core/email_template')
			->setDesignConfig(array('area' => 'frontend'))
			->sendTransactional(
				$this->getEmailTemplate(),
				array(
					'name' 	=> Mage::getStoreConfig('trans_email/ident_support/name'),
					'email'	=> Mage::getStoreConfig('trans_email/ident_support/email')
				),
				$_user->getEmail(),
				$_user->getName(),
				array(
					'name' 				=> $_user->getName(),
					'recovery_code'		=> $_recovery
				)
			);

			$translate->setTranslateInline(true);

			if(!$_checkEmail->getSentSuccess()){
				throw new Exception("Unable to send email.");
			}
		}
	}

	public function addLog($string, $type = 'cron'){
		Mage::log(is_array($string) ? print_r($string, true) : $string, null, 'google_oauth_'.$type.'.log', true);
	}
}