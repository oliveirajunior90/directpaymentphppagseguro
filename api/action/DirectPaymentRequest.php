<?php

require_once "./utils/utils.php";

class DirectPaymentRequest
{

    public static function main($pedido)
    {
        
        //if(Utils::validateCPF($pedido['cpf']) == false) { echo 'CPF inválido'; die(); }

        $senderHash = $pedido['senderHash'];
        
         // Instantiate a new payment request
         $directPaymentRequest = new PagSeguroDirectPaymentRequest();

         $directPaymentRequest->addParameter('shippingAddressRequired', 'false');
         // Set the Payment Mode for this payment request
         $directPaymentRequest->setPaymentMode('DEFAULT');
 
         // Set the Payment Method for this payment request
         $directPaymentRequest->setPaymentMethod('CREDIT_CARD');
 
         /**
         * @todo Change the receiver Email
         */
         $directPaymentRequest->setReceiverEmail('oliveira_junior90@hotmail.com');
 
         // Set the currency
         $directPaymentRequest->setCurrency("BRL");
 
         // Add an item for this payment request
            // Add an item for this payment request
         $directPaymentRequest->addItem(
             '0001',
             'Descricao do item a ser vendido',
             1,
             10.00
         );
 
         $directPaymentRequest->setReference("REF123");
 
         $directPaymentRequest->setSender(
            $pedido['nome'],
            $pedido['email'],
            $pedido['ddd'],
            $pedido['telefone'],
            'CPF',
            $pedido['cpf'],
            true
         );

         

         //Set billing information for credit card
         
         $billing = new PagSeguroBilling
         (
             array(
                 'postalCode' => $pedido['endCep'],
                 'street' => $pedido['endRua'],
                 'number' => $pedido['endNumero'],
                 'complement' => $pedido['endComplemento'],
                 'district' => $pedido['endMunicipio'],
                 'city' => $pedido['endCidade'],
                 'state' => $pedido['endEstado'],
                 'country' => 'BRA'
             )
         );
         
         $token = $pedido['tokenCartao'];
 
         $installment = new PagSeguroDirectPaymentInstallment(
             array(
               "quantity" => 1,
               "value" => "10.00",
               "noInterestInstallmentQuantity" => 2
             )
         );
 
         $cardCheckout = new PagSeguroCreditCardCheckout(
             array(
                 'token' => $token,
                 'installment' => $installment,
                 'holder' => new PagSeguroCreditCardHolder(
                     array(
                         'name' => $pedido['nome'], //Equals in Credit Card
                         'documents' => array(
                             'type' => 'CPF',
                             'value' => $pedido['cpf']
                         ),
                         'birthDate' => date($pedido['dataNascimento']),
                         'areaCode' => $pedido['ddd'],
                         'number' => $pedido['telefone']
                     )
                 ),
                 'billing' => $billing
             )
         );
 
         //Set credit card for payment
         $directPaymentRequest->setCreditCard($cardCheckout);
    
        try {
            //$credentials = PagSeguroConfig::getApplicationCredentials();
            $credentials = PagSeguroConfig::getAccountCredentials();
            
            $return = $directPaymentRequest->register($credentials);
            
            self::printTransactionReturn($return); 


        } catch (PagSeguroServiceException $e) {
            die($e->getMessage());
        }
    }

    public static function printTransactionReturn($transaction)
    {
        $data = array();
        if ($transaction) {

            $data['date'] = $transaction->getDate();

            echo json_encode($data);
            die();

            echo "<h2>Retorno da transa&ccedil;&atilde;o com Cart&atilde;o de Cr&eacute;dito.</h2>";
            echo "<p><strong>Date: </strong> ".$transaction->getDate() ."</p> ";
            echo "<p><strong>lastEventDate: </strong> ".$transaction->getLastEventDate()."</p> ";
            echo "<p><strong>code: </strong> ".$transaction->getCode() ."</p> ";
            echo "<p><strong>reference: </strong> ".$transaction->getReference() ."</p> ";
            echo "<p><strong>type: </strong> ".$transaction->getType()->getValue() ."</p> ";
            echo "<p><strong>status: </strong> ".$transaction->getStatus()->getValue() ."</p> ";

            echo "<p><strong>paymentMethodType: </strong> ".$transaction->getPaymentMethod()->getType()->getValue() ."</p> ";
            echo "<p><strong>paymentModeCode: </strong> ".$transaction->getPaymentMethod()->getCode()->getValue() ."</p> ";

            echo "<p><strong>grossAmount: </strong> ".$transaction->getGrossAmount() ."</p> ";
            echo "<p><strong>discountAmount: </strong> ".$transaction->getDiscountAmount() ."</p> ";
            echo "<p><strong>feeAmount: </strong> ".$transaction->getFeeAmount() ."</p> ";
            echo "<p><strong>netAmount: </strong> ".$transaction->getNetAmount() ."</p> ";
            echo "<p><strong>extraAmount: </strong> ".$transaction->getExtraAmount() ."</p> ";

            echo "<p><strong>installmentCount: </strong> ".$transaction->getInstallmentCount() ."</p> ";
            echo "<p><strong>itemCount: </strong> ".$transaction->getItemCount() ."</p> ";

            echo "<p><strong>Items: </strong></p>";
            foreach ($transaction->getItems() as $item)
            {
                echo "<p><strong>id: </strong> ". $item->getId() ."</br> ";
                echo "<strong>description: </strong> ". $item->getDescription() ."</br> ";
                echo "<strong>quantity: </strong> ". $item->getQuantity() ."</br> ";
                echo "<strong>amount: </strong> ". $item->getAmount() ."</p> ";
            }

            echo "<p><strong>senderName: </strong> ".$transaction->getSender()->getName() ."</p> ";
            echo "<p><strong>senderEmail: </strong> ".$transaction->getSender()->getEmail() ."</p> ";
            echo "<p><strong>senderPhone: </strong> ".$transaction->getSender()->getPhone()->getAreaCode() . " - " .
                 $transaction->getSender()->getPhone()->getNumber() . "</p> ";

        }

      echo "<pre>";
    }
}

