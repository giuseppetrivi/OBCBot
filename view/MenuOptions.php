<?php

namespace OBCBot\view;

/**
 * Final class containing all menu options constants
 */
final class MenuOptions {

  public const COMMAND_START = "/start";
  public const COMMAND_RESTART = "/restart";

  public const SEARCH_EU = "🚌 🔎  Extraurbana";
  public const SEARCH_U = "🏢 🔎  Urbana";

  public const FAST_SEARCH = "⚡  Avvia la ricerca rapida";

  public const BACK = "↩  Indietro";
  public const BACK_TO_MENU = "🏠  Torna al menu principale";

  
  /**
   * This class is not callable, so constructor is private
   */
  private function __construct() {}

}