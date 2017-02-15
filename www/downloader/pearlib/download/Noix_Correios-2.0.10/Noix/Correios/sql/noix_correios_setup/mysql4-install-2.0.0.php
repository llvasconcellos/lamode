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

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
ALTER TABLE `{$this->getTable('salesrule')}`
    ADD COLUMN `noix_correios_desconto` varchar (10);
");

$installer->run("
ALTER TABLE `{$this->getTable('salesrule')}`
    ADD COLUMN `noix_correios_tipo_desconto` varchar (20)
        AFTER `noix_correios_desconto`;
");

$installer->run("
ALTER TABLE `{$this->getTable('salesrule')}`
    ADD COLUMN `noix_correios_label_fixo` varchar (100)
        AFTER `noix_correios_tipo_desconto`;
");

$installer->endSetup();
?>