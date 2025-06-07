<?php

namespace SearchU\DepartureLocation\DepartureStop\ArrivalStop;

use CustomBotName\control\AbstractState;
use CustomBotName\entities\api_cotrap\ApiCotrapRequestHandler;
use CustomBotName\entities\api_cotrap\SearchU;
use CustomBotName\entities\telegrambot_sdk_interface\InputTypes;
use CustomBotName\utilities\DateTimeIT;
use CustomBotName\view\MenuOptions;
use CustomBotName\view\InlineKeyboards;
use CustomBotName\view\SearchUTextMessages;
use BackToMenuTrait;

class PickDatetime extends AbstractState {

  protected array $valid_static_inputs = [
    MenuOptions::BACK => "backProcedure",
    MenuOptions::BACK_TO_MENU => "backToMenuProcedure"
  ];

  protected function validateDynamicInputs() {
    $input_text = $this->_Bot->getInputFromChat()->getText();
    $input_type = $this->_Bot->getInputFromChat()->getMessageType();

    /* regex to get callback_data to select day */
    $complete_date_regex = "/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])$/";
    $yearmonth_regex = "/^\d{4}-(0[1-9]|1[0-2])$/";
    $hour_regex = "/^([01]\d|2[0-3]):00$/";

    if (preg_match($complete_date_regex, $input_text) && $input_type==InputTypes::CALLBACK_QUERY) {
      $this->function_to_call = "selectDateProcedure";
      return true;
    }
    /* regex to get callback_data to select month (and year) */
    else if (preg_match($yearmonth_regex, $input_text) && $input_type==InputTypes::CALLBACK_QUERY) {
      $this->function_to_call = "selectMonthProcedure";
      return true;
    }
    /* regex to get callback_data to select hour */
    else if (preg_match($hour_regex, $input_text) && $input_type==InputTypes::CALLBACK_QUERY) {
      $this->function_to_call = "selectHourProcedure";
      return true;
    }
    /* select the next month */
    else if ($input_text=="next_month" && $input_type==InputTypes::CALLBACK_QUERY) {
      $this->function_to_call = "selectNextMonthProcedure";
      return true; 
    }
    /* select the previous month */
    else if ($input_text=="previous_month" && $input_type==InputTypes::CALLBACK_QUERY) {
      $this->function_to_call = "selectPreviousMonthProcedure";
      return true; 
    }
    else if ($input_text=="search" && $input_type==InputTypes::CALLBACK_QUERY) {
      $this->function_to_call = "searchProcedure";
      return true; 
    }
    else if ( $input_text=="blank" && $input_type==InputTypes::CALLBACK_QUERY) {
      $this->function_to_call = "emptyProcedure"; // TODO: da cambiare (procedura vuota)
      return true; 
    }

    return false;
  }


  /**
   * States:
   * SearchU\DepartureLocation\DepartureStop\ArrivalStop\PickDatetime 
   *  -> SearchU\DepartureLocation\DepartureStop\ArrivalStop
   */
  protected function backProcedure() {
    $_SearchU = new SearchU($this->_User->getUserId());
    $_SearchU->unsetArrivalStop();

    $this->_Bot->sendMessage([
      'text' => SearchUTextMessages::chooseUrbanStop(false)
    ]);

    $this->setNextState($this->getPreviousState());
  }

  /**
   * 
   */
  use BackToMenuTrait;


  /**
   * Procedure to change the date (pressing the day button)
   */
  protected function selectDateProcedure() {
    $date_selected = $this->_Bot->getInputFromChat()->getText();
    $message_id = $this->_Bot->getWebhookUpdate()->getMessage()->getMessageId();

    $_SearchU = new SearchU($this->_User->getUserId());
    $hour = explode(" ", $_SearchU->getSearchInfo()["sea_datetime"])[1];

    $_SelectedDatetime = new DateTimeIT($date_selected . " " . $hour);
    /* check if the date is in the past (in this case sets today's date) */
    switch ($_SelectedDatetime->isDatetimeInThePast()) {
      case 0:
        break;
      case -1:
        $_SelectedDatetime = new DateTimeIT();
      case 1:
        $_SearchU->setDatetime($_SelectedDatetime->databaseFormat());

        $this->_Bot->editMessageText([
          "message_id" => $message_id,
          "text" => SearchUTextMessages::selectDatetime() . "\n\n" . SearchUTextMessages::summarySelectedDatetime($_SelectedDatetime),
          "reply_markup" => InlineKeyboards::calendar($_SelectedDatetime)
        ]);
        break;
    }
    
    $this->keepThisState();
  }


