<?php

namespace CustomBotName\view;

use Telegram\Bot\Keyboard\Keyboard;


/**
 * Specific function to create inline keyboard
 */
trait InlineKeyboardsTrait {
  protected static function createInlineKeyboard($inline_keyboard) {
    return Keyboard::make([
      'inline_keyboard' => $inline_keyboard,
    ]);
  }
}


?>