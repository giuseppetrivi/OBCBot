<?php

namespace CustomBotName\entities\api_cotrap;

use DB;
use MeekroDBException;

class SearchEU extends Search {


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
    $result = 0;
    try {
      $result = DB::update("obc_searches", 
        ["sea_departure_id" => $departure_location_id], 
        ["user_idtelegram" => $this->getUserIdtelegram()]
      );
      return $result;
    } catch(MeekroDBException $e) {
      return $result;
    }
  }

  public function unsetDepartureLocation() {
    $result = 0;
    try {
      $result = DB::update("obc_searches", 
        ["sea_departure_id" => NULL], 
        ["user_idtelegram" => $this->getUserIdtelegram()]
      );
      return $result;
    } catch(MeekroDBException $e) {
      return $result;
    }
  }

  
  public function setArrivalLocation($arrival_location_id) {
    $result = 0;
    try {
      $result = DB::update("obc_searches", 
        ["sea_arrival_id" => $arrival_location_id], 
        ["user_idtelegram" => $this->getUserIdtelegram()]
      );
      return $result;
    } catch(MeekroDBException $e) {
      return $result;
    }
  }
  
  public function unsetArrivalLocation() {
    $result = 0;
    try {
      $result = DB::update("obc_searches", 
        ["sea_arrival_id" => NULL], 
        ["user_idtelegram" => $this->getUserIdtelegram()]
      );
      return $result;
    } catch(MeekroDBException $e) {
      return $result;
    }
  }

}