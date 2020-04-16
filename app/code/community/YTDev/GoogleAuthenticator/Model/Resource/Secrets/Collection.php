<?php
class YTDev_GoogleAuthenticator_Model_Resource_Secrets_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	public function _construct(){
		$this->_init('google_oauth/secrets');
	}
}