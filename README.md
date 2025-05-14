# **ValidaCurp Client**

[![GitHub Release](https://img.shields.io/github/v/release/hckdrkmx/validaCurp-client)]()
[![GitHub Release Date](https://img.shields.io/github/release-date/hckdrkmx/validaCurp-client)]()


* This library can validate, calculate and obtain CURP information in México.

* Copyright (c) Multiservicios Web JCA S.A. de C.V., https://multiservicios-web.com.mx
* More information: https://valida-curp.com.mx
* License: MIT (https://opensource.org/license/MIT)

## 1. Requirements

-   PHP 7.2 or later

## 2. Installation

You can install this project via composer with the following commands:

```bash
  composer require multiservicios-web/valida-curp
```

## 3. Account

### 3.1. Create account 
Create an account following this link: https://valida-curp.tawk.help/article/registro-de-usuario

### 3.2. Create project
Create a project following this link: https://valida-curp.tawk.help/article/creaci%C3%B3n-de-proyecto

### 3.3. Get token
Get your token following this link: https://valida-curp.tawk.help/article/obtener-token-llave-privada-proyecto

## **4. Usage**

### 4.1. include autoload and import library  

```php
<?php
require 'vendor/autoload.php';

use MultiserviciosWeb\ValidaCurp\Client as ValidaCurp;
use MultiserviciosWeb\ValidaCurp\ValidaCurpException;
```

### 4.2. create instance
The constructor receives the first parameter the project token
```php
$validaCurp = new ValidaCurp("YOUR-TOKEN");
```

### 4.3. (Optional) setVersion API
You can set the API version to query. You can set 1 or 2. The default value is 2.
```php
$validaCurp->setVersion(1); //1 or 2
```

## 5. Methods

### 5.1. Validate CURP
isValid() method takes a CURP as a parameter. Validate the structure CURP.
```php
$validaCurp->isValid('PXNE660720HMCXTN06');
```
Response
```
stdClass Object
(
    [valido] => 1
)
```

### 5.2. Get CURP data
getData() method takes a CURP as a parameter. Consult the CURP information in RENAPO.
```php
$validaCurp->getData('PXNE660720HMCXTN06');
```
Response
```
stdClass Object
(
    [Applicant] => stdClass Object
        (
            [CURP] => PXNE660720HMCXTN06
            [Names] => ENRIQUE
            [LastName] => PEÑA
            [SecondLastName] => NIETO
            [GenderKey] => H
            [Gender] => Hombre
            [DateOfBirth] => 1966-07-20
            [Nacionality] => MEX
            [CodeEntityBirth] => 
            [EntityBirth] => 
            [KeyEvidentiaryDocument] => 1
            [EvidentiaryDocument] => Acta de nacimiento
            [CurpStatusKey] => AN
            [CurpStatus] => Alta Normal
        )

    [EvidentiaryDocument] => stdClass Object
        (
            [YearRegistration] => 1966
            [KeyIssuingEntity] => 
            [KeyMunicipalityRegistration] => 14
            [MunicipalityRegistration] => 
            [Foja] => 0
            [FolioLetter] => 
            [Book] => 0
            [CertificateNumber] => 985
            [RegistrantNumber] => 15
            [RegistrationEntity] => México
            [ForeignRegistrationNumber] => 
            [Volume] => 0
        )

)
```

### 5.3. calculate CURP
Calculates the structure of a CURP with provided data.
calculate() receives an array with the following elements:
[names, lastName, secondLastName, birthDay, birthMonth, birthYear, gender, entity]
```php
$validaCurp->calculate([
    'names' => 'Enrique',
    'lastName' => 'Peña',
    'secondLastName' => 'Nieto',
    'birthDay' => '20',
    'birthMonth' => '07',
    'birthYear' => '1966',
    'gender' => 'H',
    'entity' => '15',
]);
```
Response
```
stdClass Object
(
    [curp] => PXNE660720HMCXTN06
)
```

### 5.4. Get entities
getEntities() method get the list of entities.
```php
$validaCurp->getEntities();
```
Response
```
stdClass Object
(
    [clave_entidad] => Array
        (
            [0] => stdClass Object
                (
                    [clave_entidad] => 01
                    [nombre_entidad] => AGUASCALIENTES
                    [abreviatura_entidad] => AS
                )

            [1] => stdClass Object
                (
                    [clave_entidad] => 02
                    [nombre_entidad] => BAJA CALIFORNIA
                    [abreviatura_entidad] => BC
                ) ...
```

## 6. Full example
To see the full example click on this link
https://github.com/hckdrkmx/validaCurp-client/blob/main/index.php

## 7. Credits

- Copyright (c) **Multiservicios Web JCA S.A. de C.V.**, https://multiservicios-web.com.mx
- **Author:** Joel Rojas <me@hckdrk.mx>

## 8. License

This project is released under the MIT License. See the **[LICENSE](./LICENSE)** file for details.