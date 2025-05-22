<?php

namespace CustomBotName\entities\telegrambot_sdk_interface;

use CustomBotName\entities\BaseEntity;


/**
 * Class to handle all info about the input from chatbot
 */
class InputFromChat extends BaseEntity {

  public $message_type = null;
  public $text = null;


  public function __construct($text, $message_type) {
    $this->text = $text;
    $this->message_type = $message_type;
  }

}

?>