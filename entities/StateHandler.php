<?php

namespace OBCBot\entities;

use DB;


/**
 * Class to handle states of the user (into the database)
 * The code of this class method depends on your database architecture
 */
class StateHandler extends BaseEntity {

  protected int $user_id;


  public function __construct(int $user_id) {
    $this->setUserId($user_id);
  }


  /**
   * Gets the unique state name related to a User
   */
  public function getStateName() {
    $result = DB::queryFirstRow("SELECT user_statename FROM obc_users WHERE user_idtelegram=%i", $this->getUserId());
    if ($result['user_statename']!=null) { 
      return $result['user_statename'];
    }
    else {
      // If there is no state in database, so you need to start the Main state class
      return "Main"; // TODO: classe generica per i nomi degli stati
    }
  }

  public function updateState($new_state_name, $new_state_data=null) {
    return DB::update("obc_users", 
      ["user_statename" => $new_state_name, "user_statedata" => $new_state_data], 
      ["user_idtelegram" => $this->getUserId()]
    );
  }

  /**
   * Updates only the "state_data" field, without modify "state_name" field
   */
  public function updateOnlyStateData($new_state_data) {
    return DB::update("obc_users", 
      ["user_statedata" => $new_state_data], 
      ["user_idtelegram" => $this->getUserId()]
    );
  }

  /**
   * Updates the "state_name" and "state_data" to NULL
   */
  public function deleteState() {
    return DB::update("obc_users", 
      ["user_statename" => null, "user_statedata" => null], 
      ["user_idtelegram" => $this->getUserId()]
    );
  }

}