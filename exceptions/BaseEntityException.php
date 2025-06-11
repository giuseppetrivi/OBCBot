<?php

namespace OBCBot\exceptions;

use Exception;


class BaseEntityException extends Exception {

  protected const DEFAULT_MESSAGE = "Something went wrong in entity";

  public function __construct($message=BaseEntityException::DEFAULT_MESSAGE, $code=0, $previous=null) {

    parent::__construct($message, $code, $previous);

  }
  
}

?>