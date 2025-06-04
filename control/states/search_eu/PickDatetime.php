<?php

namespace SearchEU\DepartureLocation\ArrivalLocation\DepartureStop\ArrivalStop;

use CustomBotName\control\AbstractState;
use CustomBotName\entities\api_cotrap\LocationStops;
use CustomBotName\entities\api_cotrap\SearchEU;
use CustomBotName\entities\DatetimeHandler;
use CustomBotName\entities\DateTimeIT;
use CustomBotName\view\InlineKeyboards;
use CustomBotName\view\Keyboards;
use CustomBotName\view\MenuOptions;
use CustomBotName\view\TextMessages;

class PickDatetime extends AbstractState {

  protected array $valid_static_inputs = [
    MenuOptions::BACK => "backProcedure"
  ];

  protected function validateDynamicInputs() {
    $input_text = $this->_Bot->getInputFromChat()->getText();
    /* regex to get callback_data to select day */
    $complete_date_regex = "/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/";
    $match_result = preg_match($complete_date_regex, $input_text);
    if ($match_result) {
      $this->function_to_call = "selectDateProcedure";
      return true;
    }

    /* regex to get callback_data to select month (and year) */
    $yearmonth_regex = "/^\d{4}-(0[1-9]|1[0-2])$/";
    $match_result = preg_match($yearmonth_regex, $input_text);
    if ($match_result) {
      $this->function_to_call = "selectMonthProcedure";
      return true;
    }

    if ($input_text=="next_month") {
      $this->function_to_call = "selectNextMonthProcedure";
      return true; 
    }
  }

  /**
   * States:
   * SearchEU\DepartureLocation\ArrivalLocation\DepartureStop\ArrivalStop\PickDatetime 
   *  -> SearchEU\DepartureLocation\ArrivalLocation\DepartureStop\ArrivalStop
   */
  protected function backProcedure() {
    // TODO
  }

  /**
   * 
   */
  protected function selectDateProcedure() {
    $date_selected = $this->_Bot->getInputFromChat()->getText();
    $message_id = $this->_Bot->getWebhookUpdate()->getMessage()->getMessageId();
    $_SelectedDatetime = new DateTimeIT($date_selected);

    $this->_Bot->editMessageText([
      "message_id" => $message_id,
      "text" => TextMessages::selectDatetime() . "\n\n" . TextMessages::recapDatetime($_SelectedDatetime),
      "reply_markup" => InlineKeyboards::calendar($_SelectedDatetime)
    ]);
    
    $this->keepThisState();
  }

  /**
   * 
   */
  protected function selectNextMonthProcedure() {
    
  }


}

?>