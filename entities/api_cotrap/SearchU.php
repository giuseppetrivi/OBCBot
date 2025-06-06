<?php

namespace CustomBotName\entities\api_cotrap;

use DB;

/**
 * Specific class when in searching urban procedure
 */
class SearchU extends Search {

  /**
   * Get info to identify basic informations of the search (to build header of the message of results)
   */
  public function getSpecificSearchInfo() {
    $result_departure = DB::query("SELECT u.denominazione as comune, pl.denominazione as fermata, pl.latitudine, pl.longitudine, az.denominazione as azienda, sea.sea_datetime 
      FROM obc_searches as sea JOIN cotrap_localita_u as u JOIN cotrap_polilocalita as pl JOIN cotrap_aziende as az 
      WHERE sea.sea_departure_id=u.codice AND pl.idAzienda=az.id AND sea.sea_departure_stop_id=pl.id AND sea.user_idtelegram=%i_user_idtelegram", [
        "user_idtelegram" => $this->getUserIdtelegram()
      ])[0];
    $result_arrival = DB::query("SELECT u.denominazione as comune, pl.denominazione as fermata, pl.latitudine, pl.longitudine
      FROM obc_searches as sea JOIN cotrap_localita_u as u JOIN cotrap_polilocalita as pl 
      WHERE sea.sea_arrival_id=u.codice AND sea.sea_arrival_stop_id=pl.id AND sea.user_idtelegram=%i_user_idtelegram", [
        "user_idtelegram" => $this->getUserIdtelegram()
      ])[0];
    
    return [
      "departure" => $result_departure,
      "arrival" => $result_arrival
    ];
  }
  
}