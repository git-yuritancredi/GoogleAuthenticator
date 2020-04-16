<?php
class YTDev_GoogleAuthenticator_Model_Secrets extends Mage_Core_Model_Abstract
{
	const TYPE_CUSTOMER = "customer";
	const TYPE_ADMIN	= "admin";

	protected function _construct(){
		$this->_init('google_oauth/secrets');
	}

	public function recovery(){
		$_helper	= Mage::helper('google_oauth');
		$_recovery 	= Mage::helper('core')->getRandomString($_helper->getSecretLength());
		$this->setRecovery($_recovery);
		$this->save();
		$_helper->sendRecoveryMail($this->getUserEntity(), $_recovery);
	}

	public function getUserEntity(){
		if($this->getEntityId()){
			switch($this->getTypeId()){
				case self::TYPE_CUSTOMER:
					return Mage::getModel('customer/customer')->load($this->getEntityId());
				case self::TYPE_ADMIN:
					return Mage::getModel('admin/user')->load($this->getEntityId());
			}
		}
		return false;
	}

	protected function _beforeSave(){
		if($this->getId()){
			$this->setUpdatedAt(date("Y-m-d H:i:s"));
		}else{
			$this->setCreatedAt(date("Y-m-d H:i:s"));
		}
		return parent::_beforeSave();
	}
}