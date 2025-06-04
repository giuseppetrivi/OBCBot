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

    /* select the next month */
    if ($input_text=="next_month") {
      $this->function_to_call = "selectNextMonthProcedure";
      return true; 
    }
    /* select the previous month */
    else if ($input_text=="previous_month") {
      $this->function_to_call = "selectPreviousMonthProcedure";
      return true; 
    }
    /* select the next hour */
    else if ($input_text=="next_hour") {
      $this->function_to_call = "selectNextHourProcedure";
      return true; 
    }
    /* select the previous hour */
    else if ($input_text=="previous_hour") {
      $this->function_to_call = "selectPreviousHourProcedure";
      return true; 
    }

    return false;
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
   * Procedure to change the date (pressing the day button)
   */
  protected function selectDateProcedure() {
    $date_selected = $this->_Bot->getInputFromChat()->getText();
    $message_id = $this->_Bot->getWebhookUpdate()->getMessage()->getMessageId();

    $_SearchEU = new SearchEU($this->_User->getUserId());
    $hour = explode(" ", $_SearchEU->getSearchInfo()["sea_datetime"])[1];

    $_SelectedDatetime = new DateTimeIT($date_selected . " " . $hour);
    $_SearchEU->setDatetime($_SelectedDatetime->databaseFormat());

    $this->_Bot->editMessageText([
      "message_id" => $message_id,
      "text" => TextMessages::selectDatetime() . "\n\n" . TextMessages::recapDatetime($_SelectedDatetime),
      "reply_markup" => InlineKeyboards::calendar($_SelectedDatetime)
    ]);
    
    $this->keepThisState();
  }


  /**
   * Procedure to advance the date by one month
   */
  protected function selectNextMonthProcedure() {
    $message_id = $this->_Bot->getWebhookUpdate()->getMessage()->getMessageId();

    $_SearchEU = new SearchEU($this->_User->getUserId());
    $datetime = $_SearchEU->getSearchInfo()["sea_datetime"];
    
    $_SelectedDatetime = new DateTimeIT($datetime);
    $_SelectedDatetime->modify("+1 month");

    $_SearchEU->setDatetime($_SelectedDatetime->databaseFormat());

    $this->_Bot->editMessageText([
      "message_id" => $message_id,
      "text" => TextMessages::selectDatetime() . "\n\n" . TextMessages::recapDatetime($_SelectedDatetime),
      "reply_markup" => InlineKeyboards::calendar($_SelectedDatetime)
    ]);
    
    $this->keepThisState();
  }

  /**
   * Procedure to go back the date by one month (checking to not go in the past)
   */
  protected function selectPreviousMonthProcedure() {
    $message_id = $this->_Bot->getWebhookUpdate()->getMessage()->getMessageId();

    $_SearchEU = new SearchEU($this->_User->getUserId());
    $datetime = $_SearchEU->getSearchInfo()["sea_datetime"];
    
    $_SelectedDatetime = new DateTimeIT($datetime);
    $_TodayDatetime = new DateTimeIT(date(DateTimeIT::DATABASE_FORMAT));
    if ($_SelectedDatetime == $_TodayDatetime) {
      $this->keepThisState();
    }
    else {
      $_SelectedDatetime->modify("-1 month");

      if ($_SelectedDatetime < $_TodayDatetime) {
        $_SelectedDatetime = $_TodayDatetime;
      }

      $_SearchEU->setDatetime($_SelectedDatetime->databaseFormat());

      $this->_Bot->editMessageText([
        "message_id" => $message_id,
        "text" => TextMessages::selectDatetime() . "\n\n" . TextMessages::recapDatetime($_SelectedDatetime),
        "reply_markup" => InlineKeyboards::calendar($_SelectedDatetime)
      ]);

      $this->keepThisState();
    }
  }


  /**
   * Procedure to advance the time by one hour
   */
  protected function selectNextHourProcedure() {
    $message_id = $this->_Bot->getWebhookUpdate()->getMessage()->getMessageId();

    $_SearchEU = new SearchEU($this->_User->getUserId());
    $datetime = $_SearchEU->getSearchInfo()["sea_datetime"];
    
    $_SelectedDatetime = new DateTimeIT($datetime);
    $_SelectedDatetime->modify("+1 hour");

    $_SearchEU->setDatetime($_SelectedDatetime->databaseFormat());

    $this->_Bot->editMessageText([
      "message_id" => $message_id,
      "text" => TextMessages::selectDatetime() . "\n\n" . TextMessages::recapDatetime($_SelectedDatetime),
      "reply_markup" => InlineKeyboards::calendar($_SelectedDatetime)
    ]);
    
    $this->keepThisState();
  }

  /**
   * Procedure to go back the time by one hour (checking to not go in the past)
   */
  protected function selectPreviousHourProcedure() {
    $message_id = $this->_Bot->getWebhookUpdate()->getMessage()->getMessageId();

    $_SearchEU = new SearchEU($this->_User->getUserId());
    $datetime = $_SearchEU->getSearchInfo()["sea_datetime"];
    
    $_SelectedDatetime = new DateTimeIT($datetime);
    $_TodayDatetime = new DateTimeIT(date(DateTimeIT::DATABASE_FORMAT));
    if ($_SelectedDatetime == $_TodayDatetime) {
      $this->keepThisState();
    }
    else {
      $_SelectedDatetime->modify("-1 hour");

      if ($_SelectedDatetime < $_TodayDatetime) {
        $_SelectedDatetime = $_TodayDatetime;
      }

      $_SearchEU->setDatetime($_SelectedDatetime->databaseFormat());

      $this->_Bot->editMessageText([
        "message_id" => $message_id,
        "text" => TextMessages::selectDatetime() . "\n\n" . TextMessages::recapDatetime($_SelectedDatetime),
        "reply_markup" => InlineKeyboards::calendar($_SelectedDatetime)
      ]);

      $this->keepThisState();
    }
  }


  private function checkDatetime() {

  }


}

?>