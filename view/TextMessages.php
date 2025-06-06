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
  public static function departureLocationSelected($location_name) {
    return "✅ Hai selezionato <b>$location_name</b> come città di partenza";
  }

  public static function chooseArrivalLocation() {
    return "📍 Invia il nome della <b>località di arrivo</b>";
  }
  public static function chooseArrivalLocationAgain() {
    return "📍 Invia nuovamente il nome della <b>località di arrivo</b>";
  }
  public static function arrivalLocationSelected($location_name) {
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

  public static function departureStopSelected($stop_name) {
    return "✅ Hai selezionato <b>$stop_name</b> come fermata di partenza";
  }
  public static function arrivalStopSelected($stop_name) {
    return "✅ Hai selezionato <b>$stop_name</b> come fermata di arrivo";
  }


  public static function selectDatetime() {
    return "⌛ Seleziona la <b>data</b> e l'<b>ora</b> di partenza";
  }

  public static function recapDatetime(DateTimeIT $_SelectedDatetime) {
    return "📅  Data selezionata: <b>" . $_SelectedDatetime->getLiteralWeekDay() . " " . $_SelectedDatetime->format("d") . " " . $_SelectedDatetime->getLiteralMonth() . " " . $_SelectedDatetime->format("Y") . "</b>\n" .
      "🕒  Ora selezionata:   <b>" . $_SelectedDatetime->format("H:i") . "</b>";
  }


  public static function showSearchResults($search_results, $_Datetime) {
    $first = $search_results[0];
    $departure_position = "https://www.google.com/maps?q=" . $first["latitudinePoloPartenza"] . "," . $first["longitudinePoloPartenza"];
    $arrival_position = "https://www.google.com/maps?q=" . $first["latitudinePoloArrivo"] . "," . $first["longitudinePoloArrivo"];

    $header = "🌐 Risultati per il giorno <b>" . $_Datetime->getLiteralWeekDay() . " " . $_Datetime->format("d") . " " . $_Datetime->getLiteralMonth() . " " . $_Datetime->format("Y") . "</b>"
      . ", dalle ore <b>" . $_Datetime->format("H:00") . "</b> in poi...\n\n"
      . "╒ <b>" . $first["localitaPartenza"] . "</b>, <a href='$departure_position'>" . $first["denominazionePartenza"] . "</a>\n"
      . "╘ <b>" . $first["localitaArrivo"] . "</b>, <a href='$arrival_position'>" . $first["denominazioneArrivo"] . "</a>\n\n"
      . "🚌  <i>" . $first["azienda1"] . "</i>\n\n";

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

    return $header . $timetables;
  }

  public static function noSearchResults() {
    return "⚠️ Nessun risultato disponibile. Prova a cambiare qualche parametro di ricerca";
  }

  public static function urbanSearchHeader() {
    return "🌇 Questo è il procedimento per cercare le tratte urbane";
  }
  public static function chooseUrbanLocation() {
    return "📍 Scegli una <b>località tra le seguenti</b>";
  }
  public static function urbanDepartureLocationSelected($location_name) {
    return "✅ Hai selezionato <b>$location_name</b> come località";
  }

  public static function chooseUrbanDepartureStop() {
    return "🛑 Invia il nome della <b>fermata di partenza</b>";
  }
  public static function chooseUrbanDepartureStopAgain() {
    return "🛑 Invia nuovamente il nome della <b>fermata di partenza</b>";
  }
  public static function chooseUrbanArrivalStop() {
    return "🛑 Invia il nome della <b>fermata di arrivo</b>";
  }
  public static function chooseUrbanArrivalStopAgain() {
    return "🛑 Invia nuovamente il nome della <b>fermata di arrivo</b>";
  }

  public static function stopAlmostMatched($stop_name) {
    return "❓ Forse intendevi <i>$stop_name</i>";
  }
  public static function alternativeStops($stops_info) {
    $text = "🤔 Forse cercavi:\n";
    foreach ($stops_info as $index => $info) {
      $text .= "• <code>" . $info["stop_name"] . "</code>\n";
    }
    return $text;
  }
  
  

}