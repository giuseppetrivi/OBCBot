<?php

namespace OBCBot\entities\authorization_rules;

use OBCBot\entities\User;


/**
 * Rule to check if the user is active to bot (from user info)
 */
class CheckIfUserIsActiveRule extends Rule {

  public function __construct(User $_User) {
    parent::__construct($_User);
  }

  public function rule() {
    $_User = $this->getValidationClass();
    if ($_User->isActive()) {
      return true;
    }
    return false;
  }

}

?>