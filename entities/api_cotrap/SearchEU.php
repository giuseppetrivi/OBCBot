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


  public function setDepartureStop($departure_stop_id) {
    $result = 0;
    try {
      $result = DB::update("obc_searches", 
        ["sea_departure_stop_id" => $departure_stop_id], 
        ["user_idtelegram" => $this->getUserIdtelegram()]
      );
      return $result;
    } catch(MeekroDBException $e) {
      return $result;
    }
  }
  
  public function unsetDepartureStop() {
    $result = 0;
    try {
      $result = DB::update("obc_searches", 
        ["sea_departure_stop_id" => NULL], 
        ["user_idtelegram" => $this->getUserIdtelegram()]
      );
      return $result;
    } catch(MeekroDBException $e) {
      return $result;
    }
  }


  public function setArrivalStop($arrival_location_id) {
    $result = 0;
    try {
      $result = DB::update("obc_searches", 
        ["sea_arrival_stop_id" => $arrival_location_id], 
        ["user_idtelegram" => $this->getUserIdtelegram()]
      );
      return $result;
    } catch(MeekroDBException $e) {
      return $result;
    }
  }
  
  public function unsetArrivalStop() {
    $result = 0;
    try {
      $result = DB::update("obc_searches", 
        ["sea_arrival_stop_id" => NULL], 
        ["user_idtelegram" => $this->getUserIdtelegram()]
      );
      return $result;
    } catch(MeekroDBException $e) {
      return $result;
    }
  }


  public function setDatetime($datetime) {
    $result = 0;
    try {
      $result = DB::update("obc_searches", 
        ["sea_datetime" => $datetime], 
        ["user_idtelegram" => $this->getUserIdtelegram()]
      );
      return $result;
    } catch(MeekroDBException $e) {
      return $result;
    }
  }
  
  public function unsetDatetime() {
    $result = 0;
    try {
      $result = DB::update("obc_searches", 
        ["sea_datetime" => NULL], 
        ["user_idtelegram" => $this->getUserIdtelegram()]
      );
      return $result;
    } catch(MeekroDBException $e) {
      return $result;
    }
  }

}