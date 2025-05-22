<?php

namespace CustomBotName\view;

use Telegram\Bot\Keyboard\Keyboard;


/**
 * 
 */
trait KeyboardsTrait {
  protected static function createKeyboard($keyboard) {
    return Keyboard::make([
      'keyboard' => $keyboard,
      'resize_keyboard' => true
    ]);
  }
}


?>