<?php
class YTDev_GoogleAuthenticator_IndexController extends Mage_Core_Controller_Front_Action
{
	public function manageAction(){
		$_currentCustomer = $this->getCurrentCustomer();
		if(!$_currentCustomer->getId()){
			$this->_redirect('/');
		}

		$this->loadLayout();
		$this->renderLayout();
	}

	public function saveAction(){
		$_helper 			= Mage::helper('google_oauth/authenticator');
		$_config 			= Mage::helper('google_oauth');
		$_currentCustomer 	= $this->getCurrentCustomer();
		if($_currentCustomer->getId()){
			if($_enabled = $this->getRequest()->getParam('enabled')){
				$_check = $_currentCustomer->getOauthCollection();
				if($_check->getSize()){
					$_secret = $_check->getFirstItem();
					if(!$_secret->getEnabled()){
						try{
							$_currentCustomer->enableOauth();
							Mage::getSingleton("core/session")->addSuccess($_config->__("Two factor authentication enabled."));
						}catch(Exception $e){
							Mage::getSingleton("core/session")->addError($_config->__("Unknown error: ".$e->getMessage()));
						}
					}
				}else{
					try{
						$_secret = $_helper->createSecret($_config->getSecretLength());
						$_currentCustomer->createNewOauth($_secret);
						Mage::getSingleton("core/session")->addSuccess($_config->__("Two factor authentication enabled."));
					}catch(Exception $e){
						Mage::getSingleton("core/session")->addError($_config->__("Unknown error: ".$e->getMessage()));
					}
				}
			}else{
				$_check = $_currentCustomer->getOauthCollection();
				if($_check->getSize()){
					$_secret = $_check->getFirstItem();
					if($_secret->getEnabled()){
						try{
							$_currentCustomer->disableOauth();
							Mage::getSingleton("core/session")->addSuccess($_config->__("Two factor authentication disabled."));
						}catch(Exception $e){
							Mage::getSingleton("core/session")->addError($_config->__("Unknown error: ".$e->getMessage()));
						}
					}
				}
			}
		}

		return $this->_redirect('google_oauth/index/manage');
	}

	public function validateAction(){
		$_helper = Mage::helper('google_oauth/authenticator');
		$_config = Mage::helper('google_oauth');

		if($_code = $this->getRequest()->getParam('code')){
			$_customer 	= $this->getCurrentCustomer();
			if($_secret = $_customer->getOauthSecret()){
				try{
					if($_helper->verifyCode($_secret->getSecret(), $_code) || $_secret->getRecovery() == $_code){
						if($_secret->getRecovery()){
							$_secret->setRecovery(null)->save();
						}
						Mage::getSingleton('customer/session')->setGoogleAuthDone(true);
						if($this->getRequest()->getParam('account')){
							Mage::getSingleton("core/session")->addSuccess($_config->__("Valid code"));
						}
					}else{
						Mage::getSingleton("core/session")->addError($_config->__("Invalid code"));
					}
				}catch(Exception $e){
					Mage::getSingleton("core/session")->addError($_config->__("Unknown error: ".$e->getMessage()));
				}
			}else{
				Mage::getSingleton("core/session")->addError($_config->__("Unable to verify code"));
			}
		}
		if($this->getRequest()->getParam('account')){
			return $this->_redirect('google_oauth/index/manage');
		}
		return $this->_redirect('customer/account/loginPost');
	}

	public function authenticateAction(){
		$_session = Mage::getSingleton('customer/session');
		if(!$_session->isLoggedIn()){
			return $this->_redirect('/');
		}
		$_customer = $this->getCurrentCustomer();

		if(!$_customer->isGoogleOauthEnabled() || $_session->getGoogleAuthDone()){
			return $this->_redirect('customer/account/loginPost');
		}

		$this->loadLayout();
		$this->renderLayout();
	}

	public function recoveryAction(){
		$_config = Mage::helper('google_oauth');
		try{
			$_user 		= $this->getCurrentCustomer();
			$_secret 	= $_user->getOauthSecret();
			$_secret->recovery();
			Mage::getSingleton('core/session')->addSuccess($_config->__("Check your email address, and insert your recovery code."));
		}catch(Exception $e){
			Mage::getSingleton('core/session')->addError($e->getMessage());
		}
		return $this->_redirect('google_oauth/index/authenticate');
	}

	private function getCurrentCustomer(){
		return Mage::getSingleton('customer/session')->getCustomer();
	}
}