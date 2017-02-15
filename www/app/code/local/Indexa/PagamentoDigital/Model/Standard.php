<?php
/**
 * Indexa - Pagamento Digital Payment Module
 *
 * @title      Magento -> Custom Payment Module for Pagamento Digital (Brazil)
 * @category   Payment Gateway
 * @package    Indexa_PagamentoDigital
 * @author     Gabriel Zamprogna -> desenvolvimento [at] indexainternet [dot] com  [dot] br
 * @copyright  Copyright (c) 2010 Indexa
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Indexa_PagamentoDigital_Model_Standard extends Mage_Payment_Model_Method_Abstract
{
    //changing the payment to different from cc payment type and indexa_pagamentodigital payment type
    const PAYMENT_TYPE_AUTH = 'AUTHORIZATION';
    const PAYMENT_TYPE_SALE = 'SALE';

    protected $_code  = 'pagamentodigital_standard';
    protected $_formBlockType = 'pagamentodigital/standard_form';
    protected $_allowCurrencyCode = array('BRL');

     /**
     * Get pagamentodigital session namespace
     *
     * @return Indexa_PagamentoDigital_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('pagamentodigital/session');
    }

    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    /**
     * Using internal pages for input payment data
     *
     * @return bool
     */
    public function canUseInternal()
    {
        return false;
    }

    /**
     * Using for multiple shipping address
     *
     * @return bool
     */
    public function canUseForMultishipping()
    {
        return false;
    }

    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('pagamentodigital/standard_form', $name)
            ->setMethod('pagamentodigital_standard')
            ->setPayment($this->getPayment())
            ->setTemplate('pagamentodigital/standard/form.phtml');

        return $block;
    }

    /*validate the currency code is avaialable to use for indexa_pagamentodigital or not*/
    public function validate()
    {
        parent::validate();
        $currency_code = $this->getQuote()->getBaseCurrencyCode();
        if (!in_array($currency_code,$this->_allowCurrencyCode)) {
            Mage::throwException(Mage::helper('pagamentodigital')->__('A moeda selecionada ('.$currency_code.') não é compatível com o Pagamento Digital'));
        }
        return $this;
    }

	public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
    {
       return $this;
    }

	public function onInvoiceCreate(Mage_Sales_Model_Invoice_Payment $payment)
    {

    }

    public function canCapture()
    {
        return true;
    }

    public function getOrderPlaceRedirectUrl()
    {
          return Mage::getUrl('pagamentodigital/standard/redirect', array('_secure' => true));
    }

	function getNumEndereco($endereco) {
    	$numEndereco = '';

    	$posSeparador = $this->getPosSeparador($endereco, false);
    	if ($posSeparador !== false)
		    $numEndereco = trim(substr($endereco, $posSeparador + 1));

      	$posComplemento = $this->getPosSeparador($numEndereco, true);
		if ($posComplemento !== false)
		    $numEndereco = trim(substr($numEndereco, 0, $posComplemento));

		if ($numEndereco == '')
		    $numEndereco = '?';

		return($numEndereco);
	}

	function getPosSeparador($endereco, $procuraEspaco = false) {
		$posSeparador = strpos($endereco, ',');
		if ($posSeparador === false)
			$posSeparador = strpos($endereco, '-');

		if ($procuraEspaco)
			if ($posSeparador === false)
				$posSeparador = strrpos($endereco, ' ');

		return($posSeparador);
	}

	public function getStandardCheckoutFormFields() {

		$orderIncrementId = $this->getCheckout()->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);

        $isOrderVirtual = $order->getIsVirtual();
        $a = $isOrderVirtual ? $order->getBillingAddress() : $order->getShippingAddress();

        $currency_code = $order->getBaseCurrencyCode();

        list($items, $totals, $discountAmount, $shippingAmount) = Mage::helper('pagamentodigital')->prepareLineItems($order, false, false);

		$postal_code = trim(str_replace("-", "", $a->getPostcode()));

      	$sArr = array(
            'email_loja'        => $this->getConfigData('emailID'),
            'tipo_integracao'   => "PAD",
    	    'id_pedido'     	=> $orderIncrementId,
            'nome'      		=> $a->getFirstname().' '.str_replace(" (pj)", "", $a->getLastname()),
            'cep'       		=> $postal_code,
            'endereco'       	=> $a->getStreet(1),
            'complemento'     	=> $a->getStreet(2),
            'bairro'    		=> "",
            'cidade'    		=> $a->getCity(),
            'estado'        	=> $a->getRegionCode(),
            'pais'      		=> $a->getCountry(),
            'telefone'       	=> substr(str_replace(" ","",str_replace("(","",str_replace(")","",str_replace("-","",$a->getTelephone())))),0,2) . substr(str_replace(" ","",str_replace("-","",$a->getTelephone())),-8),
            'email'    			=> $a->getEmail(),
        );

        if ($items) {
            $i = 1;
            foreach($items as $item) {
            	//if ($item->getParentItem()) continue;
            	if ($item->getAmount()>0) {

					$valorProduto = sprintf('%.2f',$item->getAmount());

					$sArr = array_merge($sArr, array(
	                    'produto_descricao_'.$i => $item->getName(),
	                    'produto_codigo_'.$i    => $item->getId(),
	                    'produto_qtde_'.$i   	=> $item->getQty(),
	                    'produto_valor_'.$i  	=> $valorProduto,
				    ));
            	}

			    // @todo Indexa - caso utilize imposto
            	$i++;
            }
            // Indexa Fix 18-10-2010
            $sArr["desconto"] = is_numeric( $discountAmount ) ? sprintf('%.2f',$discountAmount) : 0;
        }        

        $totalArr = $order->getBaseGrandTotal();

		$shipping = sprintf('%.2f',$shippingAmount);

    	$sArr = array_merge($sArr, array('frete' => $shipping));

		if ($this->getConfigData('retorno') != '') {
	    	$sArr = array_merge($sArr, array('url_retorno' => $this->getConfigData('retorno')));
	    	$sArr = array_merge($sArr, array('redirect' => 'true'));
		}

        $sReq = '';
        $rArr = array();
        foreach ($sArr as $k=>$v) {
            /*
            replacing & char with and. otherwise it will break the post
            */
            $value =  str_replace("&","and",$v);
            $rArr[$k] =  $value;
            $sReq .= '&'.$k.'='.$value;
        }

        if ($this->getDebug() && $sReq) {
            $sReq = substr($sReq, 1);
            $debug = Mage::getModel('pagamentodigital/api_debug')
                    ->setApiEndpoint($this->getPagamentoDigitalUrl())
                    ->setRequestBody($sReq)
                    ->save();
        }

        return $rArr;
    }

    //  define a url do pagamentodigital
    public function getPagamentoDigitalUrl()
    {
         $url='https://www.pagamentodigital.com.br/checkout/pay/';
         return $url;
    }

    // @todo Indexa: esta funcao eh inutil, por enquanto
	public function getDebug()
    {
        return Mage::getStoreConfig('pagamentodigital/wps/debug_flag');
    }
}