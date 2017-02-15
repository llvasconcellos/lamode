<?php

class Noix_Correios_Adminhtml_RelatoriosController extends Mage_Adminhtml_Controller_Action
{
	
	public function calculosfreteAction()
	{
		$this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('noix_correios/adminhtml_relatorios_calculosfrete_grid'));
		$this->renderLayout();
	}
	
}