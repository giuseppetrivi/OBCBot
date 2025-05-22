<?php

namespace CustomBotName\exceptions\state_exceptions;

use Exception;


class StateInputException extends Exception {

  protected const DEFAULT_MESSAGE = "Something went wrong in input processing";

  public function __construct($message=StateInputException::DEFAULT_MESSAGE, $code=0, $previous=null) {

    parent::__construct($message, $code, $previous);

  }
  
}

?>