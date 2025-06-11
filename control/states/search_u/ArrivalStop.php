<?php

namespace SearchU\DepartureLocation\DepartureStop;

use OBCBot\control\AbstractState;
use OBCBot\entities\api_cotrap\LocationsU;
use OBCBot\entities\api_cotrap\LocationStopsU;
use OBCBot\entities\api_cotrap\SearchU;
use OBCBot\entities\telegrambot_sdk_interface\InputTypes;
use OBCBot\view\MenuOptions;
use OBCBot\view\InlineKeyboards;
use OBCBot\view\SearchUTextMessages;
use OBCBot\utilities\DateTimeIT;
use BackToMenuTrait;


class ArrivalStop extends AbstractState {

  protected array $valid_static_inputs = [
    MenuOptions::BACK => "backProcedure",
    MenuOptions::BACK_TO_MENU => "backToMenuProcedure"
  ];


  protected function validateDynamicInputs() {
    $input_text = $this->_Bot->getInputFromChat()->getText();
    $input_type = $this->_Bot->getInputFromChat()->getMessageType();

    /* regex to get words, eventually containing "-", as valid command. this word should be a location */
    $stops_regex = "/\b[a-zà-öù-ýA-ZÀ-ÖÙ-Ý]+(?:\s*-\s*[a-zà-öù-ýA-ZÀ-ÖÙ-Ý]+|\s+[a-zà-öù-ýA-ZÀ-ÖÙ-Ý]+)*\b/";
    if (preg_match($stops_regex, $input_text) && $input_type==InputTypes::MESSAGE) {
      $this->function_to_call = "selectDepartureStopProcedure";
      return true;
    }
  }


  /**
   * States:
   * SearchU\DepartureLocation\DepartureStop -> SearchU\DepartureLocation
   */
  protected function backProcedure() {
    $_SearchU = new SearchU($this->_User->getUserId());
    $_SearchU->unsetDepartureStop();

    $_LocationsU = new LocationsU();
    $all_urbal_locations = $_LocationsU->getAllUrbanLocations();

    $this->_Bot->sendMessage([
      'text' => SearchUTextMessages::chooseUrbanLocation(),
      'reply_markup' => InlineKeyboards::urbanLocationsList($all_urbal_locations)
    ]);

    $this->setNextState($this->getPreviousState());
  }

  use BackToMenuTrait;


  /**
   * States:
   * SearchU\DepartureLocation\DepartureStop\ArrivalStop
   *  -> SearchU\DepartureLocation\DepartureStop\ArrivalStop\PickDatetime
   *     SearchU\DepartureLocation\DepartureStop\ArrivalStop
   */
  protected function selectDepartureStopProcedure() {
    $stop_to_search = $this->_Bot->getInputFromChat()->getText();

    $_SearchU = new SearchU($this->_User->getUserId());
    $urban_location_id = $_SearchU->getSearchInfo()["sea_departure_id"];
    $departure_stop_id = $_SearchU->getSearchInfo()["sea_departure_stop_id"];

    $_LocationStopsU = new LocationStopsU();
    $all_departure_stops = $_LocationStopsU->getAllValidArrivalStop($urban_location_id, $departure_stop_id);
    $stops_info = $_LocationStopsU->findBestStopNameMatch($all_departure_stops, $stop_to_search);

    $first_stop_code = $stops_info[0]["stop_code"];
    $first_stop_name = $stops_info[0]["stop_name"];
    $first_stop_similarity_perc = $stops_info[0]["similarity_perc"];
    
    
    /* takes the location as valid even if there is not a perfect match */
    if ($first_stop_similarity_perc >= LocationsU::ALMOST_MATCHED) {

      if ($first_stop_similarity_perc >= LocationsU::MATCHED) {
        $this->_Bot->sendMessage([
          'text' => SearchUTextMessages::stopSelected($first_stop_name, false)
        ]);
      }
      else {
        $message_to_send = SearchUTextMessages::maybeYouMeant($first_stop_name) .
          "\n\n" . SearchUTextMessages::stopSelected($first_stop_name, false);
        $this->_Bot->sendMessage([
          'text' => $message_to_send
        ]);
      }

      $_SearchU->setArrivalStop($first_stop_code);

      /* datetime picker keyboard */
      $_SelectedDatetime = new DateTimeIT(date(DateTimeIT::DATABASE_FORMAT));
      $_SearchU->setDatetime($_SelectedDatetime->databaseFormat());

      $this->_Bot->sendMessage([
        "text" => SearchUTextMessages::selectDatetime() . "\n\n" . SearchUTextMessages::summarySelectedDatetime($_SelectedDatetime),
        "reply_markup" => InlineKeyboards::calendar($_SelectedDatetime)
      ]);

      $this->setNextState($this->appendNextState("PickDatetime"));
      
    }
    /* the match between the values ​​in the database and the value sent is not sufficient: the location must be resent */
    else {
      $message_to_send = SearchUTextMessages::alternativeOptions($stops_info) . 
        "\n\n" . SearchUTextMessages::chooseUrbanStop(false, true);
      $this->_Bot->sendMessage([
        'text' => $message_to_send
      ]);

      $this->keepThisState();
    }
  }

}

?>