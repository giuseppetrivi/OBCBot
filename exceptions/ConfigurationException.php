<?php

namespace OBCBot\exceptions;

use Exception;


class ConfigurationException extends Exception {

  protected const DEFAULT_MESSAGE = "Something went wrong in configuration handler operations";

  public function __construct($message=ConfigurationException::DEFAULT_MESSAGE, $code=0, $previous=null) {

    parent::__construct($message, $code, $previous);

  }
  
}

?>