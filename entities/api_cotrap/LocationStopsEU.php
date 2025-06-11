<?php

namespace OBCBot\entities\api_cotrap;

use OBCBot\entities\BaseEntity;
use DB;


/**
 * Class to communicate with `cotrap_polilocalita` table in database
 */
class LocationStopsEU extends BaseEntity {

  public function __construct() {}


  /**
   * Get all the possible departure stops based on the arrival location
   * 
   * TODO: descrizione migliore delle query, che non sono banali
   */
  public function getValidDepartureLocationStops($departure_location_id, $arrival_location_id) {
    $fraction_condition_query = "AND idFrazione IS NULL";
    $query_arguments = [
      "town_id" => $departure_location_id,
      "arrival_location_id" => "%|$arrival_location_id|%"
    ];

    if (str_contains($departure_location_id, "-")) {
      $exploded_id = explode("-", $departure_location_id);
      $town_id = $exploded_id[0];
      $fraction_id = $exploded_id[1];

      $fraction_condition_query = "AND idFrazione=%s_fraction_id";
      $query_arguments["town_id"] = $town_id;
      $query_arguments["fraction_id"] = $fraction_id;
    }

    $select_query = "SELECT * FROM cotrap_polilocalita 
      WHERE idComune=%s_town_id ". $fraction_condition_query ." AND localitaArrivo LIKE %s_arrival_location_id AND extraurbano=1
      ORDER BY LENGTH(poliArrivo) DESC";
    $results = DB::query($select_query, $query_arguments);
    return $results;
  }


  /**
   * Get all the possible arrival stops based on the departure stop
   * 
   * DESCRIZIONE:
   * devo prendere le informazioni relative alla fermata di partenza
   * devo prendere le informazioni relative ai poli di arrivo
   * 
   * TODO: descrizione migliore delle query, che non sono banali
   */
  public function getValidArrivalLocationStops($departure_stop_id, $arrival_location_id) {
    $departure_stop_info = $this->getStopInfoById($departure_stop_id);
    $arrival_stop_ids = $this->getArrayOfArrivalStops($departure_stop_info);

    $fraction_condition_query = "AND idFrazione IS NULL";
    $query_arguments = [
      "arrival_stop_ids" => $arrival_stop_ids,
      "company" => $departure_stop_info["idAzienda"],
      "town_id" => $arrival_location_id
    ];

    if (str_contains($arrival_location_id, "-")) {
      $exploded_id = explode("-", $arrival_location_id);
      $town_id = $exploded_id[0];
      $fraction_id = $exploded_id[1];

      $fraction_condition_query = "AND idFrazione=%s_fraction_id";
      $query_arguments["town_id"] = $town_id;
      $query_arguments["fraction_id"] = $fraction_id;
    }

    $select_query = "SELECT * FROM cotrap_polilocalita 
      WHERE id IN %ls_arrival_stop_ids AND idAzienda=%i_company AND idComune=%s_town_id ". $fraction_condition_query ." AND extraurbano=1
      ORDER BY LENGTH(poliArrivo) DESC";
    $arrival_stops_info = DB::query($select_query, $query_arguments);
    return $arrival_stops_info;
  }


  /** */
  public function getStopInfoById($stop_id) {
    return DB::query("SELECT * FROM cotrap_polilocalita WHERE id=%s_stop_id", [
      "stop_id" => $stop_id
    ])[0];
  }


  /** */
  private function getArrayOfArrivalStops($stop_info) {
    return array_slice(explode("|", $stop_info["poliArrivo"]), 1, -1);
  }


}