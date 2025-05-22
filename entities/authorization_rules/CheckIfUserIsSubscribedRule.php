<?php

namespace CustomBotName\entities\authorization_rules;

use CustomBotName\entities\User;


/**
 * Rule to check if the user is subscripted to bot (from user info)
 */
class CheckIfUserIsSubscribedRule extends Rule {

  public function __construct(User $_User) {
    parent::__construct($_User);
  }

  public function rule() {
    $_User = $this->getValidationClass();
    if ($_User->isSubscripted()) {
      return true;
    }
    return false;
  }

}

?>