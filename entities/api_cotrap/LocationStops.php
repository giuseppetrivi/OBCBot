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
   * 
   * TODO: descrizione migliore delle query, che non sono banali
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
    $arrival_stop_ids = $this->getListPoliArrivo($departure_stop_info);

    $select_query = "SELECT * FROM cotrap_polilocalita 
      WHERE id IN %ls_arrival_stop_ids AND idAzienda=%i_company AND idComune=%s_town_id AND idFrazione IS NULL
      ORDER BY LENGTH(poliArrivo) DESC";
    $query_arguments = [
      "arrival_stop_ids" => $arrival_stop_ids,
      "company" => $departure_stop_info["idAzienda"],
      "town_id" => $arrival_location_id
    ];

    if (str_contains($arrival_location_id, "-")) {
      $exploded_id = explode("-", $arrival_location_id);
      $town_id = $exploded_id[0];
      $fraction_id = $exploded_id[1];

      $select_query = "SELECT * FROM cotrap_polilocalita 
        WHERE id IN %ls_arrival_stop_ids AND idAzienda=%i_company AND idComune=%s_town_id AND idFrazione=%s_fraction_id
        ORDER BY LENGTH(poliArrivo) DESC";
      $query_arguments["town_id"] = $town_id;
      $query_arguments["fraction_id"] = $fraction_id;
    }

    $arrival_stops_info = DB::query($select_query, $query_arguments);
    return $arrival_stops_info;
  }

  public function getStopInfoById($stop_id) {
    $result = DB::query("SELECT * FROM cotrap_polilocalita WHERE id=%s_stop_id", [
      "stop_id" => $stop_id
    ]);
    return $result[0];
  }

  private function getListPoliArrivo($stop_info) {
    $arrival_stop_ids = array_slice(explode("|", $stop_info["poliArrivo"]), 1, -1);
    return $arrival_stop_ids;

  }


}