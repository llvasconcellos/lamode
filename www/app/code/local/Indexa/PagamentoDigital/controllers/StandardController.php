<?php
/**
 * Indexa - Pagamento Digital Payment Module
 *
 * @title      Magento -> Custom Payment Module for Pagamento Digital (Brazil)
 * @category   Payment Gateway
 * @package    Indexa_PagamentoDigital
 * @author     Gabriel Zamprogna -> desenvolvimento [at] indexainternet [dot] com  [dot] br
 * @copyright  Copyright (c) 2009 Indexa
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Indexa_PagamentoDigital_StandardController extends Mage_Core_Controller_Front_Action
{

    /**
     * Order instance
     */
    protected $_order;

    /**
     *  Get order
     *
     *  @return	  Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if ($this->_order == null) {
        }
        return $this->_order;
    }

    protected function _expireAjax()
    {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    /**
     * Get singleton with pagamento digital standard order transaction information
     *
     * @return Indexa_PagamentoDigital_Model_Standard
     */
    public function getStandard()
    {
        return Mage::getSingleton('pagamentodigital/standard');
    }

    /**
     * When a customer chooses Pagamento Digital on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setPagamentodigitalStandardQuoteId($session->getQuoteId());

        $this->getResponse()->setHeader("Content-Type", "text/html; charset=ISO-8859-1", true);

        $this->getResponse()->setBody($this->getLayout()->createBlock('pagamentodigital/standard_redirect')->toHtml());
        $session->unsQuoteId();
    }

    /**
     * When a customer cancel payment from pagamento digital.
     */
    public function cancelAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getPagamentoDigitalStandardQuoteId(true));

        // cancel order
        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId()) {
                $order->cancel()->save();
            }
        }

        $this->_redirect('checkout/cart');
    }

    /**
     * when pagamento_digital returns
     * The order information at this point is in POST
     * variables.  However, you don't want to "process" the order until you
     * get validation from the return post.
     */
    public function  successAction()
    {
		
    	if (!$this->getRequest()->isPost())
		{
			$this->_redirect('');
			return;
		}

        $dados_post = $this->getRequest()->getPost();
		$dados_post_status = utf8_encode($dados_post['status']);

		if($this->getStandard()->getDebug())
		{
			// @todo Indexa still no current pagamentodigital debug api
		}

		if ( trim($dados_post_status) == "Transação em Andamento" )
		{
			if (Mage::getSingleton('checkout/session')->getLastOrderId())
			{
				$order = Mage::getModel('sales/order');
				$order->setEmailSent(true);
				$order->load(Mage::getSingleton('checkout/session')->getLastOrderId());
				$order->sendNewOrderEmail();
				$order->save();
			}
			
			$session = Mage::getSingleton('checkout/session');
	        $session->setQuoteId($session->getPagamentoDigitalStandardQuoteId(true));
	        /**
	         * set the quote as inactive after back from PD
	         */
	        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
	        //Mage::getSingleton('checkout/session')->unsQuoteId();

	        $this->_redirect('checkout/onepage/success', array('_secure'=>true));
        }
        else
        {

			$token = $this->getStandard()->getConfigData('token');
        	
	        $post =	"transacao={$dados_post['id_transacao']}" .
					"&status={$dados_post['status']}" .
					"&valor_original={$dados_post['valor_original']}" .
					"&valor_loja={$dados_post['valor_loja']}" .
					"&token={$token}";

			$enderecoPost = "https://www.pagamentodigital.com.br/checkout/verify/";
			
			ob_start();
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL, $enderecoPost);
			curl_setopt ($ch, CURLOPT_POST, 1);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  
			curl_exec ($ch);
			$resposta = ob_get_contents();
			ob_end_clean();

        	// comments for errors
			$comment = "";
			if (isset($dados_post['cod_status']))
				$comment .= " - " . $dados_post['cod_status'];

			if (isset($dados_post_status))
				$comment .= " - " . $dados_post_status;

			if(trim($resposta) == "VERIFICADO")
			{
	        	$order = Mage::getModel('sales/order');
	        	$order->loadByIncrementId($dados_post['id_pedido']);
	        	
	        	if (!$order->getId())
	        	{
	        		// no order ID, but nothing to do about it.
	        	}
	        	else
	        	{
	        		if ($dados_post['valor_original'] != $order->getGrandTotal())
	        		{
	                    //when grand total does not equal, need to have some logic to take care
	                    
	        			$frase = 'Total pago ao Pagamento Digital é diferente do valor original.';
	        			
	                    $order->addStatusToHistory(
	                        $order->getStatus(),//continue setting current order status
	                        Mage::helper('pagamentodigital')->__($frase),
	                        true
	                    );
	                    $order->sendOrderUpdateEmail(true, $frase);
	                }
	                else
	                {
				        if ( $dados_post_status == "Transação Concluída" )
				        {
				        	// if it has a virtualproduct in the order do we have to act differently?
							$items = $order->getAllItems();
							
							$thereIsVirtual = false;
							foreach ($items as $itemId => $item)
							{
								if ($item["is_virtual"] == "1" or $item["is_downloadable"] == "1")
									$thereIsVirtual = true;
							}
							
							// what to do - from admin
							$toInvoice = $this->getStandard()->getConfigData('acaopadraovirtual') == "1" ? true : false;
							
							if ($thereIsVirtual && !$toInvoice)
							{
									$frase = 'Pagamento (fatura) confirmado automaticamente pelo Pagamento Digital.';
									$order->addStatusToHistory(
										$order->getStatus(),//continue setting current order status
										Mage::helper('pagamentodigital')->__($frase),
										true
									);
									$order->sendOrderUpdateEmail(true, $frase);
							}
							else
							{
								if (!$order->canInvoice())
								{
									//when order cannot create invoice, need to have some logic to take care
									$order->addStatusToHistory(
										$order->getStatus(),//continue setting current order status
										Mage::helper('pagamentodigital')->__('Erro ao criar pagamento (fatura).')
									);
									
								}
								else 
								{
									//need to save transaction id
									$order->getPayment()->setTransactionId($dados_post['id_transacao']);
									
									//need to convert from order into invoice
									$invoice = $order->prepareInvoice();
									
									if ($this->getStandard()->canCapture())
									{
										$invoice->register()->capture();
									}
									
									Mage::getModel('core/resource_transaction')
										->addObject($invoice)
										->addObject($invoice->getOrder())
										->save();
									
									$frase = 'Pagamento (fatura) '.$invoice->getIncrementId().' foi criado. Pagamento Digital confirmou automaticamente o pagamento do pedido.';
										
									if ( $thereIsVirtual ) {
										$order->addStatusToHistory(
											$order->getStatus(),
											Mage::helper('pagamentodigital')->__($frase),
											true
										);
									} else {
										$order->addStatusToHistory(
											'processing', //update order status to processing after creating an invoice
											Mage::helper('pagamentodigital')->__($frase),
											true
										);
									}
									$invoice->sendEmail(true, $frase);
								}
							}
						}
						elseif ( trim($dados_post_status) == "Transação Cancelada" )
						{
							// @todo Indexa - Melhorar cancelamento
							
							$frase = 'Pagamento Digital cancelou automaticamente o pedido (transação foi cancelada, pagamento foi negado, pagamento foi estornado ou ocorreu um chargeback).';
							
							$order->addStatusToHistory(
								Mage_Sales_Model_Order::STATE_CANCELED,
								Mage::helper('pagamentodigital')->__($frase),
								true
							);
							$order->sendOrderUpdateEmail(true, $frase);
							
							$order->cancel();
						}
						else
						{
							// STATUS ERROR

							$order->addStatusToHistory(
								$order->getStatus(),
								Mage::helper('pagamentodigital')->__('Pagamento Digital enviou automaticamente um status inválido: %s', $comment));
						}
	                }
	                
	                $order->save();
	        	}
			}
			else
			{
				// VERIFICATION ERROR
				
				$order = Mage::getModel('sales/order');
	        	$order->loadByIncrementId($dados_post['id_pedido']);
	        	
	        	if (!$order->getId())
	        	{
	        		// no order ID, but nothing to do about it.
	        	}
	        	else
		        {

					$order->addStatusToHistory(
						$order->getStatus(),
						Mage::helper('pagamentodigital')->__('Pagamento Digital enviou automaticamente uma verificação inválida: %s', $comment));
					$order->save();
		        }
			}
        }
    }
}
