<?php

namespace SearchU;

use CustomBotName\control\AbstractState;
use CustomBotName\entities\api_cotrap\LocationsEU;
use CustomBotName\entities\api_cotrap\LocationsU;
use CustomBotName\entities\api_cotrap\SearchEU;
use CustomBotName\entities\api_cotrap\SearchU;
use CustomBotName\entities\telegrambot_sdk_interface\InputTypes;
use CustomBotName\view\Keyboards;
use CustomBotName\view\MenuOptions;
use CustomBotName\view\TextMessages;


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
      'text' => TextMessages::mainMenu(),
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

    $this->_Bot->sendMessage([
      'text' => TextMessages::chooseUrbanDepartureStop(),
      'reply_markup' => Keyboards::getBackAndMenu()
    ]);

    $this->setNextState($this->appendNextState("DepartureStop"));
  }

}

?>