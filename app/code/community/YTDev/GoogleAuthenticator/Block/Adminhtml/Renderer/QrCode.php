<?php
class YTDev_GoogleAuthenticator_Block_Adminhtml_Renderer_QrCode extends Varien_Data_Form_Element_Abstract
{
	public function getElementHtml(){

		$html = '<img src="'.$this->getValue().'">';

		if($this->getComment()){
			$html .= '<ul class="messages"><li class="notice-msg"><ul><li><span>'.$this->getComment().'</span></li></ul></li></ul>';
		}

        return $html;
    }
}