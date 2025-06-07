<?php

namespace CustomBotName\utilities;

use DateTime;
use DateTimeZone;
use Exception;

/**
 * Class to handle all datetime components
 */
class DateTimeIT extends DateTime {

  public const DATABASE_FORMAT = "Y-m-d H:00:00";
  public const API_DATE_FORMAT = "d/m/Y";
  public const API_TIME_FORMAT = "H:00";


  /** */
  public function __construct(string $datetime = 'now') {
    parent::__construct($datetime, new DateTimeZone('Europe/Rome'));

    if (!$this->isValidDateTime($datetime)) {
      new Exception("Datetime \"$datetime\" is not valid");
    }
  }

  /** */
  private function isValidDateTime(string $datetime, string $format = 'Y-m-d H:i:s'): bool {
    $formatted_datetime = DateTimeIT::createFromFormat($format, $datetime);
    $errors = DateTimeIT::getLastErrors();
    return $formatted_datetime !== false && $errors['warning_count'] === 0 && $errors['error_count'] === 0;
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
   * Get week's day italian name
   */
  public function getLiteralWeekDay() {
    $it_months = [
      0 => "Domenica", 1 => "Lunedì", 2 => "Martedì", 3 => "Mercoledì", 
      4 => "Giovedì", 5 => "Venerdì", 6 => "Sabato"
    ];
    $month = (int) $this->format("w");
    return $it_months[$month];
  }

  /** */
  public function nextDay() {
    $this->modify("+1 day");
    return $this->format(DateTimeIT::DATABASE_FORMAT);
  }

  /** */
  public function databaseFormat() {
    return $this->format(DateTimeIT::DATABASE_FORMAT);
  }

  public function getApiFormattedDate() {
    return $this->format(DateTimeIT::API_DATE_FORMAT);
  }

  public function getApiFormattedTime() {
    return $this->format(DateTimeIT::API_TIME_FORMAT);
  }

  /**
   * Check if $this datetime is in the past, is equale to the present or is in the future
   * @return int -1 datetime in the past | 0 datetime==present | 1 datetime in the future
   */
  public function isDatetimeInThePast() {
    $_TodayDatetime = new DateTimeIT(date(DateTimeIT::DATABASE_FORMAT));
    if ($this < $_TodayDatetime) {
      return -1;
    }
    else if ($this == $_TodayDatetime) {
      return 0;
    }
    else {
      return 1;
    }
  }
}