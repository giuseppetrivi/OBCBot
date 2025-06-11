<?php

namespace OBCBot\view;

use OBCBot\utilities\DateTimeIT;


/**
 * Class to handle all inline keyboards 
 */
class InlineKeyboards extends ViewWrapper {  

  use InlineKeyboardsTrait;


  /**
   * List of location stops
   */
  public static function locationStops($location_stops) {
    $inline_keyboard = [];
    foreach($location_stops as $info) {
      array_push($inline_keyboard, [[
        "text" => $info["denominazione"],
        "callback_data" => "stop_" . $info["id"]
      ]]);
    }
    
    return InlineKeyboards::createInlineKeyboard($inline_keyboard);
  }


  /**
   * List of most frequent routes
   */
  public static function mostFrequentRoutesList($most_frequent_routes) {
    $inline_keyboard = [];
    foreach($most_frequent_routes as $info) {
      array_push($inline_keyboard, [
        [
          "text" => $info["tratta"],
          "callback_data" => "route_" . $info["idPartenza"] . "_" . $info["idArrivo"]
        ],
        [
          "text" => "🔁",
          "callback_data" => "route_" . $info["idArrivo"] . "_" . $info["idPartenza"]
        ]
      ]);
    }
    
    return InlineKeyboards::createInlineKeyboard($inline_keyboard);
  }


  /**
   * List of urban location
   */
  public static function urbanLocationsList($all_urban_locations) {
    $inline_keyboard = [];
    foreach($all_urban_locations as $info) {
      array_push($inline_keyboard, [[
        "text" => $info["denominazione"],
        "callback_data" => "location_" . $info["codice"]
      ]]);
    }
    
    return InlineKeyboards::createInlineKeyboard($inline_keyboard);
  }


  /**
   * Calendar generator and datetime picker
   */
  public static function calendar(DateTimeIT $_SelectedDatetime) {
    $_TodayDatetime = new DateTimeIT(date(DateTimeIT::DATABASE_FORMAT));
    $_FirstDayMonth = new DateTimeIT($_SelectedDatetime->format("Y-m-01"));
    if ($_TodayDatetime < $_FirstDayMonth) {
      $_TodayDatetime = $_FirstDayMonth;
    }

    /* month selector */
    $index_keyboard_row = 0;
    $calendar_keyboard[$index_keyboard_row]= [
      [
        "text" => "📅  Scegli il giorno di partenza",
        "callback_data" => "blank"
      ]
    ];
    $index_keyboard_row++;
    $calendar_keyboard[$index_keyboard_row]= [
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
    $index_keyboard_row++;
    $calendar_keyboard[$index_keyboard_row]= [
      [
        "text" => "Lun",
        "callback_data" => "blank"
      ],
      [
        "text" => "Mar",
        "callback_data" => "blank"
      ],
      [
        "text" => "Mer",
        "callback_data" => "blank"
      ],
      [
        "text" => "Gio",
        "callback_data" => "blank"
      ],
      [
        "text" => "Ven",
        "callback_data" => "blank"
      ],
      [
        "text" => "Sab",
        "callback_data" => "blank"
      ],
      [
        "text" => "Dom",
        "callback_data" => "blank"
      ],
    ];

    /* calendar for date */
    $month = $_TodayDatetime->format("m");
    $actual_month = $month;

    $empty_day = [
      "text" => " ",
      "callback_data" => "blank"
    ];
    
    $counter_week = ++$index_keyboard_row;
    $calendar_keyboard[$counter_week] = array_fill(0, 7, $empty_day);
    $start = false;
    while ($month==$actual_month) {
      $week_day = $_TodayDatetime->format("w")==0 ? 6 : ($_TodayDatetime->format("w")-1);

      if ($week_day==0 && $start) {
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
      $start = true;
    }
    $index_keyboard_row = $counter_week;

    /* time selector */
    $index_keyboard_row++;
    $calendar_keyboard[$index_keyboard_row]= [
      [
        "text" => "🕒  Scegli l'orario di partenza",
        "callback_data" => "blank"
      ]
    ];

    $_Hours = new DateTimeIT("1970-01-01 00:00:00");
    for ($row=1; $row<=6; $row++) {
      $index_keyboard_row++;
      $calendar_keyboard[$index_keyboard_row] = array_fill(0, 4, " ");

      for ($col=0; $col<4; $col++) {
        $time = $_Hours->format("H:00");
        if ($_SelectedDatetime->format("H:00") == $_Hours->format("H:00")) {
          $time = "[ " . $time . " ]";
        }
        $calendar_keyboard[$index_keyboard_row][$col] = [
          "text" => $time,
          "callback_data" => $_Hours->format("H:00")
        ];
        $_Hours->modify("+1 hour");
      }
    }

    /* // dynamic time selector
    $calendar_keyboard[$index_keyboard_row]= [
      [
        "text" => "☀︎ sempre",
        "callback_data" => "all_day"
      ],
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
    ];*/

    /* search button */
    $index_keyboard_row++;
    $calendar_keyboard[$index_keyboard_row]= [
      [
        "text" => "🔎  Avvia la ricerca",
        "callback_data" => "search"
      ]
    ];

    return InlineKeyboards::createInlineKeyboard($calendar_keyboard);
  }


  /**
   * Inline button to open the web page with search results
   */
  public static function websiteResultsLink($url) {
    $url = "https://biglietteria.cotrap.it/#/ricerca/itinerari;" . str_replace("&", ";", explode("?", $url)[1]);
    return InlineKeyboards::createInlineKeyboard([[
      [
        "text" => "🌍  Visualizza la pagina web con i risultati",
        "url" => $url
      ]
    ]]);
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