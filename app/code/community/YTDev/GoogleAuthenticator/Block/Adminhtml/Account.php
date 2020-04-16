<?php
class YTDev_GoogleAuthenticator_Block_Adminhtml_Account extends Mage_Adminhtml_Block_System_Account_Edit_Form
{
	protected function _prepareForm(){

		$_parent = parent::_prepareForm();

		$_config 	= Mage::helper('google_oauth');
		$_helper	= Mage::helper('google_oauth/authenticator');

		if($_config->isAdminEnabled()){

			$form 	= $this->getForm();
			$email	= Mage::getStoreConfig('trans_email/ident_general/email');

			if($_emailField = $form->getElement('email')){
				$email = $_emailField->getValue() ? $_emailField->getValue() : $email;
			}

			$_user = new Varien_Object();
			if($_idField = $form->getElement('user_id')){
				$_user = Mage::getModel('admin/user')->load($_idField->getValue());
			}

			$_secret = new Varien_Object();
			$_secret->setEnabled(0);

			if($_user->getId()){
				if($_user->isGoogleOauthEnabled()){
					$_secret = $_user->getOauthSecret();
				}else{
					$_secrets = $_user->getOauthCollection();
					if($_secrets->getSize()){
						$_secret = $_secrets->getFirstItem();
					}
				}
			}

			$fieldset = $form->addFieldset('oauth_fieldset', array('legend' => $_config->__('Two-Factor Authentication')));

			$fieldset->addField('google_oauth_enabled', 'select', array(
					'name' 		=> 'google_oauth_enabled',
					'label' 	=> $_config->__('Enabled'),
					'title' 	=> $_config->__('Enabled'),
					'required' 	=> true,
					'options' 	=> Mage::getSingleton('adminhtml/system_config_source_yesno')->toArray(),
					'value'		=> $_secret->getEnabled()
				)
			);

			if($_secret->getEnabled()){
				$fieldset->addType('google_oauth_qrcode', 'YTDev_GoogleAuthenticator_Block_Adminhtml_Renderer_QrCode');
				$fieldset->addField('qrcode', 'google_oauth_qrcode', array(
					'name' 		=> 'qrcode',
					'label' 	=> $_config->__('QR Code'),
					'comment' 	=> $_config->__("Scan this code with your 'Authenticator' app"),
					'value' 	=> $_helper->getQRCodeGoogleUrl($email, $_secret->getSecret(), $_config->getCodeName())
				));
			}
		}

		return $_parent;
	}
}