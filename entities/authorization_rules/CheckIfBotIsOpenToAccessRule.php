<?php

namespace CustomBotName\entities\authorization_rules;

use CustomBotName\config\ConfigurationHandler;


/**
 * Rule to check if the bot is open to users accesses (from config file)
 */
class CheckIfBotIsOpenToAccessRule extends Rule {

  public function __construct(ConfigurationHandler $_Config) {
    parent::__construct($_Config);
  }

  public function rule() {
    $_Config = $this->getValidationClass();
    if ($_Config->getOpenAccessToBot()) {
      return true;
    }
    return false;
  }

}

?>