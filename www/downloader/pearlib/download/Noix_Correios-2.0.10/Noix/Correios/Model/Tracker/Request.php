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

class Noix_Correios_Model_Tracker_Request
{
	
	private function _extrairTable($conteudo)
	{
		preg_match('/<table (.*)>(.*)<\/table>/is', $conteudo, $matches);
		
		return !empty($matches) ? $matches[0] : false;
	}
	
	private function _extraiProgresso($conteudo)
	{
		preg_match_all('/<tr>(.*)<\/tr>/i', $conteudo, $matches, PREG_SET_ORDER);
		
		return !empty($matches) ? $matches : false;
	}
	
	private function _trataDados($conteudo)
	{
		$_table = $this->_extrairTable($conteudo);
		
		if($_table === false){
			return false;
		}
		
		$matches = $this->_extraiProgresso($_table);
		
		if($matches === false){
			return false;
		}
		
		$i = 0;
		$j = 0;
		$_progresso = array();
		foreach($matches as $match){
			$coluna = $match[1];
			
			if($i%2 == 0){
				preg_match('/<td rowspan="?[12]"?>(.*)<\/td><td>(.*)<\/td><td><font color="[A-Z0-9]{6}">(.*)<\/font><\/td>/i', $coluna, $_tds);
				
				if(!$_tds){
					continue;
				}
				
				list($lixo, $data, $localizacao, $status) = $_tds;
				
				$_progresso[$j]['data'] = $data;
				$_progresso[$j]['localizacao'] = $localizacao;
				$_progresso[$j]['status'] = $status;
			}
			else{
				preg_match('/<td colspan="?2"?>(.*)<\/td>/i', $coluna, $_tds);
				
				if(!$_tds){
					continue;
				}
				
				list($lixo, $descricao) = $_tds;
				
				$_progresso[$j]['descricao'] = $descricao;
				
				$j++;
			}
			
			$i++;
		}
		
		return $_progresso;
	}
	
	public function send($tracking)
	{
		$url = 'http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=' . $tracking;
		$client = new Zend_Http_Client($url);
		$content = $client->request();
		
		$body = $content->getBody();
		
		return $this->_trataDados($body);
	}
	
}