<?php

namespace CustomBotName\entities\api_cotrap;

use CustomBotName\entities\BaseEntity;
use DB;


/**
 * Class to communicate with `cotrap_polilocalita` table in database
 */
class LocationStops extends BaseEntity {

  public function __construct() {}


  /**
   * Get all the possible departure stops based on the arrival location
   */
  public function getValidDepartureLocationStops($departure_location_id, $arrival_location_id) {
    $select_query = "SELECT * FROM cotrap_polilocalita 
      WHERE idComune=%s_town_id AND idFrazione IS NULL AND localitaArrivo LIKE %s_arrival_location_id
      ORDER BY LENGTH(poliArrivo) DESC";
    $query_arguments = [
      "town_id" => $departure_location_id,
      "arrival_location_id" => "%|$arrival_location_id|%"
    ];

    if (str_contains($departure_location_id, "-")) {
      $exploded_id = explode("-", $departure_location_id);
      $town_id = $exploded_id[0];
      $fraction_id = $exploded_id[1];

      $select_query = "SELECT * FROM cotrap_polilocalita 
        WHERE idComune=%s_town_id AND idFrazione=%s_fraction_id AND localitaArrivo LIKE %s_arrival_location_id
        ORDER BY LENGTH(poliArrivo) DESC";
      $query_arguments["town_id"] = $town_id;
      $query_arguments["fraction_id"] = $fraction_id;
    }

    $results = DB::query($select_query, $query_arguments);
    return $results;
  }


}