<?php
class YTDev_GoogleAuthenticator_Controller_Router
{

	public function checkOauth($observer){

		$currentUrl = Mage::helper('core/url')->getCurrentUrl();
		$url 		= Mage::getSingleton('core/url')->parseUrl($currentUrl);
		$request 	= $url->getPath();

		if(Mage::app()->getStore()->isAdmin()){
			$_session = Mage::getSingleton('admin/session');
			if($_session->isLoggedIn()){
				$_user 				= $_session->getUser();
				$requestPathInfo 	= Mage::app()->getFrontController()->getAction()->getFullActionName();
				$isPathCorrect 		= $requestPathInfo != 'adminhtml_google_oauth_authenticate' && $requestPathInfo != 'adminhtml_google_oauth_validate' && $requestPathInfo != 'adminhtml_google_oauth_recovery';
				if($_user->isGoogleOauthEnabled() && $isPathCorrect && !$_session->getGoogleAuthDone()){
					Mage::app()->getFrontController()->getResponse()
						->setRedirect(Mage::helper('adminhtml')->getUrl('adminhtml/google_oauth/authenticate'))
						->sendResponse();
					exit;
				}
			}
		}else{
			$_session = Mage::getSingleton('customer/session');
			if($_session->isLoggedIn()){
				$_customer = $_session->getCustomer();
				$requestPathInfo = trim($request, '/');
				$isPathCorrect = $requestPathInfo != 'google_oauth/index/authenticate' && $requestPathInfo != 'google_oauth/index/validate';
				if($_customer->isGoogleOauthEnabled() && $isPathCorrect && !$_session->getGoogleAuthDone()){
					Mage::app()->getFrontController()->getResponse()
						->setRedirect(Mage::getUrl('google_oauth/index/authenticate'))
						->sendResponse();
					exit;
				}
			}
		}
	}
}