<?php
class YTDev_GoogleAuthenticator_Model_Admin_User extends Mage_Admin_Model_User
{

	public function isGoogleOauthEnabled(){
		$_helper = Mage::helper('google_oauth');
		if($this->getGoogleOauthEnabled() && $this->getOauthSecret() && $_helper->isAdminEnabled()){
			return true;
		}
		return false;
	}

	public function getOauthSecret(){
		$_collection = Mage::getModel('google_oauth/secrets')->getCollection()
			->addFieldToFilter('entity_id', $this->getId())
			->addFieldToFilter('type_id', YTDev_GoogleAuthenticator_Model_Secrets::TYPE_ADMIN)
			->addFieldToFilter('enabled', 1);

		if($_collection->getSize()){
			return $_collection->getFirstItem();
		}
		return false;
	}

	public function getOauthCollection(){
		return Mage::getModel('google_oauth/secrets')->getCollection()
			->addFieldToFilter('entity_id', $this->getId())
			->addFieldToFilter('type_id', YTDev_GoogleAuthenticator_Model_Secrets::TYPE_ADMIN);
	}

	public function createNewOauth($_secret){
		$_model = Mage::getModel('google_oauth/secrets');
		$_model->setEntityId($this->getId());
		$_model->setEnabled(true);
		$_model->setTypeId(YTDev_GoogleAuthenticator_Model_Secrets::TYPE_ADMIN);
		$_model->setSecret($_secret);
		$_model->save();

		$this->setGoogleOauthEnabled(true);
		Mage::getSingleton('admin/session')->setGoogleAuthDone(true);
	}

	public function disableOauth(){
		if($_secret = $this->getOauthSecret()){
			$_secret->setEnabled(false);
			$_secret->save();
		}

		$this->setGoogleOauthEnabled(false);
		Mage::getSingleton('admin/session')->setGoogleAuthDone(false);
	}

	public function enableOauth(){
		$_collection = $this->getOauthCollection();
		if($_collection->getSize()){
			$_secret = $_collection->getFirstItem();
			$_secret->setEnabled(true);
			$_secret->save();
		}

		$this->setGoogleOauthEnabled(true);
		Mage::getSingleton('admin/session')->setGoogleAuthDone(true);
	}

	protected function _beforeSave(){
		$_enabled = Mage::app()->getRequest()->getParam('google_oauth_enabled');

		if($_enabled == '1'){
			$_collection = $this->getOauthCollection();
			if($_collection->getSize()){
				$this->enableOauth();
			}else{
				$this->createNewOauth(Mage::helper('google_oauth/authenticator')->createSecret(Mage::helper('google_oauth')->getSecretLength()));
			}
		}else{
			$this->disableOauth();
		}

		return parent::_beforeSave();
	}
}