  /**
   * Procedure to advance the date by one month
   */
  protected function selectNextMonthProcedure() {
    $message_id = $this->_Bot->getWebhookUpdate()->getMessage()->getMessageId();

    $_SearchU = new SearchU($this->_User->getUserId());
    $datetime = $_SearchU->getSearchInfo()["sea_datetime"];
    
    $_SelectedDatetime = new DateTimeIT($datetime);
    $_SelectedDatetime->modify("+1 month");

    $_SearchU->setDatetime($_SelectedDatetime->databaseFormat());

    $this->_Bot->editMessageText([
      "message_id" => $message_id,
      "text" => SearchUTextMessages::selectDatetime() . "\n\n" . SearchUTextMessages::summarySelectedDatetime($_SelectedDatetime),
      "reply_markup" => InlineKeyboards::calendar($_SelectedDatetime)
    ]);
    
    $this->keepThisState();
  }

  /**
   * Procedure to go back the date by one month (checking to not go in the past)
   */
  protected function selectPreviousMonthProcedure() {
    $message_id = $this->_Bot->getWebhookUpdate()->getMessage()->getMessageId();

    $_SearchU = new SearchU($this->_User->getUserId());
    $datetime = $_SearchU->getSearchInfo()["sea_datetime"];
    
    $_SelectedDatetime = new DateTimeIT($datetime);
    $_SelectedDatetime->modify("-1 month");
    /* check if the date is in the past (in this case sets today's date) */
    switch ($_SelectedDatetime->isDatetimeInThePast()) {
      case 0:
        break;
      case -1:
        $_SelectedDatetime = new DateTimeIT();
      case 1:
        $_SearchU->setDatetime($_SelectedDatetime->databaseFormat());

        $this->_Bot->editMessageText([
          "message_id" => $message_id,
          "text" => SearchUTextMessages::selectDatetime() . "\n\n" . SearchUTextMessages::summarySelectedDatetime($_SelectedDatetime),
          "reply_markup" => InlineKeyboards::calendar($_SelectedDatetime)
        ]);
        break;
    }

    $this->keepThisState();
  }


  /**
   * Procedure to set hour
   */
  protected function selectHourProcedure() {
    $hour_selected = $this->_Bot->getInputFromChat()->getText();
    $message_id = $this->_Bot->getWebhookUpdate()->getMessage()->getMessageId();

    $_SearchU = new SearchU($this->_User->getUserId());
    $date = explode(" ", $_SearchU->getSearchInfo()["sea_datetime"])[0];

    $_SelectedDatetime = new DateTimeIT($date . " " . $hour_selected);
    /* check if the date is in the past (in this case sets today's date) */
    switch ($_SelectedDatetime->isDatetimeInThePast()) {
      case 0:
        break;
      case -1:
        $_SelectedDatetime = new DateTimeIT();
      case 1:
        $_SearchU->setDatetime($_SelectedDatetime->databaseFormat());

        $this->_Bot->editMessageText([
          "message_id" => $message_id,
          "text" => SearchUTextMessages::selectDatetime() . "\n\n" . SearchUTextMessages::summarySelectedDatetime($_SelectedDatetime),
          "reply_markup" => InlineKeyboards::calendar($_SelectedDatetime)
        ]);
        break;
    }
    
    $this->keepThisState();
  }


  /**
   * Start the search of timetables, taking all data previously given
   */
  public function searchProcedure() {
    $_SearchU = new SearchU($this->_User->getUserId());
    $search_info = $_SearchU->getSearchInfo();
    
    $_Datetime = new DateTimeIT($search_info["sea_datetime"]);
    $message_id = $this->_Bot->getWebhookUpdate()->getMessage()->getMessageId();
    if ($_Datetime->isDatetimeInThePast()==-1) {
      $_Datetime = new DateTimeIT();
      
      $_SearchU->setDatetime($_Datetime->databaseFormat());
      $this->_Bot->editMessageText([
        "message_id" => $message_id,
        "text" => SearchUTextMessages::selectDatetime() . "\n\n" . SearchUTextMessages::summarySelectedDatetime($_Datetime),
        "reply_markup" => InlineKeyboards::calendar($_Datetime)
      ]);
    }

    $formatted_date = $_Datetime->getApiFormattedDate();
    $formatted_time = $_Datetime->getApiFormattedTime();

    $_ApiCotrap = new ApiCotrapRequestHandler();
    $request_result_data = $_ApiCotrap->get("search_u", [], [
      "idLocalita" => $search_info["sea_departure_id"],
      "idPoloPartenza" => $search_info["sea_departure_stop_id"],
      "idPoloArrivo" => $search_info["sea_arrival_stop_id"],
      "dataPartenza" => $formatted_date,
      "oraPartenza" => $formatted_time,
      "pagina" => 1,
    ]);

    $search_results = $request_result_data["result"]["itinerariTrovati"];
    $specific_search_info = $_SearchU->getSpecificSearchInfo();

    if (empty($search_results)) {
      $this->_Bot->sendMessage([
        'text' => SearchUTextMessages::noSearchResults($specific_search_info)
      ]);

      $this->keepThisState();
    }
    else {
      $this->_Bot->sendMessage([
        'text' => SearchUTextMessages::showSearchResults($specific_search_info, $search_results),
        'reply_markup' => InlineKeyboards::websiteResultsLink($request_result_data["url"]),
        'disable_web_page_preview' => true
      ]);
    }

    $this->keepThisState();
  }


}

?>