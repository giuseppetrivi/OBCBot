<?php

namespace OBCBot\entities\api_cotrap;

use OBCBot\entities\BaseEntity;
use DB;
use MeekroDBException;

/**
 * Base class for handle `obc_searches` table in db
 */
class Search extends BaseEntity {

  protected $user_idtelegram;


  public function __construct($user_idtelegram) {
    $this->setUserIdtelegram($user_idtelegram);
  }

  /**
   * Method to mechanically update a signle field of search table
   */
  protected function updateSingleField($field, $value) {
    try {
      $result = DB::update("obc_searches", 
        [$field => $value], 
        ["user_idtelegram" => $this->getUserIdtelegram()]
      );
      return $result;
    } catch(MeekroDBException $e) {
      return 0;
    }
  }


  /**
   * Get all info about search
   */
  public function getSearchInfo() {
    $result = 0;
    try {
      $result = DB::query("SELECT * FROM obc_searches WHERE user_idtelegram=%i_user_idtelegram", [
        "user_idtelegram" => $this->getUserIdtelegram()
      ]);
      return $result[0];
    } catch(MeekroDBException $e) {
      return $result;
    }
  }


  /**
   * Create the record with only user_idtelegram in the search table
   */
  public function initializeSearch() {
    $result = 0;
    try {
      $result = DB::insert("obc_searches", [
        "user_idtelegram" => $this->getUserIdtelegram()
      ]);
      return $result;
    } catch(MeekroDBException $e) {
      return $result;
    }
  }

  /**
   * Delete the record of search
   */
  public function destroySearch() {
    $result = 0;
    try {
      $result = DB::delete("obc_searches", [
        "user_idtelegram" => $this->getUserIdtelegram()
      ]);
      return $result;
    } catch(MeekroDBException $e) {
      return $result;
    }
  }

  /**
   * Set/unset the departure location id in the initialized search record
   */
  public function setDepartureLocation($departure_location_id) {
    return $this->updateSingleField("sea_departure_id", $departure_location_id);
  }

  public function unsetDepartureLocation() {
    return $this->updateSingleField("sea_departure_id", NULL);
  }


  /**
   * Set/unset the departure stop id, based on departure and arrival options
   */
  public function setDepartureStop($departure_stop_id) {
    return $this->updateSingleField("sea_departure_stop_id", $departure_stop_id);
  }
  
  public function unsetDepartureStop() {
    return $this->updateSingleField("sea_departure_stop_id", NULL);
  }


  /**
   * Set/unset the arrival stop id, based on departure stop and arrival options
   */
  public function setArrivalStop($arrival_stop_id) {
    return $this->updateSingleField("sea_arrival_stop_id", $arrival_stop_id);
  }
  
  public function unsetArrivalStop() {
    return $this->updateSingleField("sea_arrival_stop_id", NULL);
  }


  /**
   * Set/unset the start datetime
   */
  public function setDatetime($datetime) {
    return $this->updateSingleField("sea_datetime", $datetime);
  }
  
  public function unsetDatetime() {
    return $this->updateSingleField("sea_datetime", NULL);
  }

}