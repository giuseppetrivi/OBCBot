<?php

namespace CustomBotName\entities\api_cotrap;

use CustomBotName\entities\BaseEntity;
use DB;


/**
 * Class to communicate with `cotrap_polilocalita` table in database
 */
class LocationStopsU extends BaseEntity {

  public function __construct() {}

  
  /** */
  public function getAllValidDepartureStop($urban_location_id) {
    return DB::query("SELECT * FROM cotrap_polilocalita 
      WHERE idComune=%s_town_id AND urbano=1
      ORDER BY LENGTH(poliArrivo) DESC", ["town_id" => $urban_location_id]);
  }


  /** */
  public function getAllValidArrivalStop($urban_location_id, $departure_stop_id) {
    return DB::query("SELECT * FROM cotrap_polilocalita 
      WHERE idComune=%s_town_id AND urbano=1 AND poliArrivo LIKE %s_departure_stop_id
      ORDER BY LENGTH(poliArrivo) DESC", [
        "town_id" => $urban_location_id,
        "departure_stop_id" => "%|$departure_stop_id%|"
      ]);
  }
  

  /**
   * Find matching between stop to search and the elements in the array of stops
   */
  public function findBestStopNameMatch(array $all_locations, string $stop_to_search) {
    $count_locations = count($all_locations);
    $results_array = [];

    /* this loop for each stop in database calculates and saves the similarity percentage with the stop to search */
    for ($i=0; $i<$count_locations; $i++) {
      $similarity_perc = 0;
      $stop_code = $all_locations[$i]["id"];
      $stop_name = $all_locations[$i]["denominazione"];

      /* the $stop_name_to_match handles some exceptional manipulations to better match with stop to search */
      $stop_name_to_match = preg_replace('/\s*-\s*[^-]*$/', '', $stop_name);
      if (str_contains($stop_to_search, "-")) {
        $stop_name_to_match = $stop_name;
      }

      /* removes white spaces from the beginning and the ending of the strings; also transform strings to lowercase */
      $formatted_stop_name = trim(strtolower($stop_name_to_match));
      $formatted_stop_to_search = trim(strtolower($stop_to_search));

      similar_text(
        $formatted_stop_name, 
        $formatted_stop_to_search, 
        $similarity_perc
      );

      array_push($results_array, [
        "stop_code" => $stop_code,
        "stop_name" => $stop_name,
        "similarity_perc" => $similarity_perc
      ]);
    }

    usort($results_array, function ($a, $b) {
      /* sorts stops in descending order based on similarity percentage */
      return $b['similarity_perc'] <=> $a['similarity_perc'];
    });

    /* returns the first 5 best matching stops */
    return array_slice($results_array, 0, 10);
  }

}