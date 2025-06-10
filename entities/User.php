<?php

namespace CustomBotName\entities;

use CustomBotName\utilities\DateTimeIT;
use DB;

/**
 * Class to handle all attributes of the user
 * The code of this class method depends on your database architecture
 */
class User extends BaseEntity {

  protected int $user_id;
  protected string $username;
  protected string $first_name;
  protected ?StateHandler $_StateHandler = null;


  /**
   * @param int $user_id Telegram user id
   */
  public function __construct(int $user_id, string $username, string $first_name) {    
    $this->setUserId($user_id);
    $this->setUsername($username);
    $this->setFirstName($first_name);
    
    $this->setStateHandler(new StateHandler($this->getUserId()));

    /* creates the user record in database if it doesn't exists */
    if (!$this->userExists()) {
      $this->insertUserInDatabase();
    }

    $this->updateLastActionDatetime();
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
      "user_idtelegram" => $this->getUserId(),
      "user_username" => $this->getUsername(),
      "user_firstname" => $this->getFirstName()
    ]);
  }


  private function updateLastActionDatetime() {
    $_Now = new DateTimeIT();
    return DB::update("obc_users", 
      [ "user_lastaction_datetime" => $_Now->currentDatetimeFormat() ], 
      [ "user_idtelegram" => $this->getUserId() ]);
  }
  

}


?>