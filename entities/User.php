<?php

namespace CustomBotName\entities;


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
    /** db query to get unique user info */
    
    $this->setUserId($user_id);
    $this->setStateHandler(new StateHandler($this->getUserId()));
  }
  

}


?>