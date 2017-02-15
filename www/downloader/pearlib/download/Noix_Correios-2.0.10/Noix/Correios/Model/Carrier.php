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
 
class Noix_Correios_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{

    protected $_code = 'noix_correios';

    protected $_desconto;
    
    private $_rules;

    public function getAllowedMethods()
    {
        return array($this->_code => $this->getConfigData('title'));
    }
    
    public function setDesconto($desconto)
    {
    	$this->_desconto = $desconto;
    }

    public function collectRates(Mage_Shipping_Model_Rate_Request $rateRequest)
    {
        $cepOrigem = $this->trataCep(Mage::getStoreConfig('shipping/origin/postcode', $this->getStore()));
        $cepDestino = $this->trataCep($rateRequest->getDestPostcode());
        
        if(!$this->estaAtivo()){
        	$relatorioCalculosFrete = Mage::getModel('noix_correios/relatorio_calculosfrete')
        						->setDataCalculo(date('Y-m-d H:i:s'))
        						->setCepDestino($cepDestino)
        						->setMensagemErro('O módulo não está ativo')
            					->setStatus(Noix_Correios_Model_Relatorio_Calculosfrete::STATUS_ERRO)
            					->save();
        	
            return false;
        }

        $idPaisDestino = $rateRequest->getDestCountryId();

        if(!$this->lojaEstaNoBrasil() || !$this->estaNoBrasil($idPaisDestino)){
        	
        	$msgErro = !$this->lojaEstaNoBrasil() ? 'O país configurado para a loja não é o Brasil' : 
        													'O país informado pelo usuário não é o Brasil';
        	
        	$relatorioCalculosFrete = Mage::getModel('noix_correios/relatorio_calculosfrete')
        						->setDataCalculo(date('Y-m-d H:i:s'))
        						->setCepDestino($cepDestino)
        						->setMensagemErro($msgErro)
            					->setStatus(Noix_Correios_Model_Relatorio_Calculosfrete::STATUS_ERRO)
            					->save();
            return false;
        }

        $valorEncomenda = $rateRequest->getBaseCurrency()->convert($rateRequest->getPackageValue(), $rateRequest->getPackageCurrency());

        $codigoAdministrativo = $this->getConfigData('codigoadministrativo');
        $senha = $this->getConfigData('senha');

        $peso = $rateRequest->getPackageWeight();

        if(!$this->validaCep($cepOrigem) || !$this->validaCep($cepDestino)){
        	
        	$msgErro = !$this->validaCep($cepOrigem) ? 'CEP de origem inválido' : 'CEP de destino inválido';
        	
        	$relatorioCalculosFrete = Mage::getModel('noix_correios/relatorio_calculosfrete')
        						->setDataCalculo(date('Y-m-d H:i:s'))
        						->setCepDestino($cepDestino)
        						->setMensagemErro($msgErro)
            					->setStatus(Noix_Correios_Model_Relatorio_Calculosfrete::STATUS_ERRO)
            					->save();
            return false;
        }

        $tiposFrete = explode(',', $this->getConfigData('tiposdefrete'));

        $request = Mage::getModel('noix_correios/carrier_request');

        $request->setCepOrigem($cepOrigem);
        $request->setCepDestino($cepDestino);
        $request->setValorEncomenda($valorEncomenda);

        $request->setCodigoEmpresa($codigoAdministrativo);
        $request->setSenhaEmpresa($senha);

        $request->setPeso($peso);

        $_calculador = Mage::getModel('noix_correios/volume_calculador');
        $_calculador->setCode($this->_code);
        $_calculador->setStore($this->getStore());
        $_calculador->setRequest($rateRequest);
        
        list($altura, $largura, $comprimento) = $_calculador->getVolume();
        
        $request->setAltura($altura);
        $request->setLargura($largura);
        $request->setComprimento($comprimento);
        $request->setDiametro(0);

        /**
         * Serviços adicionais dos Correios. Deve ser implementado
         * de acordo com sua logística.
         */
        $request->setMaoPropria('N');
        $request->setAvisoRecebimento('N');
        $request->setValorDeclarado(0); // Esta opção é obrigatória para o serviço Sedex a Cobrar

        $request->setFormatoEmbalagem($this->getFormatoEmbalagem());

        $request->addServico($tiposFrete);
        
        // coleta regras de promoção
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $_appliedRulesIds = explode(',', $quote->getAppliedRuleIds());
        
        $_allRules = Mage::getResourceModel('salesrule/rule_collection')->load();
        
        $usedRules = array();
        $_temFreteFixo = false;
        $_valorFreteFixo = 0;
        $_labelFreteFixo = '';
        foreach($_allRules as $_rule){
        	if(in_array($_rule->getId(), $_appliedRulesIds)){
        		$usedRules[] = $_rule;
        		
        		if($_rule->getNoixCorreiosTipoDesconto() == 'fixo'){
        			$_temFreteFixo = true;
			        $_valorFreteFixo = $_rule->getNoixCorreiosDesconto();
			        $_labelFreteFixo = $_rule->getNoixCorreiosLabelFixo();
			        
			        break;
        		}
        	}
        }
        
        $this->_rules = $usedRules;

        $rateResult = Mage::getModel('shipping/rate_result');

        if($_temFreteFixo){
        	$method = Mage::getModel('shipping/rate_result_method');
	
			$method->setCarrier($this->_code);
			$method->setCarrierTitle($this->getTitle());
			
			$method->setMethod('fixo');
			
			$method->setMethodTitle($_labelFreteFixo);
			
			$method->setPrice($_valorFreteFixo);
			$method->setCost($_valorFreteFixo);
			
			$rateResult->append($method);
        }
        else{        
	        $servicos = $request->send();
	
	        if($servicos !== false){
	             foreach($servicos->cServico as $servico){
	                 if($servico->Erro == 0){
	                     $cod = (string)$servico->Codigo;
	
	                     $method = Mage::getModel('shipping/rate_result_method');
	
	                     $method->setCarrier($this->_code);
	                     $method->setCarrierTitle($this->getTitle());
	
	                     $method->setMethod($cod);
	
	                     $methodTitle = Noix_Correios_Model_Carrier_Request::$labels[$cod];
	
	                     if($this->mostrarPrazoEntrega()){
	                         $methodTitle .= sprintf($this->getTextoPrazoEntrega(), (int)$servico->PrazoEntrega + (int)$this->getAcrescimoPrazoEntrega());
	                     }
	
	                     $method->setMethodTitle($methodTitle);
	                     
	                     $_valor = str_replace(',', '.', $servico->Valor);
	                     
	                     $manuseio = $this->calculaManuseio($_valor);
	                     $valor = $_valor+$manuseio;
	                     $valor = $this->getValorSobPromocoes($valor);
	
	                     $method->setPrice($valor);
	                     $method->setCost($valor);
	
	                     $rateResult->append($method);
	                 }
	             }
	        }
	        else{
	            $error = Mage::getModel('shipping/rate_result_error');
	
	            $error->setCarrier($this->_code);
	            $error->setCarrierTitle($this->getTitle());
	            $error->setErrorMessage($this->getMensagemDeErro());
	
	            $rateResult->append($error);
	
	            return $rateResult;
	        }
        }

        return $rateResult;
    }
    
