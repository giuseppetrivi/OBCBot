<?php

namespace CustomBotName\view;

/**
 * Final class containing all text messages
 */
final class TextMessages {
  
  /**
   * This class is not callable, so constructor is private
   */
  private function __construct() {}


  public static function startingMessage($username) {
    return "👋 Ciao @$username!\n\n".
    "🤖 Con questo bot puoi cercare rapidamente gli orari delle tratte COTRAP";
  }
  
  public static function mainMenu() {
    return "📜 Menu principale";
  }

  public static function chooseDepartureLocation() {
    return "➤ Invia il nome della località di <u>partenza</u>";
  }
  public static function chooseDepartureLocationAgain() {
    return "➤ Invia nuovamente il nome della località di <u>partenza</u>";
  }

  public static function departureLocationValid100($location_name) {
    return "Hai selezionato <b>$location_name</b> come città di partenza";
  }
  public static function departureLocationValidNot100($location_name) {
    return "Forse intendevi <i>$location_name</i>";
  }
  public static function departureLocationNotValid($location_to_search) {
    return "La località <i>$location_to_search</i> non è tra le scelte possibili";
  }

  public static function chooseArrivalLocation() {
    return "➤ Invia il nome della località di <u>arrivo</u>";
  }
  public static function chooseArrivalLocationAgain() {
    return "➤ Invia nuovamente il nome della località di <u>arrivo</u>";
  }

  public static function arrivalLocationValid100($location_name) {
    return "Hai selezionato <b>$location_name</b> come città di arrivo";
  }
  public static function arrivalLocationValidNot100($location_name) {
    return "Forse intendevi <i>$location_name</i>";
  }
  public static function arrivalLocationNotValid($location_to_search) {
    return "La località <i>$location_to_search</i> non è tra le scelte possibili";
  }


}