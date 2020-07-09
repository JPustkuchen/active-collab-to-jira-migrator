<?php

namespace ActiveCollabToJiraMigrator\Util;

/**
 * Utility function for Dates.
 */
class Date {

  /**
   * Converts UNIX timestamp to SimpleDateFormat.
   *
   * @param int $timestamp
   *
   * @return string
   */
  public static function convertTimestampToSimpleDateFormat(int $timestamp = NULL) {
    if (empty($timestamp)) {
      return NULL;
    }
    return date('c', $timestamp);
  }

  /**
   * Converts hours to Iso8601 duration format.
   *
   * @param decimal $hours
   *   number of hours as decimal, e.g. 2.5 for two and a half hour.
   *
   * @return string
   */
  public static function convertHoursToDuration($hours) {
    if ($hours === NULL) {
      return NULL;
    }
    if ($hours == 0) {
      // Jira expects null, not "0" here, otherwise an error occurs.
      return NULL;
    }
    $seconds = $hours * 60 * 60;
    return self::secondsToIso8601Duration($seconds);
  }

  /**
   * Seconds to ISO_8601 duration conversion.
   *
   * @param int $time strtotime.
   *
   * @return string
   *   The iso8610 string duration.
   */
  private static function secondsToIso8601Duration(int $seconds) {
    $units = [
      "Y" => 365 * 24 * 3600,
      "D" => 24 * 3600,
      "H" => 3600,
      "M" => 60,
      "S" => 1,
    ];

    $str = "P";
    $istime = FALSE;

    foreach ($units as $unitName => &$unit) {
      $quot = intval($seconds / $unit);
      $seconds -= $quot * $unit;
      $unit = $quot;
      if ($unit > 0) {
        // There may be a better way to do this.
        if (!$istime && in_array($unitName, ["H", "M", "S"])) {
          $str .= "T";
          $istime = TRUE;
        }
        $str .= strval($unit) . $unitName;
      }
    }

    return $str;
  }

}
