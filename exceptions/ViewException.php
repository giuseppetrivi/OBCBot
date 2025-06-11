<?php

namespace OBCBot\exceptions;

use Exception;


class ViewException extends Exception {

  protected const DEFAULT_MESSAGE = "Something went wrong in view composition";

  public function __construct($message=ViewException::DEFAULT_MESSAGE, $code=0, $previous=null) {

    parent::__construct($message, $code, $previous);

  }
  
}

?>