<?php

namespace OBCBot\exceptions;

use Exception;


class RuleException extends Exception {

  protected const DEFAULT_MESSAGE = "Something went wrong in rules checks";

  public function __construct($message=RuleException::DEFAULT_MESSAGE, $code=0, $previous=null) {

    parent::__construct($message, $code, $previous);

  }
  
}

?>