<?php

require 'vendor/autoload.php';

use MultiserviciosWeb\ValidaCurp\Client as ValidaCurp;
use MultiserviciosWeb\ValidaCurp\ValidaCurpException;

try {

    //instance
    $validaCurp = new ValidaCurp("YOUR-TOKEN");

    //validate curp structure
    print_r($validaCurp->isValid('PXNE660720HMCXTN06'));
    echo PHP_EOL;

    //get data
    print_r($validaCurp->getData('PXNE660720HMCXTN06'));
    echo PHP_EOL;

    //calculate curp
    print_r($validaCurp->calculate([
        'names' => 'Enrique',
        'lastName' => 'Peña',
        'secondLastName' => 'Nieto',
        'birthDay' => '20',
        'birthMonth' => '07',
        'birthYear' => '1966',
        'gender' => 'H',
        'entity' => '15',
    ]));
    echo PHP_EOL;

    //get entities
    print_r($validaCurp->getEntities());
    echo PHP_EOL;


} catch (ValidaCurpException $e) {
    echo "Valida CURP Exception: " . $e->getMessage() . PHP_EOL;
} catch (\GuzzleHttp\Exception\GuzzleException $e) {
    echo "Request Exception: " . $e->getMessage() . PHP_EOL;
}





