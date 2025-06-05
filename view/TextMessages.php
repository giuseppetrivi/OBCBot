<?php

namespace CustomBotName\view;

use CustomBotName\entities\DateTimeIT;

/**
 * Final class containing all text messages
 */
final class TextMessages {
  
  /**
   * This class is not callable, so constructor is private
   */
  private function __construct() {}

  public static function inputError() {
    return "⚠️ Il messaggio inviato non è valido.\nInvia un messaggio valido per la procedura in cui ti trovi.";
  }

  public static function startingMessage($username) {
    return "👋 Ciao @$username!\n\n".
    "🤖 Con questo bot puoi cercare rapidamente gli orari delle tratte COTRAP, partendo dal menu principale qui sotto.";
  }
  
  public static function mainMenu() {
    return "🏠  Menu principale";
  }
  public static function mainMenuFromRestart() {
    return "↻ Hai riavviato il bot\n\n📜 Menu principale";
  }

  public static function chooseDepartureLocation() {
    return "📍 Invia il nome della <b>località di partenza</b>";
  }
  public static function chooseDepartureLocationAgain() {
    return "📍 Invia nuovamente il nome della <b>località di partenza</b>";
  }
  public static function departureLocationMatched($location_name) {
    return "✅ Hai selezionato <b>$location_name</b> come città di partenza";
  }

  public static function chooseArrivalLocation() {
    return "📍 Invia il nome della <b>località di arrivo</b>";
  }
  public static function chooseArrivalLocationAgain() {
    return "📍 Invia nuovamente il nome della <b>località di arrivo</b>";
  }
  public static function arrivalLocationMatched($location_name) {
    return "✅ Hai selezionato <b>$location_name</b> come città di arrivo";
  }

  public static function locationAlmostMatched($location_name) {
    return "❓ Forse intendevi <i>$location_name</i>";
  }
  public static function alternativeLocations($location_info) {
    $text = "🤔 Oppure cercavi:\n";
    foreach ($location_info as $index => $info) {
      $text .= "• <code>" . $info["location_name"] . "</code>\n";
    }
    return $text;
  }
  public static function locationNotMatched($location_to_search) {
    return "❌  La località <i>$location_to_search</i> non è tra le scelte possibili...";
  }


  public static function chooseDepartureStop() {
    return "🛑 Seleziona la <b>fermata di partenza</b> tra quelle proposte";
  }
  public static function chooseArrivalStop() {
    return "🛑 Seleziona la <b>fermata di arrivo</b> tra quelle proposte";
  }

  public static function errorInRetriveStops() {
    return "⚠️ Si è verificato un errore nella ricerca delle fermate";
  }


  public static function selectDatetime() {
    return "⌛ Seleziona la <b>data</b> e l'<b>ora</b> di partenza";
  }

  public static function recapDatetime(DateTimeIT $_SelectedDatetime) {
    return "📅  Data selezionata: <b>" . $_SelectedDatetime->format("d") . " " . $_SelectedDatetime->getLiteralMonth() . " " . $_SelectedDatetime->format("Y") . "</b>\n" .
      "🕒  Ora selezionata:   <b>" . $_SelectedDatetime->format("H:i") . "</b>";
  }


}