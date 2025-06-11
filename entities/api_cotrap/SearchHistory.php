<?php

namespace OBCBot\entities\api_cotrap;

use OBCBot\entities\BaseEntity;
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
    return DB::query("SELECT his_id, his_departure_id, his_arrival_id FROM obc_search_history WHERE user_idtelegram=%s_user_idtelegram ORDER BY his_search_datetime DESC", [
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
   * Get info to get the most frequent routes, no matter which are the departure-arrival.
   * Routes A->B and B->A are considered the same.
   */
  public function getMostFrequentRoutes() {
    $select_query = "SELECT l1.codice as idPartenza, l2.codice as idArrivo, CONCAT(l1.denominazione, ' ➝ ', l2.denominazione) AS tratta, rs.search_count
      FROM (
        SELECT
          user_idtelegram,
          LEAST(his_departure_id, his_arrival_id) AS location1_id,
          GREATEST(his_departure_id, his_arrival_id) AS location2_id,
          COUNT(*) AS search_count
        FROM obc_search_history
        GROUP BY location1_id, location2_id
      ) AS rs
      JOIN cotrap_localita_eu AS l1 ON l1.codice = rs.location1_id
      JOIN cotrap_localita_eu AS l2 ON l2.codice = rs.location2_id
      WHERE rs.user_idtelegram=%s_user_idtelegram
      ORDER BY rs.search_count DESC
      LIMIT 5";

    return DB::query($select_query, [
      "user_idtelegram" => $this->getUserIdtelegram()
    ]);
  }

}