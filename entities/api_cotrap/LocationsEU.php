<?php

namespace OBCBot\entities\api_cotrap;

use OBCBot\entities\BaseEntity;
use DB;


/**
 * Class to communicate with `cotrap_localita_eu` table in database
 */
class LocationsEU extends BaseEntity {

  public const MATCHED = 95;
  public const ALMOST_MATCHED = 70;


  public function __construct() {}


  /**
   * Get all locations (for departure) in the database
   */
  public function getAllDepartureLocations() {
    return DB::query("SELECT codice, denominazione FROM cotrap_localita_eu ORDER BY codice ASC");
  }


  /**
   * Get all the possible arrival locations (for a specific departure location) and return array with info about them
   */
  public function getArrivalLocationsFromDepartureLocationId($departure_location_id) {
    $query_result = DB::query("SELECT codice, denominazione, localitaArrivo FROM cotrap_localita_eu WHERE codice=%s_departure_location_id", [
      "departure_location_id" => $departure_location_id
    ]);
    // TODO: controllare se il risultato è null
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

    /* this loop for each location in database calculates and saves the similarity percentage with the location to search */
    for ($i=0; $i<$count_locations; $i++) {
      $similarity_perc = 0;
      $location_code = $all_locations[$i]["codice"];
      $location_name = $all_locations[$i]["denominazione"];

      /* the $location_name_to_match handles some exceptional manipulations to match with location to search */
      $location_name_to_match = $location_name;
      if (str_contains($location_name_to_match, "-")) {
        $location_name_to_match = explode("-", $location_name_to_match)[0];
      }

      if (str_contains($location_name_to_match, "di Puglia")) {
        $location_name_to_match = explode("di Puglia", $location_name_to_match)[0];
      }

      /* removes white spaces from the beginning and the ending of the strings; also transform strings to lowercase */
      $formatted_location_name = trim(strtolower($location_name_to_match));
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
      /* sorts locations in descending order based on similarity percentage */
      return $b['similarity_perc'] <=> $a['similarity_perc'];
    });

    /* returns the first 5 best matching locations */
    return array_slice($results_array, 0, 5);
  }

}