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

class Noix_Correios_Model_Carrier_Request
{

    const SERVICO_PAC_SEM_CONTRATO = 41106;

    const SERVICO_PAC_COM_CONTRATO = 41068;

    const SERVICO_SEDEX_SEM_CONTRATO = 40010;

    const SERVICO_SEDEX_COM_CONTRATO1 = 40096;

    const SERVICO_SEDEX_COM_CONTRATO2 = 40436;

    const SERVICO_SEDEX_COM_CONTRATO3 = 40444;

    const SERVICO_ESEDEX_COM_CONTRATO = 81019;

    const SERVICO_SEDEX_A_COBRAR_SEM_CONTRATO = 40045;

    const SERVICO_SEDEX_10_SEM_CONTRATO = 40215;

    const SERVICO_SEDEX_HOJE_SEM_CONTRATO = 40290;

    //const URL = 'http://shopping.correios.com.br/wbm/shopping/script/CalcPrecoPrazo.aspx';
    const URL = 'http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx';

    /**
     * Contém os labels que serão exibidos quando
     * o frete for calculado.
     * 
     * @var array
     */
    public static $labels;

    /**
     * Cep de destino sem traço
     *
     * @var integer
     */
    private $_cepDestino;

    /**
     * Cep de origem sem traço
     *
     * @var integer
     */
    private $_cepOrigem;

    /**
     * Código do contraro dado pelos correios
     *
     * @var integer
     */
    private $_codigoEmpresa;

    /**
     * Senha do contrato dado pelos correios
     *
     * @var integer
     */
    private $_senhaEmpresa;

    /**
     * Valor total da encomenda
     * 
     * @var float
     */
    private $_valorEncomenda;

    /**
     * Soma dos pesos dos produtos
     * 
     * @var float
     */
    private $_peso;

    /**
     * Contém o código dos serviços consultados.
     * Eles são selecionados na área administrativa do Magento.
     * 
     * @var array
     */
    private $_servicos = array();

    /**
     * Comprimento da encomenda
     *
     * @var integer
     */
    private $_comprimento;

    /**
     * Altura da encomenda
     * 
     * @var integer
     */
    private $_altura;

    /**
     * Largura da encomenda
     * 
     * @var integer
     */
    private $_largura;

    /**
     * Diâmetro da encomenda
     * 
     * @var integer
     */
    private $_diametro;

    /**
     * Formato da embalagem selecionado na área administrativa do Magento.
     * 
     * @var integer
     */
    private $_formatoEmbalagem;

    /**
     * Informa se foi escolhido o serviço Mão Própria dos Correios.
     *
     * Informar 'S' ou 'N'
     *
     * @var string
     */
    private $_maoPropria;

    /**
     * Informa se foi escolhido o serviço Aviso de Recebimento dos Correios.
     *
     * Informar 'S' ou 'N'
     *
     * @var string
     */
    private $_avisoRecebimento;

    /**
     * Informa se foi escolhido o serviço Valor Declarado dos Correios.
     *
     * Informar 'S' ou 'N'
     *
     * @var string
     */
    private $_valorDeclarado;

    public function __construct()
    {
        $this->_createLabels();
    }

    private function _createLabels()
    {
        self::$labels = array(
            self::SERVICO_PAC_SEM_CONTRATO => Mage::helper('adminhtml')->__('PAC' ),
            self::SERVICO_PAC_COM_CONTRATO => Mage::helper('adminhtml')->__('PAC' ),
            self::SERVICO_SEDEX_SEM_CONTRATO => Mage::helper('adminhtml')->__('Sedex' ),
            self::SERVICO_SEDEX_COM_CONTRATO1 => Mage::helper('adminhtml')->__('Sedex' ),
            self::SERVICO_SEDEX_COM_CONTRATO2 => Mage::helper('adminhtml')->__('Sedex' ),
            self::SERVICO_SEDEX_COM_CONTRATO3 => Mage::helper('adminhtml')->__('Sedex' ),
            self::SERVICO_ESEDEX_COM_CONTRATO => Mage::helper('adminhtml')->__('E-Sedex' ),
            self::SERVICO_SEDEX_A_COBRAR_SEM_CONTRATO => Mage::helper('adminhtml')->__('Sedex a cobrar' ),
            self::SERVICO_SEDEX_10_SEM_CONTRATO => Mage::helper('adminhtml')->__('Sedex 10' ),
            self::SERVICO_SEDEX_HOJE_SEM_CONTRATO => Mage::helper('adminhtml')->__('Sedex Hoje' )
        );
    }

