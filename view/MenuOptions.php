<?php

namespace CustomBotName\view;

/**
 * Final class containing all menu options constants
 */
final class MenuOptions {

  public const COMMAND_START = '/start';
  public const COMMAND_RESTART = '/restart';

  public const SEARCH_EU = '🚌  Cerca Extraurbana';
  public const SEARCH_U = '🏘️  Cerca Urbana';
  public const SETTINGS = '⚙️  Impostazioni';

  public const BACK = "↩  Indietro";
  public const BACK_TO_MENU = "🏠  Torna al menu principale";

  
  /**
   * This class is not callable, so constructor is private
   */
  private function __construct() {}

}