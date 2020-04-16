<?php
class YTDev_GoogleAuthenticator_Adminhtml_Google_OauthController extends Mage_Adminhtml_Controller_Action
{
	public function validateAction(){
		if($_params = $this->getRequest()->getParams()){
			$_helper	= Mage::helper('google_oauth/authenticator');
			$_config 	= Mage::helper('google_oauth');
			$_user		= $this->getCurrentUser();
			$_secret	= $_user->getOauthSecret();

			if($_helper->verifyCode($_secret->getSecret(), $_params['code']) || $_secret->getRecovery() == $_params['code']){
				Mage::getSingleton('admin/session')->setGoogleAuthDone(true);
				if($_secret->getRecovery()){
					$_secret->setRecovery(null)->save();
				}
				if(isset($_params['account'])){
					Mage::getSingleton('adminhtml/session')->addSuccess($_config->__("Valid code"));
				}else{
					Mage::getSingleton('adminhtml/session')->addSuccess($_config->__("Welcome %s", $_user->getName()));
				}
			}else{
				Mage::getSingleton('adminhtml/session')->addError($_config->__("Invalid code"));
			}

			if(isset($_params['account'])){
				return $this->_redirect('adminhtml/system_account/index');
			}
		}
		return $this->_redirect('/');
	}

	public function authenticateAction(){
		$_session = Mage::getSingleton('admin/session');
		if(!$_session->isLoggedIn()){
			return $this->_redirect('/');
		}

		$_user = $this->getCurrentUser();
		if(!$_user->isGoogleOauthEnabled() || $_session->getGoogleAuthDone()){
			return $this->_redirect('/');
		}

		$this->loadLayout();
		$this->renderLayout();
	}

	public function recoveryAction(){
		$_config = Mage::helper('google_oauth');
		try{
			$_user 		= $this->getCurrentUser();
			$_secret 	= $_user->getOauthSecret();
			$_secret->recovery();
			Mage::getSingleton('adminhtml/session')->addSuccess($_config->__("Check your email address, and insert your recovery code."));
		}catch(Exception $e){
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}
		return $this->_redirect('adminhtml/google_oauth/authenticate');
	}

	private function getCurrentUser(){
		return Mage::getSingleton('admin/session')->getUser();
	}
}