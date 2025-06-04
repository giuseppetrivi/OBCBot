<?php

namespace CustomBotName\entities;

use DateTime;

/**
 * Class to handle all datetime components
 */
class DateTimeIT extends DateTime {
  const DATABASE_FORMAT = "Y-m-d H:i:s";

  /**
   * 
   */
  public function __construct(string $datetime = 'now', \DateTimeZone|null $timezone = null) {
    parent::__construct();

    
  }

  /**
   * Get month's italian name
   */
  public function getLiteralMonth() {
    $it_months = [
      1 => "Gennaio", 2 => "Febbraio", 3 => "Marzo",
      4 => "Aprile", 5 => "Maggio", 6 => "Giugno",
      7 => "Luglio", 8 => "Agosto", 9 => "Settembre",
      10 => "Ottobre", 11 => "Novembre", 12 => "Dicembre"
    ];
    $month = (int) $this->format("n");
    return $it_months[$month];
  }

  /**
   * 
   */
  public function nextDay() {
    $this->modify("+1 day");
    return $this->format(DateTimeIT::DATABASE_FORMAT);
  }
}