    public function setCepDestino($cep)
    {
        $this->_cepDestino = $cep;
    }

    public function setCepOrigem($cep)
    {
        $this->_cepOrigem = $cep;
    }

    public function setCodigoEmpresa($cod)
    {
        $this->_codigoEmpresa = $cod;
    }

    public function setSenhaEmpresa($senha)
    {
        $this->_senhaEmpresa = $senha;
    }

    public function setValorEncomenda($valor)
    {
        $this->_valorEncomenda = $valor;
    }

    public function setPeso($peso)
    {
        $this->_peso = $peso;
    }

    public function setComprimento($comprimento)
    {
        $this->_comprimento = $comprimento;
    }

    public function setLargura($largura)
    {
        $this->_largura = $largura;
    }

    public function setAltura($altura)
    {
        $this->_altura = $altura;
    }

    public function setDiametro($diametro)
    {
        $this->_diametro = $diametro;
    }

    public function setFormatoEmbalagem($formato)
    {
        $this->_formatoEmbalagem = $formato;
    }

    /**
     * Informa se haverá o serviço Mão Própria dos Correios.
     *
     * @param string $maoPropria Informar 'S' ou 'N'
     */
    public function setMaoPropria($maoPropria)
    {
        $this->_maoPropria = $maoPropria;
    }

    /**
     * Informa se haverá o serviço Aviso de Recebimento
     *
     * @param string $aviso Informar 'S' ou 'N'
     */
    public function setAvisoRecebimento($aviso)
    {
        $this->_avisoRecebimento = $aviso;
    }

    /**
     * Se for optado por usar o serviço de valor declarado
     * aqui deve ser passado o valor. Caso contrário passe
     * sempre 0
     * 
     * @param float $valor
     */
    public function setValorDeclarado($valor)
    {
        $this->_valorDeclarado = $valor;
    }

    public function addServico($servico)
    {
        if(is_array($servico)){
            foreach($servico as $s){
                $this->_servicos[] = $s;
            }
        }
        else if(is_string($servico)){
            $this->_servicos[] = $servico;
        }
    }

    private function _buildParams()
    {
        $params = array(
            'sCepOrigem' => $this->_cepOrigem,
            'sCepDestino' => $this->_cepDestino,
            'nCdServico' => implode(',', $this->_servicos),
            'nCdFormato' => $this->_formatoEmbalagem,
            'nVlPeso' => $this->_peso,
            'sCdMaoPropria' => $this->_maoPropria,
            'nVlValorDeclarado' => $this->_valorDeclarado,
            'sCdAvisoRecebimento' => $this->_avisoRecebimento,
            'StrRetorno' => 'xml',
            'nVlComprimento' => $this->_comprimento,
            'nVlAltura' => $this->_altura,
            'nVlLargura' => $this->_largura,
            'nVlDiametro' => $this->_diametro
        );

        if(isset($this->_codigoEmpresa) && isset($this->_senhaEmpresa)){
            $params['nCdEmpresa'] = $this->_codigoEmpresa;
            $params['sDsSenha'] = $this->_senhaEmpresa;
        }

        $p = array();
        foreach($params as $k => $v){
            $p[] = "{$k}={$v}";
        }

        return implode('&', $p);
    }

    public function send()
    {
        $relatorioCalculosFrete = Mage::getModel('noix_correios/relatorio_calculosfrete');
        
    	$params = $this->_buildParams();
        
        $url = self::URL . '?' . $params;
        
        $relatorioCalculosFrete->setCepDestino($this->_cepDestino)
        					->setDataCalculo(date('Y-m-d H:i:s'))
        					->setParametrosEnviados($params);

        try {
            $client = new Zend_Http_Client($url);
            $content = $client->request();
            
            $xml = simplexml_load_string($content->getBody());
            
            $relatorioCalculosFrete->setXmlRetorno($content->getBody())
            					->setStatus(Noix_Correios_Model_Relatorio_Calculosfrete::STATUS_SUCESSO)
            					->save();
            
            return $xml;
        } catch (Exception $e) {
            
            $relatorioCalculosFrete->setMensagemErro($e->getMessage())
            					->setStatus(Noix_Correios_Model_Relatorio_Calculosfrete::STATUS_ERRO)
            					->save();
            return false;
        }
    }

}