<?php

namespace CustomBotName\entities\api_cotrap;

use CustomBotName\entities\BaseEntity;
use DB;
use MeekroDBException;


/**
 * Base class for handle `obc_search_history` table in db
 */
class SearchHistory extends BaseEntity {

  protected $user_idtelegram;

  public function __construct($user_idtelegram) {
    $this->setUserIdtelegram($user_idtelegram);
  }


  /**
   * 
   */
  public function getAllSearchHistory() {
    return DB::query("SELECT id FROM obc_search_history WHERE user_idtelegram=%s_user_idtelegram ORDER BY his_search_datetime DESC", [
      "user_idtelegram" => $this->getUserIdtelegram()
    ]);
  }
  

  /**
   * Insert the search info as a record in the search history table
   */
  public function insertSearchHistory($search_info) {
    return DB::insert("obc_search_history", [
      "user_idtelegram" => $search_info["user_idtelegram"],
      "his_departure_id" => $search_info["sea_departure_id"],
      "his_arrival_id" => $search_info["sea_arrival_id"],
      "his_departure_stop_id" => $search_info["sea_departure_stop_id"],
      "his_arrival_stop_id" => $search_info["sea_arrival_stop_id"],
      "his_datetime" => $search_info["sea_datetime"]
    ]);
  }


  /**
   * Get info to identify basic informations of the search (to build header of the message of results)
   */
  public function getSpecificSearchHistoryInfo($search_history_id) {
    /*$result_departure = DB::query("SELECT eu.denominazione as comune, pl.denominazione as fermata, pl.latitudine, pl.longitudine, az.denominazione as azienda, sea.sea_datetime 
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
    ];*/
  }

}