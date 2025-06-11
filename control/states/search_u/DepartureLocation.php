<?php

namespace SearchU;

use OBCBot\control\AbstractState;
use OBCBot\entities\api_cotrap\LocationsU;
use OBCBot\entities\api_cotrap\SearchU;
use OBCBot\entities\telegrambot_sdk_interface\InputTypes;
use OBCBot\view\MenuOptions;
use OBCBot\view\Keyboards;
use OBCBot\view\MainTextMessages;
use OBCBot\view\SearchUTextMessages;


class DepartureLocation extends AbstractState {

  protected array $valid_static_inputs = [
    MenuOptions::BACK => "backProcedure",
    MenuOptions::BACK_TO_MENU => "backProcedure"
  ];


  protected function validateDynamicInputs() {
    $input_text = $this->_Bot->getInputFromChat()->getText();
    $input_type = $this->_Bot->getInputFromChat()->getMessageType();

    /* TODO */
    $urban_location_id_regex = "/^location_\d+$/";
    if (preg_match($urban_location_id_regex, $input_text) && $input_type==InputTypes::CALLBACK_QUERY) {
      $this->function_to_call = "selectDepartureLocationProcedure";
      return true;
    }
  }


  /**
   * States:
   * SearchU\DepartureLocation -> NULL (Main)
   */
  protected function backProcedure() {
    $_SearchU = new SearchU($this->_User->getUserId());
    $_SearchU->destroySearch();

    $this->_Bot->sendMessage([
      'text' => MainTextMessages::mainMenu(),
      'reply_markup' => Keyboards::getMainMenu()
    ]);

    $this->setNextState(NULL);
  }


  /**
   * States:
   * SearchU\DepartureLocation 
   *  -> SearchU\DepartureLocation\DepartureStop
   *     SearchU\DepartureLocation
   */
  protected function selectDepartureLocationProcedure() {
    $urban_location_id = explode("_", $this->_Bot->getInputFromChat()->getText())[1];

    // TODO: verificare il codice della località urbana

    $_SearchU = new SearchU($this->_User->getUserId());
    $_SearchU->setDepartureLocation($urban_location_id);

    $_LocationsU = new LocationsU();
    $urban_location_name = $_LocationsU->getUrbanLocationInfoById($urban_location_id)["denominazione"];

    $this->_Bot->sendMessage([
      'text' => SearchUTextMessages::locationSelected($urban_location_name)
    ]);

    $this->_Bot->sendMessage([
      'text' => SearchUTextMessages::chooseUrbanStop(true),
      'reply_markup' => Keyboards::getBackAndMenu()
    ]);

    $this->setNextState($this->appendNextState("DepartureStop"));
  }

}

?>