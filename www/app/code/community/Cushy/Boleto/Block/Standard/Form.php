<?php
/**
 * Indexa - Pagamento Digital Payment Module
 *
 * @title      Magento -> Custom Payment Module for Pagamento Digital (Brazil)
 * @category   Payment Gateway
 * @package    Indexa_PagamentoDigital
 * @author     Gabriel Zamprogna -> desenvolvimento [at] indexainternet [dot] com  [dot] br
 * @copyright  Copyright (c) 2009 Indexa
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Cushy_Boleto_Block_Standard_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        $this->setTemplate('cushy_boleto/standard/form.phtml');
        parent::_construct();
    }
}
