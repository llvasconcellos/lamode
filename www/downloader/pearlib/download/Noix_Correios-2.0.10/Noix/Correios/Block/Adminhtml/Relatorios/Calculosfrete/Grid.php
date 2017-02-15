<?php
class Noix_Correios_Block_Adminhtml_Relatorios_Calculosfrete_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
 
    public function __construct()
    {
        parent::__construct();
        
        $this->setId('produtoGrid');
        
        //$this->_controller = 'produto';
    }
 
    protected function _prepareCollection()
    {    	
    	$collection = Mage::getModel('noix_correios/relatorio_calculosfrete')->getCollection()
    		->setOrder('data_calculo', 'DESC');
    	
    	$collection2 = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('status')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('description')
            ->addAttributeToSelect('image')
            ->joinField('is_in_stock',
                'cataloginventory/stock_item',
                'is_in_stock',
                'product_id=entity_id',
                '{{table}}.stock_id=1',
                'left');
                
		$this->setCollection($collection);
 
        return parent::_prepareCollection();
    }
 
    /*protected function _getCategories(){
        $collection = Mage::getModel('catalog/category')->getCollection()
        ->addAttributeToSelect('name')
        ->setOrder('name','ASC');
        
        $categories = array();
        
        foreach($collection as $category){
            if($category->getName() == ''){
                //$categories[$category->getId()] = 'Id: '.$category->getId().' ('.$category->getProductCount().')';
            }else{                                                
            $categories[$category->getId()] = $category->getName().' ('.$category->getProductCount().')';
            }
            
        } 
        return $categories;
    }*/
     
    protected function _prepareColumns() 
    {   
        //$classname = 'Ibyte_Produto_Block_Admin_Widget_Grid_Column_Filter_Boolean';
        
        //$methodname= 'doFilter';
        
         $this->addColumn('data_calculo',
            array(
                'header'=> Mage::helper('catalog')->__('Data'),
                'width' => '25px',
            	'type' => 'date',
                'index' => 'data_calculo',
        ));
        
         $this->addColumn('cep_destino',
            array(
                'header'=> Mage::helper('catalog')->__('CEP'),
                'width' => '25px',
                'index' => 'cep_destino',
        ));
        
         $this->addColumn('status',
            array(
                'header'=> Mage::helper('catalog')->__('Status'),
                'width' => '25px',
                'index' => 'status',
        ));
        
         $this->addColumn('mensagem_erro',
            array(
                'header'=> Mage::helper('catalog')->__('Mensagem de erro'),
                //'width' => '25px',
                'index' => 'mensagem_erro',
        ));
        
        
        return parent::_prepareColumns();
    }
}