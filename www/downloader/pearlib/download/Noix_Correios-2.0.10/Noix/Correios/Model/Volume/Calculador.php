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
 
class Noix_Correios_Model_Volume_Calculador
{
	
	const DEFAULT_ALTURA = 2;
	
	const DEFAULT_LARGURA = 11;
	
	const DEFAULT_COMPRIMENTO = 16;
	
	private $_code;
	
	private $_store;	
	
	private $_request;
	
	public function setCode($code)
	{
		$this->_code = $code;
	}
	
	public function setStore($store)
	{
		$this->_store = $store;
	}
	
	public function setRequest($request)
	{
		$this->_request = $request;
	}
	
	public function getConfigData($field)
    {
        $path = 'carriers/'.$this->_code.'/'.$field;
        return Mage::getStoreConfig($path, $this->_store);
    }
    
    public function getVolume()
    {
    	$_atributoAltura = $this->getConfigData('nomeatributoaltura');
        $_atributoLargura = $this->getConfigData('nomeatributolargura');
        $_atributoComprimento = $this->getConfigData('nomeatributocomprimento');
        
        $_itens = Mage::getSingleton('checkout/session')->getQuote()->getItemsCollection()->getItems();
        
        $pesoCubicoTotal = 0;
        $volumeTotal = 0;
        foreach($_itens as $item){
        	$produto = Mage::getModel('catalog/product')->load($item->getProductId());
        	
        	$_altura = ($_atributoAltura && $produto->getData($_atributoAltura)) ? $produto->getData($_atributoAltura) : self::DEFAULT_ALTURA;
        	$_largura = ($_atributoLargura && $produto->getData($_atributoLargura)) ? $produto->getData($_atributoLargura) : self::DEFAULT_LARGURA;
        	$_comprimento = ($_atributoComprimento && $produto->getData($_atributoComprimento)) ? $produto->getData($_atributoComprimento) : self::DEFAULT_COMPRIMENTO;

        	$_calcPesoCubico = (($_altura * $_largura * $_comprimento)/4800) * $item->getQty();
        	
        	$pesoCubicoTotal += $_calcPesoCubico;
        	$volumeTotal += ($_altura * $_largura * $_comprimento) * $item->getQty();
        }
        
        $pesoTotal = $this->_request->getPackageWeight();
        
        if($pesoCubicoTotal > $pesoTotal){
        	$mediaVolume = round(pow((int)$volumeTotal, (1/3)));
        	
			$altura = (($mediaVolume < self::DEFAULT_ALTURA) ? self::DEFAULT_ALTURA : $mediaVolume);
			$largura = (($mediaVolume < self::DEFAULT_LARGURA) ? self::DEFAULT_LARGURA : $mediaVolume);
			$comprimento = (($mediaVolume < self::DEFAULT_COMPRIMENTO) ? self::DEFAULT_COMPRIMENTO : $mediaVolume);
        }
        else{
        	$altura = self::DEFAULT_ALTURA;
        	$largura = self::DEFAULT_LARGURA;
        	$comprimento = self::DEFAULT_COMPRIMENTO;
        }
        
        return array($altura, $largura, $comprimento);
        
    }
	
}