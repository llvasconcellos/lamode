<?php
/**
 * Pedro Teixeira
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL).
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   PedroTeixeira
 * @package    PedroTeixeira_Correios
 * @copyright  Copyright (c) 2010 Pedro Teixeira (http://www.pteixeira.com.br)
 * @author     Pedro Teixeira <pedro@pteixeira.com.br>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class PedroTeixeira_Correios_Model_Source_WeightType
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'gr', 'label'=>Mage::helper('adminhtml')->__('Gramas')),
            array('value'=>'kg', 'label'=>Mage::helper('adminhtml')->__('Kilos')),
        );
    }

}