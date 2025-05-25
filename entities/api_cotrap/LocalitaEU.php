<?php

namespace CustomBotName\entities\api_cotrap;

use CustomBotName\entities\BaseEntity;
use DB;

class LocalitaEU extends BaseEntity {


  public function __construct() {}


  /**
   * 
   */
  public function getAllLocations() {
    return DB::query("SELECT codice, denominazione FROM cotrap_localita_eu");
  }


  /**
   * 
   */
  public function findBestLocationNameMatch(string $location_to_search) {
    $all_locations = $this->getAllLocations();
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
      return $b['similarity_perc'] <=> $a['similarity_perc']; /* ordina in modo decrescente */
    });

    return $results_array[0];
  }

}