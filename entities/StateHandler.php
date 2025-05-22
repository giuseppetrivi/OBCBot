<?php

namespace CustomBotName\entities;

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
   * 
   */
  public function getStateName() {
    /** [Example of] db query to get user state info */
    /*
    $result = DB::queryFirstRow("SELECT * FROM [CHANGE THIS: state_table] WHERE [CHANGE THIS: user_id]=%i", $this->getUserId());
    if ($result) { 
      return $result['state_path'];
    }
    else {
      // If there is no state in database, so you need to start the Main state class
      return "Main";
    }
    */
  }


  /**
   * 
   */
  public function createNewState() {
    /** db query to insert a new state for the user */
  }

  /**
   * 
   */
  public function updateState($new_state_name, $new_state_data=null) {
    /** db query to update user state attributes */
  }

  /**
   * 
   */
  public function updateOnlyStateData($new_state_data) {
    /** db query to update user state data */
  }

  /**
   * 
   */
  public function deleteState() {
    /** db query to delete user state */
  }

}