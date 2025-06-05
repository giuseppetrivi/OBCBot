<?php

namespace CustomBotName\entities\api_cotrap;

use CustomBotName\entities\BaseEntity;
use Exception;

class ApiCotrapRequestHandler extends BaseEntity {
  private $baseUrl = "https://biglietteria.cotrap.it/api/ricerca";
  private $endpoints = [];

  /**
   * Costruttore della classe ApiClient.
   *
   * @param string $baseUrl L'URL base dell'API (es. "https://api.example.com/v1").
   * @param string|null $apiKey (Opzionale) La chiave API da includere nelle richieste.
   */
  public function __construct() {
    $this->defineEndpoints();
  }


  /**
   * Definisce gli endpoint API.
   * È il luogo dove centralizzi tutti gli URL degli endpoint.
   */
  private function defineEndpoints() {
    $this->endpoints = [
      'localita_u' => '/localitaurbane',
      'localita_eu' => '/localitaextraurbane',
      'aziende' => '/aziende',
      'poli_localita' => '/polilocalita/{id}',
      'search_eu' => '/extraurbana'
      // ...
      // Aggiungi qui tutti gli altri endpoint della tua API
    ];
  }


  /**
   * Costruisce l'URL completo per una chiamata API.
   *
   * @param string $endpointName Il nome logico dell'endpoint definito in $endpoints.
   * @param array $pathParams (Opzionale) Un array associativo di parametri per il path (es. ['id' => 123]).
   * @param array $queryParams (Opzionale) Un array associativo di parametri per la query string (es. ['limit' => 10]).
   * @return string L'URL completo.
   * @throws Exception Se l'endpoint non è definito.
   */
  private function buildUrl(string $endpointName, array $pathParams = [], array $queryParams = []) : string {
    if (!isset($this->endpoints[$endpointName])) {
      throw new Exception("Endpoint '{$endpointName}' non definito.");
    }

    $endpointPath = $this->endpoints[$endpointName];

    // Sostituisce i parametri nel path (es. {id} con il valore effettivo)
    foreach ($pathParams as $key => $value) {
      $endpointPath = str_replace("{{$key}}", $value, $endpointPath);
    }

    $url = $this->baseUrl . $endpointPath;

    // Aggiunge i parametri di query se presenti
    if (!empty($queryParams)) {
      $url .= '?' . http_build_query($queryParams);
    }

    return $url;
  }


  /**
   * Esegue una richiesta HTTP generica.
   *
   * @param string $method Il metodo HTTP (GET, POST, PUT, DELETE).
   * @param string $endpointName Il nome logico dell'endpoint.
   * @param array $pathParams Parametri per il path dell'URL.
   * @param array $queryParams Parametri per la query string dell'URL.
   * @param array $body (Opzionale) Il corpo della richiesta per POST/PUT, solitamente un array associativo.
   * @param array $headers (Opzionale) Array di header personalizzati.
   * @return mixed La risposta decodificata (solitamente un array o un oggetto PHP) o false in caso di errore.
   * @throws Exception Se la richiesta fallisce o ci sono problemi con cURL.
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
        // GET è il default, non servono opzioni speciali qui
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

    // Puoi aggiungere una logica più sofisticata per gestire i codici di stato HTTP
    if ($httpCode >= 400) {
      throw new Exception("API Error: HTTP Code {$httpCode} - Response: " . print_r($decodedResponse, true));
    }

    return $decodedResponse;
  }
  

  /**
   * Esegue una richiesta GET.
   *
   * @param string $endpointName Il nome logico dell'endpoint.
   * @param array $pathParams Parametri per il path dell'URL.
   * @param array $queryParams Parametri per la query string dell'URL.
   * @return mixed La risposta decodificata.
   * @throws Exception Se la richiesta fallisce.
   */
  public function get(string $endpointName, array $pathParams = [], array $queryParams = []) {
    return $this->request('GET', $endpointName, $pathParams, $queryParams);
  }

}