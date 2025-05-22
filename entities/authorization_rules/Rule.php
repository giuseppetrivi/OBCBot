<?php

namespace CustomBotName\entities\authorization_rules;

use ReflectionClass;
use CustomBotName\entities\BaseEntity;


/**
 * Class to create generic rules to verify if a user can use the bot
 */
abstract class Rule extends BaseEntity {

  protected $_ValidationClass = null;

  public function __construct($_ValidationClass) {
    $this->setValidationClass($_ValidationClass);
  }

  /**
   * Method to define the rule (this method should be overridden)
   * 
   * @return bool
   */
  abstract public function rule();


  /**
   * Error message in case of breaking rules (for logging)
   * 
   * @return string String to describe the error message
   */
  public function errorMessage() {
    $_ReflectionClass = new ReflectionClass($this);
    $error_message = "The rule check (" . $_ReflectionClass->getShortName() . ") has failed";
    return $error_message;
  }

}


?>