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

class Noix_Correios_Block_Adminhtml_Promo_Tab
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('salesrule')->__('Noix Correios - Ações');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('salesrule')->__('Noix Correios - Ações');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $rule = Mage::registry('current_promo_quote_rule');
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('default_label_fieldset', array(
            'legend' => Mage::helper('salesrule')->__('Default Label')
        ));
        
        $fieldset->addField('noix_correios_tipo_desconto', 'select', array(
            'name'      => 'noix_correios_tipo_desconto',
            'required'  => false,
            'label'     => Mage::helper('salesrule')->__('Tipo de desconto a ser aplicado'),
            'value'     => $rule->getNoixCorreiosTipoDesconto(),
            'options'    => array(
                '0' => Mage::helper('salesrule')->__('-- Escolha uma opção --'),
                'normal' => Mage::helper('salesrule')->__('Desconto normal'),
                'porcentagem' => Mage::helper('salesrule')->__('Desconto em porcentagem'),
                'fixo' => Mage::helper('salesrule')->__('Valor fixo de frete')
            )
        ));
        
        $fieldset->addField('noix_correios_desconto', 'text', array(
            'name'      => 'noix_correios_desconto',
            'required'  => false,
            'label'     => Mage::helper('salesrule')->__('Desconto a ser aplicado'),
            'value'     => $rule->getNoixCorreiosDesconto()
        ));
        
        $fieldset->addField('noix_correios_label_fixo', 'text', array(
            'name'      => 'noix_correios_label_fixo',
            'required'  => false,
            'label'     => Mage::helper('salesrule')->__('Label utilizado para valores fixos'),
            'value'     => $rule->getNoixCorreiosLabelFixo()
        ));

        if ($rule->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
