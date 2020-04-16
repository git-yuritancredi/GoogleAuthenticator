<?php
class YTDev_GoogleAuthenticator_Model_Resource_Secrets extends Mage_Core_Model_Resource_Db_Abstract
{
	protected function _construct(){
		$this->_init('google_oauth/secrets', 'id');
	}
}