<?php

namespace CustomBotName\view;

/**
 * Class to handle all inline keyboards 
 */
class InlineKeyboards extends ViewWrapper {

  private const MAX_INLINE_BUTTONS = 100;

  public const LIST_BUTTON_NAVIGATION = [
    [
      [
        "text" => "\xE2\xAC\x85 Pagina precedente",
        "callback_data" => "back"
      ],
      [
        "text" => "Prossima pagina \xE2\x9E\xA1",
        "callback_data" => "forward"
      ]
    ]
  ];
  

  use InlineKeyboardsTrait;

  public static function locationStops($location_stops) {
    $inline_keyboard = [];
    foreach($location_stops as $info) {
      array_push($inline_keyboard, [[
        "text" => $info["denominazione"],
        "callback_data" => "polo_" . $info["id"]
      ]]);
    }
    
    return InlineKeyboards::createInlineKeyboard($inline_keyboard);
  }

}


/**
 * [Explaination of inline keyboard buttons]
 * 
 * "inline_keyboard" attribute (in the API call) needs an array o array of InlineKeyboardButton.
 * So, if you want for example this structure of buttons:
 * [ First button ][ Second button ]
 * [        Close all button       ]
 * (2 in the first row and 1 in the second one)
 * 
 * You have to declare this:
 * public const INLINE_BUTTONS = [
 *  [
 *    [
 *      "text" => "First button",
 *      "url" => "https://google.it"
 *    ],
 *    [
 *      "text" => "Second button",
 *      "url" => "https://google.it"
 *    ],
 *  ],
 *  [
 *    [
 *      "text" => "Close all button",
 *      "url" => "https://google.it"
 *    ]
 *  ],
 * ]
 */