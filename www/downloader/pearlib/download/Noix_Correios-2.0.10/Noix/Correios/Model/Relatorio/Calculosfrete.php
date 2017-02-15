<?php
/**
 * NOIX Internet
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL).
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @package    Noix_Correios
 * @copyright  Copyright (c) 2009 NOIX Internet [ magento@noixinternet.com.br ]
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Noix_Correios_Model_Relatorio_Calculosfrete extends Mage_Core_Model_Abstract
{
	
	const STATUS_SUCESSO = 'sucesso';
	
	const STATUS_ERRO = 'erro';
	
	public function _construct()
	{
		$this->_init('noix_correios/relatorio_calculosfrete');
	}
	
}