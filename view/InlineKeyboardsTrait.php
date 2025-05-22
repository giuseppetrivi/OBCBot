<?php

namespace CustomBotName\view;

use Telegram\Bot\Keyboard\Keyboard;


/**
 * 
 */
trait InlineKeyboardsTrait {
  protected static function createInlineKeyboard($inline_keyboard) {
    return Keyboard::make([
      'inline_keyboard' => $inline_keyboard,
    ]);
  }
}


?>