<?php

namespace CustomBotName\view;

use CustomBotName\utilities\DateTimeIT;

/**
 * 
 */
abstract class SearchTextMessages {
  
  /* This class is not callable, so constructor is private */
  protected function __construct() {}


  /**
   * @param string $name Can bea a location or a stop
   */
  public static function maybeYouMeant(string $name) {
    return "❓ Forse intendevi <i>$name</i>";
  }

  /**
   * Format a list of alternative options (between locations or stops)
   */
  abstract public static function alternativeOptions(array $options_info);

  abstract public static function entityNotFound(string $entity_to_search);


  public static function selectDatetime() {
    return "⌛ Seleziona la <b>data</b> e l'<b>ora</b> di partenza";
  }

  public static function summarySelectedDatetime(DateTimeIT $_SelectedDatetime) {
    return "📅  Data selezionata: <b>" . $_SelectedDatetime->getLiteralWeekDay() . " " . $_SelectedDatetime->format("d") . " " . $_SelectedDatetime->getLiteralMonth() . " " . $_SelectedDatetime->format("Y") . "</b>\n" .
      "🕒  Ora selezionata:   <b>" . $_SelectedDatetime->format("H:i") . "</b>";
  }


  /** */
  public static function searchResultsHeader($specific_search_info) {
    $departure_info = $specific_search_info["departure"];
    $arrival_info = $specific_search_info["arrival"];
    $_Datetime = new DateTimeIT($departure_info["sea_datetime"]);

    $departure_position = "https://www.google.com/maps?q=" . $departure_info["latitudine"] . "," . $departure_info["longitudine"];
    $arrival_position = "https://www.google.com/maps?q=" . $arrival_info["latitudine"] . "," . $arrival_info["longitudine"];

    $header = "🌐 Risultati per il giorno <b>" . $_Datetime->getLiteralWeekDay() . " " . $_Datetime->format("d") . " " . $_Datetime->getLiteralMonth() . " " . $_Datetime->format("Y") . "</b>"
      . ", dalle ore <b>" . $_Datetime->format("H:00") . "</b> in poi...\n\n"
      . "╒ <b>" . $departure_info["comune"] . "</b>, <a href='$departure_position'>" . $departure_info["fermata"] . "</a>\n"
      . "╘ <b>" . $arrival_info["comune"] . "</b>, <a href='$arrival_position'>" . $arrival_info["fermata"] . "</a>\n\n"
      . "🚌  <i>" . $departure_info["azienda"] . "</i>\n\n";

    return $header;
  }

  /** */
  public static function showSearchResults($specific_search_info, $search_results) {
    $_Datetime = new DateTimeIT($specific_search_info["departure"]["sea_datetime"]);
    $timetables = "";
    foreach($search_results as $ride) {
      if ($ride["oraPartenza"] < $_Datetime->format("H:00")) {
        continue;
      }

      $type = "";
      if ($ride["tipologiaFrequenzaCorsa1"]==2) {
        $type = "[<i>Scolastica</i>]";
      }
      $timetables .= "🕒  <b>" . $ride["oraPartenza"] . " ➝ " . $ride["oraArrivo"] . "</b> (" . $ride["durata"] . ") $type\n"
        . "💰  <i>€ " . number_format($ride["prezzoUnitario"], 2) . "</i>\n\n";
    }

    return self::searchResultsHeader($specific_search_info) . $timetables;
  }

  public static function noSearchResults($specific_search_info) {
    $error_message = "⚠️ Nessun risultato disponibile...\nProva a cambiare qualche parametro di ricerca";
    return self::searchResultsHeader($specific_search_info) . $error_message;
  }

  /**
   * Result message after selecting a stop name for the departure (or arrival, if $departure=false) 
   */
  public static function stopSelected(string $stop_name, bool $departure) {
    $type_of_location_text = $departure ? "fermata di partenza" : "fermata di arrivo";
    return "✅ Hai selezionato <b>$stop_name</b> come " . $type_of_location_text;
  }

  public static function errorDatetimeInThePast() {
    return "⚠️ Stai provando a selezionare una data + ora nel passato...";
  }
}