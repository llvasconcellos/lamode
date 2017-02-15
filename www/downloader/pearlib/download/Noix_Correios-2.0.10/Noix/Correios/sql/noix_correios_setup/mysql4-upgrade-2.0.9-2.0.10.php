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
DROP TABLE IF EXISTS `noix_correios_calculos`;

CREATE TABLE `noix_correios_calculos` (
  `id_calculo` int(11) AUTO_INCREMENT PRIMARY KEY NOT NULL,
  `data_calculo` datetime DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `mensagem_erro` varchar(200) DEFAULT NULL,
  `cep_destino` varchar(10) DEFAULT NULL,
  `parametros_enviados` text,
  `xml_retorno` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
");

$installer->endSetup();
?>