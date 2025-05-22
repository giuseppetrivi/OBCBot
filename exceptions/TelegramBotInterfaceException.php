<?php

namespace CustomBotName\exceptions;

use Exception;


class TelegramBotInterfaceException extends Exception {

  protected const DEFAULT_MESSAGE = "Something went wrong in Telegram Bot API interface handler";

  public function __construct($message=TelegramBotInterfaceException::DEFAULT_MESSAGE, $code=0, $previous=null) {

    parent::__construct($message, $code, $previous);

  }
  
}

?>