<?php

namespace CustomBotName\entities;

use DB;

/**
 * Class to handle all attributes of the user
 * The code of this class method depends on your database architecture
 */
class User extends BaseEntity {

  protected int $user_id;
  protected ?StateHandler $_StateHandler = null;


  /**
   * @param int $user_id Telegram user id
   */
  public function __construct(int $user_id) {    
    $this->setUserId($user_id);
    $this->setStateHandler(new StateHandler($this->getUserId()));

    /* creates the user record in database if it doesn't exists */
    if (!$this->userExists()) {
      $this->insertUserInDatabase();
    }
  }


  private function userExists() {
    $result = DB::query("SELECT * FROM obc_users WHERE user_idtelegram=%s_user_idtelegram", [
      "user_idtelegram" => $this->getUserId()
    ]);

    if (count($result)==1) {
      return true;
    }
    return false;
  }

  private function insertUserInDatabase() {
    return DB::insert("obc_users", [
      "user_idtelegram" => $this->getUserId()
    ]);
  }
  

}


?>