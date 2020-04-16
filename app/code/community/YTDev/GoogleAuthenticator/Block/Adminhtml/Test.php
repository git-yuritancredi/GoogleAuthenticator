<?php
class YTDev_GoogleAuthenticator_Block_Adminhtml_Test extends Mage_Adminhtml_Block_System_Account_Edit
{
	public function getCurrentUser(){
		return Mage::getSingleton('admin/session')->getUser();
	}

	public function getFormAction(){
		return Mage::helper('adminhtml')->getUrl('adminhtml/google_oauth/validate');
	}
}