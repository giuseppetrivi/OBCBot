<?php

namespace CustomBotName\entities\api_cotrap;

use CustomBotName\entities\BaseEntity;

abstract class Search extends BaseEntity {

  protected $user_idtelegram;


  public function __construct($user_idtelegram) {
    $this->setUserIdtelegram($user_idtelegram);
  }


  abstract public function initializeSearch();
  abstract public function destroySearch();

  abstract public function setDepartureLocation($departure_location_id);
  abstract public function unsetDepartureLocation();
  
  /*
  abstract public function setArrivalLocation($arrival_location_id);
  abstract public function unsetArrivalLocation();
  */

}