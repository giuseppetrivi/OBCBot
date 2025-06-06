<?php

namespace CustomBotName\entities\api_cotrap;

use DB;

/**
 * Specific class when in searching eu procedure
 */
class SearchEU extends Search {
  
  /**
   * Set/unset the arrival location id in the initialized search record
   */
  public function setArrivalLocation($arrival_location_id) {
    return $this->updateSingleField("sea_arrival_id", $arrival_location_id);
  }
  
  public function unsetArrivalLocation() {
    return $this->updateSingleField("sea_arrival_id", NULL);
  }


  /**
   * 
   */
  public function getSpecificSearchInfo() {
    $result_departure = DB::query("SELECT eu.denominazione as comune, pl.denominazione as fermata, pl.latitudine, pl.longitudine, az.denominazione as azienda, sea.sea_datetime 
      FROM obc_searches as sea JOIN cotrap_localita_eu as eu JOIN cotrap_polilocalita as pl JOIN cotrap_aziende as az 
      WHERE sea.sea_departure_id=eu.codice AND pl.idAzienda=az.id AND sea.sea_departure_stop_id=pl.id AND sea.user_idtelegram=%i_user_idtelegram", [
        "user_idtelegram" => $this->getUserIdtelegram()
      ])[0];
    $result_arrival = DB::query("SELECT eu.denominazione as comune, pl.denominazione as fermata, pl.latitudine, pl.longitudine
      FROM obc_searches as sea JOIN cotrap_localita_eu as eu JOIN cotrap_polilocalita as pl 
      WHERE sea.sea_arrival_id=eu.codice AND sea.sea_arrival_stop_id=pl.id AND sea.user_idtelegram=%i_user_idtelegram", [
        "user_idtelegram" => $this->getUserIdtelegram()
      ])[0];
    
    return [
      "departure" => $result_departure,
      "arrival" => $result_arrival
    ];
  }

}