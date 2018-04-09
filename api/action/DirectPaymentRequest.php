<?php

class DirectPaymentRequest
{

    public static function main($pedido)
    {   

        var_dump($pedido);
        die();
        // Instantiate a new payment request
        $directPaymentRequest = new PagSeguroDirectPaymentRequest();

        
        $directPaymentRequest->setPaymentMode('DEFAULT');
        $directPaymentRequest->setPaymentMethod('CREDIT_CARD');
        $directPaymentRequest->setCurrency("BRL");

        $directPaymentRequest->addItem(
            '0001',
            'pinel',
            1,
            10.00
        );

        $directPaymentRequest->setReference("REF123");

        $directPaymentRequest->setSender(
            $pedido['nome'],
            'c50008726444651060809@sandbox.pagseguro.com.br',
            $pedido['ddd'],
            $pedido['telefone'],
            'CPF',
            $pedido['cpf']
        );

    
        $directPaymentRequest->setSenderHash($pedido['senderHash']);

        //Set billing information for credit card
    
        $token = $pedido['tokenCartao'];

        $installment = new PagSeguroDirectPaymentInstallment(
            array(
              "quantity" => 1,
              "value" => "15.00",
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
                            'value' => '131.962.147-37'
                        ),
                        'birthDate' => date('01/10/1979'),
                        'areaCode' => $pedido['ddd'],
                        'number' => $pedido['telefone']
                    )
                ),
                
            )
        );

        //Set credit card for payment
        $directPaymentRequest->setCreditCard($cardCheckout);

        try {
            $credentials = PagSeguroConfig::getApplicationCredentials();
            $return = $directPaymentRequest->register($credentials);

            self::printTransactionReturn($return);

        } catch (PagSeguroServiceException $e) {
            die($e->getMessage());
        }
    }

    public static function printTransactionReturn($transaction)
    {

        if ($transaction) {
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
            echo "<p><strong>Shipping: </strong></p>";
            echo "<p><strong>street: </strong> ".$transaction->getShipping()->getAddress()->getStreet() ."</p> ";
            echo "<p><strong>number: </strong> ".$transaction->getShipping()->getAddress()->getNumber()  ."</p> ";
            echo "<p><strong>complement: </strong> ".$transaction->getShipping()->getAddress()->getComplement()  ."</p> ";
            echo "<p><strong>district: </strong> ".$transaction->getShipping()->getAddress()->getDistrict()  ."</p> ";
            echo "<p><strong>postalCode: </strong> ".$transaction->getShipping()->getAddress()->getPostalCode()  ."</p> ";
            echo "<p><strong>city: </strong> ".$transaction->getShipping()->getAddress()->getCity()  ."</p> ";
            echo "<p><strong>state: </strong> ".$transaction->getShipping()->getAddress()->getState()  ."</p> ";
            echo "<p><strong>country: </strong> ".$transaction->getShipping()->getAddress()->getCountry()  ."</p> ";
        }

      echo "<pre>";
    }
}

