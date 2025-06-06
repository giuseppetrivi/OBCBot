<?php

namespace CustomBotName\entities\api_cotrap;

use DB;
use MeekroDBException;

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


}