    private function getValorSobPromocoes($valor)
    {
    	foreach($this->_rules as $rule){
    		switch($rule->getNoixCorreiosTipoDesconto()){
    			
    			case 'normal':
    				$valor -= $rule->getNoixCorreiosDesconto();
    				break;
    				
    			case 'porcentagem':
    				$desconto = $valor*($rule->getNoixCorreiosDesconto()/100);
    				$valor -= $desconto;
    				break;    			
    		}			
			
    	}
    	
    	return $valor;
    }

    public function calculaManuseio($valor)
    {
        if(strpos($this->getTaxaManuseio(), '%') !== false){
             $manuseio = str_replace('%', '', $this->getTaxaManuseio());
             return $valor*($manuseio/100);
        }

         return $this->getTaxaManuseio();
    }
      
    public function isTrackingAvailable()
    {
        return true;
    }
    
    private function _getArrayDeliveryDateTime($data)
    {
    	$brLocale = new Zend_Locale('pt_BR');
            
		list($_deliveryDate, $_deliveryTime) = explode(' ', $data);

		$objDeliveryDate = new Zend_Date($_deliveryDate, 'dd/MM/YYYY', $brLocale);
		$_deliveryDate = $objDeliveryDate->toString('YYYY-MM-dd');
		
		return array($_deliveryDate, $_deliveryTime);
    }
    
    public function getTrackingInfo($tracking)
    {
		$request = Mage::getModel('noix_correios/tracker_request');
		$progresso = $request->send($tracking);
		
		if($progresso !== false){		
			$_progressDetail = array();
			
			foreach($progresso as $prog){
				list($_deliveryDate, $_deliveryTime) = $this->_getArrayDeliveryDateTime($prog['data']);		
				            	
				$descricao = $prog['status'];
				$descricao .= isset($prog['descricao']) ? ' - ' . utf8_encode($prog['descricao']) : '';
				
				$_progressDetail[] = array(
					'deliverydate' => $_deliveryDate,
					'deliverytime' => $_deliveryTime,
					'deliverylocation' => $prog['localizacao'],
					'activity' => $descricao
				);
			}
			
			$trackProgress = array(
				'progressdetail' => $_progressDetail
			);
			
			$status = Mage::getModel('shipping/tracking_result_status');
			            
			$status->setCarrier($this->_code);
			$status->setCarrierTitle($this->getConfigData('title'));
			$status->setTracking($tracking);           
			$status->addData($trackProgress);
			$status->setPopup(1);
			$status->setUrl("http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI={$tracking}");
			
			return $status;
		}
		else{
			$error = Mage::getModel('shipping/tracking_result_error');
			
			$error->setCarrier($this->_code);
			$error->setCarrierTitle($this->getConfigData('title'));
			$error->setTracking($tracking);
			
			return $error;
		}
    }

    /*
     * Métodos próprios do model
     */

    private function validaCep($cep)
    {
        if(!preg_match('/^\d{8}$/', $cep)){
            return false;
        }

        return true;
    }

    public function estaAtivo()
    {
        return $this->getConfigFlag('active') == 1;
    }

    public function mostrarPrazoEntrega()
    {
        return $this->getConfigFlag('mostrarprazoentrega') == 1;
    }

    public function getTextoPrazoEntrega()
    {
        return $this->getConfigData('textoprazoentrega');
    }

    public function getTaxaManuseio()
    {
        return $this->getConfigData('taxamanuseio');
    }

    public function getAcrescimoPrazoEntrega()
    {
        return $this->getConfigData('acrescimoprazoentrega');
    }

    public function getTitulo()
    {
        return $this->getConfigData('title');
    }

    public function getFormatoEmbalagem()
    {
        return $this->getConfigData('formato');
    }

    public function getMensagemDeErro()
    {
        return $this->getConfigData('msgerro');
    }

    public function lojaEstaNoBrasil()
    {
        $idPais = Mage::getStoreConfig('shipping/origin/country_id', $this->getStore());

        return $this->estaNoBrasil($idPais);
    }

    public function estaNoBrasil($idPais)
    {
        return $idPais == 'BR';
    }

    public function trataCep($cep)
    {
        return preg_replace('/\D/', '', $cep);
    }

}