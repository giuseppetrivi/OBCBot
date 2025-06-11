<?php

namespace OBCBot\view;


/**
 * Class to handle all keyboards
 */
class Keyboards extends ViewWrapper {

  public const MAIN_MENU = [
    [MenuOptions::SEARCH_EU, MenuOptions::SEARCH_U]
  ];

  public const ONLY_BACK = [
    [MenuOptions::BACK]
  ];

  public const BACK_AND_MENU = [
    [MenuOptions::BACK],
    [MenuOptions::BACK_TO_MENU],
  ];


  use KeyboardsTrait;

}


/**
 * [Explaination of keyboard buttons]
 * 
 * "keyboard" attribute (in the API call) needs an array o array of KeyboardButton.
 * So, if you want for example this structure of buttons:
 * [ First button ][ Second button ]
 * [        Close all button       ]
 * (2 in the first row and 1 in the second one)
 * 
 * You have to declare this:
 * public const BUTTONS = [
 *  ["First button", "Second button"],
 *  ["Close all button"]
 * ]
 */