<?php

namespace CustomBotName\view;

use CustomBotName\entities\DateTimeIT;
use DateTime;

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


  public static function calendar(DateTimeIT $_SelectedDatetime) {

    $_TodayDatetime = new DateTimeIT(date("Y-m-d H:00"));
    $_ReferenceMonth = new DateTimeIT($_SelectedDatetime->format("Y-m-01"));
    if ($_TodayDatetime < $_ReferenceMonth) {
      $_TodayDatetime = $_ReferenceMonth;
    }

    $month = $_TodayDatetime->format("m");
    $actual_month = $month;

    /* month handler */
    $calendar_keyboard[0]= [
      [
        "text" => "◀️",
        "callback_data" => "previous_month"
      ],
      [
        "text" => $_TodayDatetime->getLiteralMonth() . " " . $_TodayDatetime->format("Y"),
        "callback_data" => $_TodayDatetime->format("Y-m")
      ],
      [
        "text" => "▶️",
        "callback_data" => "next_month"
      ]
    ];

    /* week day header */
    $calendar_keyboard[1]= [
      [
        "text" => "Lun",
        "callback_data" => "week_day"
      ],
      [
        "text" => "Mar",
        "callback_data" => "week_day"
      ],
      [
        "text" => "Mer",
        "callback_data" => "week_day"
      ],
      [
        "text" => "Gio",
        "callback_data" => "week_day"
      ],
      [
        "text" => "Ven",
        "callback_data" => "week_day"
      ],
      [
        "text" => "Sab",
        "callback_data" => "week_day"
      ],
      [
        "text" => "Dom",
        "callback_data" => "week_day"
      ],
    ];

    $empty_day = [
      "text" => " ",
      "callback_data" => "blank_day"
    ];
    
    /* calendar for date */
    $counter_week = 2;
    $calendar_keyboard[$counter_week] = array_fill(0, 7, $empty_day);
    $start = 0;
    while ($month==$actual_month) {
      $start++;
      $week_day = $_TodayDatetime->format("w")==0 ? 6 : ($_TodayDatetime->format("w")-1);

      if ($week_day==0 && $start>1) {
        $counter_week++;
        $calendar_keyboard[$counter_week] = array_fill(0, 7, $empty_day);
      }

      $day_text = $_TodayDatetime->format("d");
      $day_callback = $_TodayDatetime->format("Y-m-d");
      if ($_TodayDatetime->format("Y-m-d")==$_SelectedDatetime->format("Y-m-d")) {
        $day_text = "[ " . $day_text . " ]";
      }

      $calendar_keyboard[$counter_week][$week_day] = [
        "text" => $day_text,
        "callback_data" => $day_callback
      ];

      $_TodayDatetime->modify("+1 day");
      $actual_month = $_TodayDatetime->format("m");
    }

    /* time selector */
    $calendar_keyboard[$counter_week+1]= [
      [
        "text" => "◀️",
        "callback_data" => "previous_hour"
      ],
      [
        "text" => $_SelectedDatetime->format("H:00"),
        "callback_data" => $_SelectedDatetime->format("H:00")
      ],
      [
        "text" => "▶️",
        "callback_data" => "next_hour"
      ]
    ];

    /* search button */
    $calendar_keyboard[$counter_week+2]= [
      [
        "text" => "🔎  Avvia la ricerca",
        "callback_data" => "search"
      ]
    ];

    return InlineKeyboards::createInlineKeyboard($calendar_keyboard);
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