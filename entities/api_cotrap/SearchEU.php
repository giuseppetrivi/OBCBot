<?php

namespace CustomBotName\entities\api_cotrap;

use DB;
use MeekroDBException;

class SearchEU extends Search {


  public function getSearchInfo() {
    $result = 0;
    try {
      $result = DB::query("SELECT * FROM obc_searches WHERE user_idtelegram=%i_user_idtelegram", [
        "user_idtelegram" => $this->getUserIdtelegram()
      ]);
      return $result[0];
    } catch(MeekroDBException $e) {
      return $result;
    }
  }


  public function initializeSearch() {
    $result = 0;
    try {
      $result = DB::insert("obc_searches", [
        "user_idtelegram" => $this->getUserIdtelegram()
      ]);
      return $result;
    } catch(MeekroDBException $e) {
      return $result;
    }
  }

  public function destroySearch() {
    $result = 0;
    try {
      $result = DB::delete("obc_searches", [
        "user_idtelegram" => $this->getUserIdtelegram()
      ]);
      return $result;
    } catch(MeekroDBException $e) {
      return $result;
    }
  }

  
  public function setDepartureLocation($departure_location_id) {
    return $this->updateSingleField("sea_departure_id", $departure_location_id);
  }

  public function unsetDepartureLocation() {
    return $this->updateSingleField("sea_departure_id", NULL);
  }

  
  public function setArrivalLocation($arrival_location_id) {
    return $this->updateSingleField("sea_arrival_id", $arrival_location_id);
  }
  
  public function unsetArrivalLocation() {
    return $this->updateSingleField("sea_arrival_id", NULL);
  }


  public function setDepartureStop($departure_stop_id) {
    return $this->updateSingleField("sea_departure_stop_id", $departure_stop_id);
  }
  
  public function unsetDepartureStop() {
    return $this->updateSingleField("sea_departure_stop_id", NULL);
  }


  public function setArrivalStop($arrival_stop_id) {
    return $this->updateSingleField("sea_arrival_stop_id", $arrival_stop_id);
  }
  
  public function unsetArrivalStop() {
    return $this->updateSingleField("sea_arrival_stop_id", NULL);
  }


  public function setDatetime($datetime) {
    return $this->updateSingleField("sea_datetime", $datetime);
  }
  
  public function unsetDatetime() {
    return $this->updateSingleField("sea_datetime", NULL);
  }

}