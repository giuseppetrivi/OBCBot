<?php

namespace CustomBotName\entities\api_cotrap;

use CustomBotName\entities\BaseEntity;
use DB;

class LocalitaEU extends BaseEntity {

  public const MATCHED = 95;
  public const ALMOST_MATCHED = 70;


  public function __construct() {}


  /**
   * Get all locations in the database
   */
  public function getAllDepartureLocations() {
    return DB::query("SELECT codice, denominazione FROM cotrap_localita_eu");
  }

  /**
   * Get all the possible arrival locations (for a departure) and return array with info about them
   */
  public function getAllArrivalLocations($user_idtelegram) {
    $query_result = DB::query("SELECT eu.codice, eu.denominazione, eu.localitaArrivo FROM obc_searches as sea JOIN cotrap_localita_eu as eu WHERE sea.user_idtelegram=%i_user_idtelegram AND eu.codice=sea.sea_departure_id", [
      "user_idtelegram" => $user_idtelegram
    ]);
    //controllare se il risultato è null
    $arrival_location_ids = array_slice(explode("|", $query_result[0]["localitaArrivo"]), 1, -1);
    
    $arrival_location_info = DB::query("SELECT codice, denominazione FROM cotrap_localita_eu WHERE codice IN %ls", $arrival_location_ids);
    return $arrival_location_info;
  }


  /**
   * Find matching between location to search and the elements in the array of locations
   */
  public function findBestLocationNameMatch(array $all_locations, string $location_to_search) {
    $count_locations = count($all_locations);

    $results_array = [];

    /* per ogni località nel database da la percentuale di similarità con la località da cercare */
    for ($i=0; $i<$count_locations; $i++) {
      $similarity_perc = 0;
      $location_code = $all_locations[$i]["codice"];
      $location_name = $all_locations[$i]["denominazione"];

      /* toglie gli spazi bianchi da inizio e fine stringa e mette tutto in minuscolo */
      $formatted_location_name = trim(strtolower($location_name));
      $formatted_location_to_search = trim(strtolower($location_to_search));

      similar_text(
        $formatted_location_name, 
        $formatted_location_to_search, 
        $similarity_perc
      );

      array_push($results_array, [
        "location_code" => $location_code,
        "location_name" => $location_name,
        "similarity_perc" => $similarity_perc
      ]);
    }

    usort($results_array, function ($a, $b) {
      /* ordina in modo decrescente in base alla percentuale di similarità */
      return $b['similarity_perc'] <=> $a['similarity_perc'];
    });

    return $results_array[0];
  }


  /**
   * 
   */

}