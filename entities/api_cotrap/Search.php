<?php

namespace CustomBotName\entities\api_cotrap;

use CustomBotName\entities\BaseEntity;
use DB;
use MeekroDBException;

abstract class Search extends BaseEntity {

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
  abstract public function getSearchInfo();


  /**
   * Create the record with only user_idtelegram in the search table
   */
  abstract public function initializeSearch();
  
  /**
   * Delete the record of search
   */
  abstract public function destroySearch();

  /**
   * Set/unset the departure location id in the initialized search record
   */
  abstract public function setDepartureLocation($departure_location_id);
  abstract public function unsetDepartureLocation();

  /**
   * Set/unset the departure stop id, based on departure and arrival options
   */
  abstract public function setDepartureStop($departure_stop_id);
  abstract public function unsetDepartureStop();

  /**
   * Set/unset the arrival stop id, based on departure stop and arrival options
   */
  abstract public function setArrivalStop($arrival_stop_id);
  abstract public function unsetArrivalStop();

  /**
   * Set/unset the start datetime
   */
  abstract public function setDatetime($datetime);
  abstract public function unsetDatetime();

}