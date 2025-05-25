<?php

namespace CustomBotName\entities\api_cotrap;

use CustomBotName\entities\BaseEntity;

abstract class Search extends BaseEntity {

  protected $user_idtelegram;


  public function __construct($user_idtelegram) {
    $this->setUserIdtelegram($user_idtelegram);
  }


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
   * Set/unset the arrival location id in the initialized search record
   */
  abstract public function setArrivalLocation($arrival_location_id);
  abstract public function unsetArrivalLocation();

}