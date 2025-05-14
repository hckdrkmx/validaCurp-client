<?php

/**
 * ValidaCurp Client
 *
 * This library can validate, calculate and obtain CURP information in México.
 *
 * Copyright (c) Multiservicios Web JCA S.A. de C.V., https://multiservicios-web.com.mx
 * License: MIT (https://opensource.org/license/MIT)
 *
 * @author  Joel Rojas <me@hckdrk.mx>
 * @copyright   Multiservicios Web JCA S.A. de C.V., https://multiservicios-web.com.mx
 * @license https://opensource.org/license/MIT	MIT License
 * @link    https://valida-curp.com.mx
 *
 */

namespace MultiserviciosWeb\ValidaCurp;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class Client
{

    const URL_V1 = 'https://api.valida-curp.com.mx/curp/';
    const URL_V2 = 'https://version.valida-curp.com.mx/api/v2/curp/';

    private $version = 2;
    private $endpoint = self::URL_V2;
    private $customEndpoint;
    private $token;

    const LIBRARY_VERSION = '1.0.0';
    const TYPE = 'php_composer';


    /**
     * You are required to receive a token.
     * Optionally, an endpoint can be specified. Example: https://custom.valida-curp.com/curp/
     *
     * @param string $token
     * @param string|null $customEndpoint
     */
    public function __construct(string $token, ?string $customEndpoint = null)
    {
        $this->token = $token;
        if ($customEndpoint) {
            $this->customEndpoint = $customEndpoint;
        }
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * Set de API version
     *
     * Version 1 of the API is deprecated. Please use version 2 of the API.
     *
     * @param int $version
     * @throws ValidaCurpException
     */
    public function setVersion(int $version = 2)
    {
        switch ($version) {
            case 1:
                $this->version = 1;
                $this->endpoint = $this->customEndpoint ?? self::URL_V1;
                break;
            case 2:
                $this->version = 2;
                $this->endpoint = $this->customEndpoint ?? self::URL_V2;
                break;
            default:
                throw new ValidaCurpException("The version is invalid");
        }
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * This method takes a CURP as a parameter. Validate the structure CURP.
     *
     * Note: In version 1 of the API, consult the validar method.
     * In version 2 of the API, consult the validateCurpStructure method.
     *
     *
     * @param string $curp
     * @return object|void
     * @throws GuzzleException
     * @throws ValidaCurpException
     */
    public function isValid(string $curp): object
    {
        if (!$this->getToken()) {
            throw new ValidaCurpException('The token was not set');
        }

        if ($this->getVersion() === 1) {
            return $this->validateV1($curp);
        }

        if ($this->getVersion() === 2) {
            return $this->validateV2($curp);
        }
    }

    /**
     * @param string $curp
     * @return object
     * @throws GuzzleException
     * @throws ValidaCurpException
     */
    private function validateV1(string $curp): object
    {
        return $this->decodeResponse($this->request($this->makeUrl("validar", $curp)));
    }

    /**
     * @param string $curp
     * @return object
     * @throws GuzzleException
     * @throws ValidaCurpException
     */
    private function validateV2(string $curp): object
    {
        return $this->decodeResponse($this->makeRequest('validateCurpStructure', $curp));
    }


    /**
     * This method takes a CURP as a parameter. Consult the CURP information in RENAPO.
     *
     * Note: In version 1 of the API, consult the obtener_datos method.
     * In version 2 of the API, consult the getData method.
     *
     * @param string $curp
     * @return object|void
     * @throws ValidaCurpException
     * @throws GuzzleException
     */
    public function getData(string $curp): object
    {

        if (!$this->getToken()) {
            throw new ValidaCurpException('The token was not set');
        }

        if ($this->getVersion() === 1) {
            return $this->getDataV1($curp);
        }

        if ($this->getVersion() === 2) {
            return $this->getDataV2($curp);
        }
    }

    /**
     * @param string $curp
     * @return void
     * @throws ValidaCurpException|GuzzleException
     */
    private function getDataV1(string $curp): object
    {
        return $this->decodeResponse($this->request($this->makeUrl("obtener_datos", $curp)));
    }

    /**
     * @param string $curp
     * @return object
     * @throws GuzzleException
     * @throws ValidaCurpException
     */
    private function getDataV2(string $curp): object
    {
        return $this->decodeResponse($this->makeRequest('getData', $curp));
    }

    /**
     * Calculates the structure of a CURP with provided data.
     * Receives an array with the following elements:
     * [names, lastName, secondLastName, birthDay, birthMonth, birthYear, gender, entity]
     *
     * Note: In version 1 of the API, consult the calcular_curp method.
     * In version 2 of the API, consult the calculateCURP method.
     *
     * @param array $data
     * @return object|void
     * @throws ValidaCurpException|GuzzleException
     */
    public function calculate(array $data): object
    {
        if (!$this->getToken()) {
            throw new ValidaCurpException('The token was not set');
        }

        if ($this->getVersion() === 1) {
            return $this->calculateV1($data);
        }

        if ($this->getVersion() === 2) {
            return $this->calculateV2($data);
        }
    }

    /**
     * @param array $data
     * @return object
     * @throws GuzzleException
     * @throws ValidaCurpException
     */
    private function calculateV1(array $data): object
    {

        $this->validateDataCalculate($data);

        $dataV1 = [
            'nombres' => $data['names'],
            'apellido_paterno' => $data['lastName'],
            'apellido_materno' => $data['secondLastName'],
            'dia_nacimiento' => $data['birthDay'],
            'mes_nacimiento' => $data['birthMonth'],
            'anio_nacimiento' => $data['birthYear'],
            'sexo' => $data['gender'],
            'entidad' => $data['entity'],
        ];

        return $this->decodeResponse($this->request($this->makeUrl("calcular_curp", null, $dataV1)));
    }

    /**
     * @param $data
     * @return object
     * @throws GuzzleException
     * @throws ValidaCurpException
     */
    private function calculateV2($data): object
    {

        $this->validateDataCalculate($data);

        $data['birthday'] = $data['birthDay'];
        $data['yearBirth'] = $data['birthYear'];
        unset($data['birthDay']);
        unset($data['birthYear']);

        return $this->decodeResponse($this->makeRequest("calculateCURP", null, $data));

    }

    /**
     * @param array $data
     * @return void
     * @throws ValidaCurpException
     */
    private function validateDataCalculate(array $data): void
    {
        if (!isset($data['names'])) {
            throw new ValidaCurpException('The names was not set');
        }

        if (!isset($data['lastName'])) {
            throw new ValidaCurpException('The lastName was not set');
        }

        if (!isset($data['secondLastName'])) {
            throw new ValidaCurpException('The secondLastName was not set');
        }

        if (!isset($data['birthDay'])) {
            throw new ValidaCurpException('The birthday was not set');
        }

        if (!isset($data['birthMonth'])) {
            throw new ValidaCurpException('The birthMonth was not set');
        }

        if (!isset($data['birthYear'])) {
            throw new ValidaCurpException('The yearBirth was not set');
        }

        if (!isset($data['gender'])) {
            throw new ValidaCurpException('The gender was not set');
        }

        if (!isset($data['entity'])) {
            throw new ValidaCurpException('The entity was not set');
        }
    }

    /**
     * The list of entities is obtained.
     *
     * Note: In version 1 of the API, consult the entidades method.
     * In version 2 of the API, consult the getEntities method.
     *
     * @return object|void
     * @throws GuzzleException
     * @throws ValidaCurpException
     */
    public function getEntities(): object
    {
        if (!$this->getToken()) {
            throw new ValidaCurpException('The token was not set');
        }

        if ($this->getVersion() === 1) {
            return $this->getEntitiesV1();
        }

        if ($this->getVersion() === 2) {
            return $this->getEntitiesV2();
        }
    }

    /**
     * @return object
     * @throws GuzzleException
     * @throws ValidaCurpException
     */
    private function getEntitiesV1(): object
    {
        return $this->decodeResponse($this->request($this->makeUrl("entidades")));
    }

    /**
     * @return object
     * @throws GuzzleException
     * @throws ValidaCurpException
     */
    private function getEntitiesV2(): object
    {
        return $this->decodeResponse($this->makeRequest("getEntities"));
    }


    /**
     * @param string $url
     * @param array $data
     * @return ResponseInterface|void
     * @throws ValidaCurpException|GuzzleException
     */
    private function request(string $url, array $data = [])
    {

        try {
            $client = new GuzzleClient();

            if ($this->getVersion() === 1) {
                return $client->get($url);
            }
            if ($this->getVersion() === 2) {
                return $client->post($url, $data);
            }
        } catch (ClientException $e) {
            $this->decodeResponse($e->getResponse());
        }

    }

    /**
     * @throws ValidaCurpException
     */
    private function decodeResponse(ResponseInterface $response): object
    {

        $attr = $this->getVersion() == 1 ? 'error_message' : 'msn';

        switch ($response->getStatusCode()) {
            case 200:
                break;
            case 403:
            case 401:
                throw new ValidaCurpException("Failed authentication: " . json_decode($response->getBody()->getContents())->{$attr});
            case 400:
                throw new ValidaCurpException("Bad request: " . json_decode($response->getBody()->getContents())->{$attr});
            default:
                throw new ValidaCurpException("The request failed: " . $response->getReasonPhrase());
        }

        return json_decode($response->getBody()->getContents())->response;
    }

    /**
     * @param string $method
     * @param string|null $curp
     * @param array $extraData
     * @return string
     */
    private function makeUrl(string $method, ?string $curp = null, array $extraData = []): string
    {

        $data = [
            'token' => $this->getToken()
        ];

        if ($curp) {
            $data = array_merge($data, ['curp' => $curp]);
        }

        if ($extraData) {
            $data = array_merge($data, $extraData);
        }

        $data = array_merge($data, [
            'library' => self::TYPE,
            'library_version' => self::LIBRARY_VERSION,
            'api_version' => $this->getVersion(),
        ]);

        return $this->getEndpoint() . "{$method}?" . http_build_query($data);
    }

    /**
     * @param string $method
     * @param string|null $curp
     * @param array|null $extraData
     * @return ResponseInterface|null
     * @throws GuzzleException
     * @throws ValidaCurpException
     */
    private function makeRequest(string $method, ?string $curp = null, ?array $extraData = []): ?ResponseInterface
    {
        $data = ['token' => $this->getToken()];

        if ($curp) {
            $data = array_merge($data, ['curp' => $curp]);
        }

        if ($extraData) {
            $data = array_merge($data, $extraData);
        }

        $queryString = http_build_query([
            'library' => self::TYPE,
            'library_version' => self::LIBRARY_VERSION,
            'api_version' => $this->getVersion(),
        ]);

        return $this->request($this->getEndpoint() . $method . '?' . $queryString, [
            'json' => $data
        ]);
    }


}