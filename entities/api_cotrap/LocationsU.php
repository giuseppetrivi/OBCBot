<?php

namespace CustomBotName\entities\api_cotrap;

use CustomBotName\entities\BaseEntity;
use DB;


/**
 * Class to communicate with `cotrap_localita_u` table in database
 */
class LocationsU extends BaseEntity {


  public function __construct() {}


  /**
   * Get all urban locations in the database
   */
  public function getAllUrbanLocations() {
    return DB::query("SELECT codice, denominazione FROM cotrap_localita_u ORDER BY denominazione ASC");
  }

}