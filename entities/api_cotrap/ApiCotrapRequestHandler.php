<?php

namespace CustomBotName\entities\api_cotrap;

use CustomBotName\entities\BaseEntity;
use Exception;

class ApiCotrapRequestHandler extends BaseEntity {
  private $baseUrl = "https://biglietteria.cotrap.it/api/ricerca";
  private $endpoints = [];


  public function __construct() {
    $this->defineEndpoints();
  }


  /**
   * Define API endpoints, to call them with aliases (keys)
   */
  private function defineEndpoints() {
    $this->endpoints = [
      'localita_u' => '/localitaurbane',
      'localita_eu' => '/localitaextraurbane',
      'aziende' => '/aziende',
      'poli_localita' => '/polilocalita/{id}',
      'search_eu' => '/extraurbana'
      // ...
    ];
  }


  /**
   * Build URL to execute an API call
   *
   * @param string $endpointName Endpoint name defined in $endpoints.
   * @param array $pathParams (Optional) Associative array of parameter for the path (es. ['id' => 123]).
   * @param array $queryParams (Optional) Associative array of parameter for the query string (es. ['limit' => 10]).
   * @return string Complete URL.
   * @throws Exception if endpoint is not defined.
   */
  private function buildUrl(string $endpointName, array $pathParams = [], array $queryParams = []) : string {
    if (!isset($this->endpoints[$endpointName])) {
      throw new Exception("Endpoint '{$endpointName}' not defined");
    }

    $endpointPath = $this->endpoints[$endpointName];

    /* substitute path parameters (es. {id} with effective value) */
    foreach ($pathParams as $key => $value) {
      $endpointPath = str_replace("{{$key}}", $value, $endpointPath);
    }

    $url = $this->baseUrl . $endpointPath;

    /* adds query parameters, if them exists */
    if (!empty($queryParams)) {
      $url .= '?' . http_build_query($queryParams);
    }

    return $url;
  }


  /**
   * Execute generic HTTP request
   *
   * @param string $method HTTP method (GET, POST, PUT, DELETE)
   * @param string $endpointName Endpoint name
   * @param array $pathParams URL path parameters
   * @param array $queryParams URL query parameters
   * @param array $body (Optional) Body for POST/PUT request, usually an associative array
   * @param array $headers (Optional) Custom array of headers
   * @return mixed Decoded response or false in case of error
   * @throws Exception If request fails or there is some problem with cURL
   */
  private function request(
      string $method,
      string $endpointName,
      array $pathParams = [],
      array $queryParams = [],
      array $body = [],
      array $headers = []
  ) {
    $url = $this->buildUrl($endpointName, $pathParams, $queryParams);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge([
      'Content-Type: application/json',
      'Accept: application/json'
    ], $headers));

    switch (strtoupper($method)) {
      case 'POST':
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        break;
      case 'PUT':
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        break;
      case 'DELETE':
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        break;
      case 'GET':
      default:
        /* GET is default, so no special options here */
        break;
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
      $error_msg = curl_error($ch);
      curl_close($ch);
      throw new Exception("Errore cURL durante la richiesta a {$url}: {$error_msg}");
    }

    curl_close($ch);

    $decodedResponse = json_decode($response, true);

    /* logic to handle special states */
    if ($httpCode >= 400) {
      throw new Exception("API Error: HTTP Code {$httpCode} - Response: " . print_r($decodedResponse, true));
    }

    return [
      "result" => $decodedResponse,
      "url" => $url
    ];
  }
  

  /**
   * Execute GET request
   *
   * @param string $endpointName Endpoint name
   * @param array $pathParams URL path parameters
   * @param array $queryParams URL query parameters
   * @return mixed Decoded response
   * @throws Exception If request fails
   */
  public function get(string $endpointName, array $pathParams = [], array $queryParams = []) {
    return $this->request('GET', $endpointName, $pathParams, $queryParams);
  }

}