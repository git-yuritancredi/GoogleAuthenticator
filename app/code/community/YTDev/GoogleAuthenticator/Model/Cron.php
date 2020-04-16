<?php
class YTDev_GoogleAuthenticator_Model_Cron
{
	public function checkRecovery(){
		$_collection = Mage::getModel('google_oauth/secrets')->getCollection()
		->addFieldToFilter('recovery', array('neq' => null));

		if($_collection->getSize()){
			foreach($_collection as $_secret){
				try{
					$_secret->setSecret(null);
					$_secret->save();
				}catch(Exception $e){
					Mage::helper('google_oauth')->addLog($e->getMessage());
				}
			}
		}
	}
}