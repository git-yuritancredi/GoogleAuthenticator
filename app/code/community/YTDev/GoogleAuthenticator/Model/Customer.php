<?php
class YTDev_GoogleAuthenticator_Model_Customer extends Mage_Customer_Model_Customer
{
	public function isGoogleOauthEnabled(){
		$_helper = Mage::helper('google_oauth');
		if($this->getGoogleOauthEnabled() && $this->getOauthSecret() && $_helper->isCustomerEnabled()){
			return true;
		}
		return false;
	}

	public function getOauthSecret(){
		$_collection = Mage::getModel('google_oauth/secrets')->getCollection()
		->addFieldToFilter('entity_id', $this->getId())
		->addFieldToFilter('type_id', YTDev_GoogleAuthenticator_Model_Secrets::TYPE_CUSTOMER)
		->addFieldToFilter('enabled', 1);

		if($_collection->getSize()){
			return $_collection->getFirstItem();
		}
		return false;
	}

	public function getOauthCollection(){
		return Mage::getModel('google_oauth/secrets')->getCollection()
		->addFieldToFilter('entity_id', $this->getId())
		->addFieldToFilter('type_id', YTDev_GoogleAuthenticator_Model_Secrets::TYPE_CUSTOMER);
	}

	public function createNewOauth($_secret){
		$_model = Mage::getModel('google_oauth/secrets');
		$_model->setEntityId($this->getId());
		$_model->setEnabled(true);
		$_model->setTypeId(YTDev_GoogleAuthenticator_Model_Secrets::TYPE_CUSTOMER);
		$_model->setSecret($_secret);
		$_model->save();

		$this->setGoogleOauthEnabled(true);
		$this->save();

		Mage::getSingleton('customer/session')->setGoogleAuthDone(true);
	}

	public function disableOauth(){
		if($_secret = $this->getOauthSecret()){
			$_secret->setEnabled(false);
			$_secret->save();
		}

		$this->setGoogleOauthEnabled(false);
		$this->save();

		Mage::getSingleton('customer/session')->setGoogleAuthDone(false);
	}

	public function enableOauth(){
		$_collection = $this->getOauthCollection();
		if($_collection->getSize()){
			$_secret = $_collection->getFirstItem();
			$_secret->setEnabled(true);
			$_secret->save();
		}

		$this->setGoogleOauthEnabled(true);
		$this->save();

		Mage::getSingleton('customer/session')->setGoogleAuthDone(true);
	}
}