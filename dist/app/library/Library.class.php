<?php
/**
 *  Re-usable methods.
 */

class Library
{

  /**
   *  Format money in a human-readable form.
   *
   *  @param float $float    The amount to format
   *  @return string      The human-readable format, eg:  $12,345.67
   */
  public static function formatMoney($float)
  {
    return '$' . number_format($float, 2, '.', ',');
  }

  /**
   *  Format postal code into uppercase with a space in between.
   *
   *  @param string $str  The postal code to format
   *  @return string      The correctly formatted postal code, eg:  A1B 2C3
   */
  public static function formatPostalCode($str)
  {
    $str = strtoupper($str);
    $str = str_replace(' ', '', $str);
    if (strlen($str) === 6) {
      $str = substr($str, 0, 3) . ' ' . substr($str, 3, 3);
    }
    return $str;
  }

  public static function dateIsInPast($testDate)
  {
    return $testDate < date('Y-m-d');
  }

  public static function dateIsInFuture($testDate)
  {
    return $testDate > date('Y-m-d');
  }

  public static function dateIsToday($testDate)
  {
    return $testDate === date('Y-m-d');
  }

  public static function addDaysToDate($date, $num)
  {
    return date('Y-m-d', strtotime($date . ' + ' . $num . ' days'));
  }

  public static function subtractDaysFromDate($date, $num)
  {
    return date('Y-m-d', strtotime($date . ' - ' . $num . ' days'));
  }

  /**
   *  Pad the number with zeroes until it is the defined length.
   *  Typically used for dates (turning '9' into '09').
   *
   *  @param  integer number    The original number, eg 1
   *  @param  length number     How long it should be when done, eg 3.
   *  @return string            The padded number, eg '001'
   */
  public static function pad($number, $length)
  {
    return str_pad($number, $length, '0', STR_PAD_LEFT);
  }

  public static function getPurifiedString($str)
  {
    return strip_tags($str, '<br><br /><p><i><em><strong><u><b><ul><ol><li>');
  }

  public static function isValidEmail($str)
  {
    if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9+-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $str)) {
      return true;
    } else {
      return false;
    }
  }

  public static function dateRangesOverlap($dateRange1Start, $dateRange1End, $dateRange2Start, $dateRange2End)
  {
    return !($dateRange1End <= $dateRange2Start || $dateRange1Start >= $dateRange2End);
  }

  /**
   *  @return string    Something like 8d610eb6-940c-11eb-a8b3-0242ac130003
   */
  public static function generateUuid()
  {
    $key = '-%lq~&glb4~r*vpa(r6h+r^=t`t7@^8:m_?}x>w7-tb.qd<6btlc?~c(,cf=?@x`';
    return sha1($key . microtime(true) . rand(1, 9999999));
  }
}
