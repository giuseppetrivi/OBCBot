<?php

namespace CustomBotName\exceptions\state_exceptions;

use Exception;


class StateFunctionException extends Exception {

  protected const DEFAULT_MESSAGE = "Something went wrong in process function callback";

  public function __construct($message=StateFunctionException::DEFAULT_MESSAGE, $code=0, $previous=null) {

    parent::__construct($message, $code, $previous);

  }
  
}

?>