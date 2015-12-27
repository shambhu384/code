<?php

/**
 SoapClient class

*/

try {

    $soap_client = new SoapClient('http://www.webservicex.net/stockquote.asmx?WSDL');

    //var_dump($soap_client->__getFunctions());
    //var_dump($soap_client->__getTypes());
    $vec = array("symbol"=>"string");

    $quote = $soap_client->GetQuote($vec);

    echo $quote->GetQuoteResult; 


} catch(SoapFault $e) {
    print_r($e);
}


