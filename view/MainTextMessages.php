<?php

namespace CustomBotName\view;

use CustomBotName\utilities\DateTimeIT;

/**
 * Final class containing all text messages
 */
final class MainTextMessages {

  /* This class is not callable, so constructor is private */
  private function __construct() {}


  /**
   * Message for invalid input in the state
   */
  public static function inputError() {
    return "⚠️ Il messaggio inviato non è valido.\nInvia un messaggio valido per la procedura in cui ti trovi.";
  }

  /**
   * Message after /start command
   */
  public static function welcome($username) {
    return "👋 Ciao @$username!\n\n".
    "🤖 Con questo bot puoi cercare rapidamente gli orari delle tratte COTRAP, partendo dal menu principale qui sotto.";
  }
  
  public static function mainMenu() {
    return "🏠 Menu principale";
  }

  /**
   * Message after /restart command
   */
  public static function restarted() {
    return "↻ Hai riavviato il bot";
  }


  /** */
  public static function chooseBetweenMostFrequentRoutes() {
    return "📈 Le seguenti sono le tratte che hai cercato più di frequente.\n\n" .
      "👉 Clicca su una di esse per avviare una ricerca rapida, oppure su 🔁 per cercare la tratta inversa (se possibile)";
  }

}