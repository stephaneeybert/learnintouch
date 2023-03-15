<?php

class ClockUtils {

  var $mlText;

  // Property names
  var $propertyTimeDifference;

  // The list of time difference values
  var $timeDifferences;

  var $preferences;

  var $languageUtils;
  var $preferenceUtils;
  var $propertyUtils;

  function __construct() {
    $this->init();
  }

  function init() {
    $this->propertyTimeDifference = "CLOCK_TIME_DIFFERENCE";

    $this->timeDifferences = array(
        'p12' => '+12',
        'p11' => '+11',
        'p10' => '+10',
        'p9' => '+9',
        'p8' => '+8',
        'p7' => '+7',
        'p6' => '+6',
        'p5' => '+5',
        'p4' => '+4',
        'p3' => '+3',
        'p2' => '+2',
        'p1' => '+1',
        'p0' => '0',
        'n1' => '-1',
        'n2' => '-2',
        'n3' => '-3',
        'n4' => '-4',
        'n5' => '-5',
        'n6' => '-6',
        'n7' => '-7',
        'n8' => '-8',
        'n9' => '-9',
        'n10' => '-10',
        'n11' => '-11',
        'n12' => '-12'
      );
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  function loadPreferences() {
    $this->loadLanguageTexts();

    $languageNames = $this->languageUtils->getLanguageNames();

    array_unshift($languageNames, " ");

    $this->preferences = array(
      // The numeric format of the date is used in form fields and to type in dates
      "CLOCK_DATE_NUMERIC_FORMAT" =>
      array(
        $this->mlText[27], $this->mlText[32], PREFERENCE_TYPE_SELECT,
        array(
          "" => "",
          "%d-%m-%Y" => strftime("%d-%m-%Y", time()),
          "%m/%d/%Y" => strftime("%m/%d/%Y", time())
        )
      ),
      // The display format of the date is used when displaying the date on the web site
      "CLOCK_DATE_FORMAT" =>
      array(
        $this->mlText[1], $this->mlText[33], PREFERENCE_TYPE_SELECT,
        array(
          "" => "",
          "%A %e %B %Y" => strftime("%A %e %B %Y", time()),
          "%e %B %Y" => strftime("%e %B %Y", time()),
          "%A %e %B" => strftime("%A %e %B", time()),
          "%A %e %B, %Y" => strftime("%A %e %B, %Y", time()),
          "%B %e, %Y" => strftime("%B %e, %Y", time()),
          "%a %b %e" => strftime("%a %b %e", time()),
          "%a %b %e, %Y" => strftime("%a %b %e, %Y", time()),
          "%c" => strftime("%c", time()),
          "%d/%m/%Y" => strftime("%d/%m/%Y", time()),
          "%m/%d/%Y" => strftime("%m/%d/%Y", time()),
          "%d/%m/%y" => strftime("%d/%m/%y", time()),
          "%m/%d/%y" => strftime("%m/%d/%y", time()),
          "%d/%m" => strftime("%d/%m", time()),
          "%m/%Y" => strftime("%m/%Y", time()),
          "%m/%y" => strftime("%m/%y", time()),
          "%d-%m-%Y" => strftime("%d-%m-%Y", time()),
          "%m-%d-%Y" => strftime("%m-%d-%Y", time()),
          "%d-%m-%y" => strftime("%d-%m-%y", time()),
          "%m-%d-%y" => strftime("%m-%d-%y", time()),
          "%d-%m" => strftime("%d-%m", time()),
          "%m-%Y" => strftime("%m-%Y", time()),
          "%m-%y" => strftime("%m-%y", time())
        )
      ),
      "CLOCK_TIME_FORMAT" =>
      array(
        $this->mlText[10], $this->mlText[35], PREFERENCE_TYPE_SELECT,
        array(
          "" => "",
          "%H:%M" => strftime("%H:%M", time()),
          "%H:%M:%S" => strftime("%H:%M:%S", time()),
          "%I:%M %p" => strftime("%I:%M %p", time())
        )
      ),
      "CLOCK_LANGUAGE" =>
      array(
        $this->mlText[2], $this->mlText[3], PREFERENCE_TYPE_SELECT, $languageNames
      )
    );

    $this->preferenceUtils->init($this->preferences);
  }

  // Get the system time of the server
  function getSystemTime() {
    $time = strftime("%H:%M:%S", time());

    return($time);
  }

  // Get the system timestamp of the server
  function getSystemTimestamp() {
    $time = time();

    return($time);
  }

  // Get the number of seconds from a given timestamp
  function getSecondsAgo($startTimestamp) {
    return(time() - $startTimestamp);
  }

  // Get the locale of the website language
  function getLanguageLocale() {
    $locale = '';

    $languageCode = $this->preferenceUtils->getValue("CLOCK_LANGUAGE");

    if ($languageCode) {
      if ($language = $this->languageUtils->selectByCode($languageCode)) {
        $locale = $language->getLocale();
      }
    } else {
      $locale = $this->languageUtils->getLanguageLocale();
    }

    return($locale);
  }

  // Get the locale of the admin language
  function getAdminLanguageLocale() {
    $locale = $this->languageUtils->getAdminLanguageLocale();

    return($locale);
  }

  // Set the locale of the website language
  function setWebsiteLocale() {
    $locale = $this->getLanguageLocale();

    $locale = setlocale(LC_TIME, $locale);

    // Check if the locale is implemented on the system
    if (!$locale) {
      reportError("The locale $locale for the website language is not implemented.");
    }
  }

  // Set the locale of the admin language
  function setLocale() {
    $locale = $this->getAdminLanguageLocale();

    $setLocale = setlocale(LC_TIME, $locale);

    // Check if the locale is implemented on the system
    if (!$setLocale) {
      reportError("The locale $locale for the admin language is not implemented.");
    }
  }

  // Render the date
  function renderDate() {
    $this->setLocale();

    $str = "\n<div class='clock_date'>";

    $this->setWebsiteLocale();

    $dateFormat = $this->getDateFormat();

    $timeStamp = $this->getLocalTimeStamp();

    $date = strftime($dateFormat, $timeStamp);

    if ($this->isUSDateFormat()) {
      $str .= ucwords($date);
    } else {
      $str .= ucfirst($date);
    }

    $str .= "</div>";

    return($str);
  }

  // Render the time
  function renderTime() {
    $str = "\n<div class='clock_time'>";

    $this->setWebsiteLocale();

    $timeFormat = $this->getTimeFormat();

    $timeStamp = $this->getLocalTimeStamp();

    $str .= strftime($timeFormat, $timeStamp);

    $str .= "</div>";

    return($str);
  }

  // Get the system date of the server
  function getSystemDate() {
    $date = strftime("%Y-%m-%d", time());

    return($date);
  }

  // Get the first day of the week
  function getFirstDayOfTheWeek($date) {
    $year = substr($date, 0, 4);
    $weekNumber = $this->getWeekNumber($date);
    $monday = date('Y-m-d', strtotime($year . 'W' . $weekNumber . '1'));

    return($monday);
  }

  // Get the last day of the week
  function getLastDayOfTheWeek($date) {
    $sunday = $this->incrementDays($this->getFirstDayOfTheWeek($this->incrementWeeks($date, 1)), -1);

    return($sunday);
  }

  // Check if the date is the first day of the week
  function isFirstDayOfWeek() {
    $monday = date('Y-m-d', strtotime(date('Y') . 'W' . date('W') . '1'));

    if ($monday == strftime("%Y-%m-%d", time())) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if the date is the first day of the month
  function isFirstDayOfMonth() {
    $day = strftime("%d", time());

    if ($day == 1) {
      return(true);
    } else {
      return(false);
    }
  }

  // Get the year of the system date of the server
  function getSystemYear() {
    $year = strftime("%Y", time());

    return($year);
  }

  // Get the month of the system date of the server
  function getSystemMonth() {
    $month = strftime("%m", time());

    return($month);
  }

  // Get the week number of the system date of the server
  function getWeekNumber($date) {
    $weekNumber = strftime("%W", $this->systemDateToTimeStamp($date));

    return($weekNumber);
  }

  // Get the system date and time of the server
  function getSystemDateTime() {
    $time = strftime("%Y-%m-%d %H:%M:%S", time());

    return($time);
  }

  // Get the local timestamp for the web site
  function getLocalTimeStamp() {
    $time = time() + $this->getTimeDifference() * 3600;

    return($time);
  }

  // Get the local time for the web site
  // The localized time takes care of the time difference
  function getLocalTime() {
    $time = strftime("%H:%M:%S", $this->getLocalTimeStamp());

    return($time);
  }

  // Get the local date for the web site
  // The localized date takes care of the time difference and of the local numeric format
  function getLocalNumericDate() {
    $this->setWebsiteLocale();

    $date = strftime($this->getDateNumericFormat(), $this->getLocalTimeStamp());

    return($date);
  }

  // Check if the numeric format is American (US)
  function isUSDateFormat() {
    $dateFormat = $this->getDateNumericFormat();

    if ($dateFormat == "%m/%d/%Y") {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if the numeric format is European (EU)
  function isEUDateFormat() {
    $dateFormat = $this->getDateNumericFormat();

    if ($dateFormat == "%d-%m-%Y") {
      return(true);
    } else {
      return(false);
    }
  }

  function getDateFormat() {
    $dateFormat = $this->preferenceUtils->getValue("CLOCK_DATE_FORMAT");

    if (!$dateFormat) {
      $dateFormat = "%d-%m-%Y";
    }

    return($dateFormat);
  }

  function getDateNumericFormat() {
    $dateFormat = $this->preferenceUtils->getValue("CLOCK_DATE_NUMERIC_FORMAT");

    if (!$dateFormat) {
      $dateFormat = "%d-%m-%Y";
    }

    return($dateFormat);
  }

  function getTimeFormat() {
    $timeFormat = $this->preferenceUtils->getValue("CLOCK_TIME_FORMAT");

    if (!$timeFormat) {
      $timeFormat = "%H:%M:%S";
    }

    return($timeFormat);
  }

  // Transform a timestamp into a local display format time
  // The date must be greater than or equal to 1970-01-01
  // That is because it makes use of timestamps
  function timeStampToLocalTime($timestamp) {
    $lTime = '';

    if ($timestamp) {
      $this->setWebsiteLocale();

      $lTime = strftime($this->getTimeFormat(), $timestamp);

      if ($this->isUSDateFormat()) {
        $lTime = ucwords($lTime);
      } else {
        $lTime = ucfirst($lTime);
      }
    }

    return($lTime);
  }

  // Transform a timestamp into a system date
  function dateTimeToSystemDate($dateTime) {
    $date = strftime("%Y-%m-%d", strtotime($dateTime));

    return($date);
  }

  // Transform a timestamp into a system time
  function dateTimeToSystemTime($dateTime) {
    $time = strftime("%H:%M:%S", strtotime($dateTime));

    return($time);
  }

  // Transform a system date into a local display format date
  // The date must be greater than or equal to 1970-01-01
  // That is because it makes use of timestamps
  function systemToLocalDate($date) {
    $lDate = '';

    if ($date && $date != '0000-00-00' && $date != '0000-00-00 00:00:00') {
      $this->setWebsiteLocale();

      $lDate = strftime($this->getDateFormat(), $this->systemDateToTimeStamp($date));

      if ($this->isUSDateFormat()) {
        $lDate = ucwords($lDate);
      } else {
        $lDate = ucfirst($lDate);
      }
    }

    return($lDate);
  }

  // Transform a date into a local numeric format date
  // This function is NOT using timestamps to allow dates before 01/01/1970
  function systemToLocalNumericDate($date) {
    $lnDate = '';

    if ($date && $date != '0000-00-00' && $date != '0000-00-00 00:00:00') {
      $this->setWebsiteLocale();

      $year = substr($date, 0, 4);
      $month = substr($date, 5, 2);
      $day = substr($date, 8, 2);
      if ($this->isUSDateFormat()) {
        $lnDate = "$month/$day/$year";;
      } else {
        $lnDate = "$day-$month-$year";;
      }
    }

    return($lnDate);
  }

  // Get a tip on the format of the numeric date
  function getDateNumericFormatTip() {
    $dateFormat = $this->getDateNumericFormat();

    if ($dateFormat == "%d-%m-%Y") {
      $format = "(JJ-MM-AAAA)";
    } else if ($dateFormat == "%m/%d/%Y") {
      $format = "(MM/DD/YYYY)";
    } else {
      $format = '';
    }

    return($format);
  }

  // Get a tip on the format of the time
  function getTimeFormatTip() {
    $format = "(HH-MM-SS)";

    return($format);
  }

  // Transform a system date into a time stamp
  // The date must be greater than or equal to 1970-01-01
  // That is because it makes use of timestamps
  function systemDateToTimeStamp($date) {
    $day = substr($date, 8, 2);
    $month = substr($date, 5, 2);
    $year = substr($date, 0, 4);

    $timestamp = mktime(0, 0, 0, $month, $day, $year);

    return($timestamp);
  }

  // Transform a system date time into a time stamp
  // The date must be greater than or equal to 1970-01-01
  // That is because it makes use of timestamps
  function systemDateTimeToTimeStamp($date) {
    $year = substr($date, 0, 4);
    $month = substr($date, 5, 2);
    $day = substr($date, 8, 2);
    $hour = substr($date, 11, 2);
    $minute = substr($date, 14, 2);
    $second = substr($date, 17, 2);

    $timestamp = mktime($hour, $minute, $second, $month, $day, $year);

    return($timestamp);
  }

  // Transform a local date into a system date
  // This function is NOT using timestamps to allow dates before 01/01/1970
  function localToSystemDate($date) {
    if ($this->isUSDateFormat()) {
      $year = substr($date, 6, 4);
      $month = substr($date, 0, 2);
      $day = substr($date, 3, 2);
    } else {
      $year = substr($date, 6, 4);
      $month = substr($date, 3, 2);
      $day = substr($date, 0, 2);
    }

    $date = "$year-$month-$day";

    return($date);
  }

  // Check if the first date is greater than the second one
  function systemDateIsGreater($date1, $date2) {
    $t1 = strtotime($date1);
    $t2 = strtotime($date2);
    if ($t1 > $t2) {
      return(true);
    } else {
      return(false);
    }
  }

  // Increment a system date by a number of days (positive or negative)
  function incrementDays($date, $days) {
    date_default_timezone_set('UTC');

    $timestamp = $this->systemDateToTimeStamp($date) + ($days * 24 * 60 * 60);

    $date = date("Y-m-d", $timestamp);

    return($date);
  }

  // Increment a system date by a number of weeks (positive or negative)
  function incrementWeeks($date, $weeks) {
    $timestamp = $this->systemDateToTimeStamp($date) + ($weeks * 7 * 24 * 60 * 60);

    $date = date("Y-m-d", $timestamp);

    return($date);
  }

  // Increment a system date by a number of months (positive or negative)
  function incrementMonths($date, $months) {
    $date = $this->incrementWeeks($date, $months * 4);

    return($date);
  }

  function getFirstDayOfTheMonth($date) {
    $day = substr($date, 8, 2);
    $month = substr($date, 5, 2);
    $year = substr($date, 0, 4);

    $date = "$year-$month-01";

    return($date);
  }

  function getLastDayOfTheMonth($date) {
    $day = substr($date, 8, 2);
    $month = substr($date, 5, 2);
    $year = substr($date, 0, 4);

    $month++;
    if ($month > 12) {
      $month = '01';
    } else if (strlen($month) == 1) {
      $month = '0' . $month;
    }

    $firstDayOfNextMonth = "$year-$month-01";

    $lastDay = $this->incrementDays($firstDayOfNextMonth, -1);

    return($lastDay);
  }

  // Check if the first date is greater than or equal to the second one
  function systemDateIsGreaterOrEqual($date1, $date2) {
    $t1 = strtotime($date1);
    $t2 = strtotime($date2);
    if ($t1 >= $t2) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if the first date is equal to the second one
  function systemDateIsEqual($date1, $date2) {
    $t1 = strtotime($date1);
    $t2 = strtotime($date2);
    if ($t1 == $t2) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if a system date is set
  function systemDateIsSet($date) {
    $year = substr($date, 0, 4);
    $month = substr($date, 5, 2);
    $day = substr($date, 8, 2);
    if ($month > 0 || $day > 0 || $year > 0) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if a system time is set
  function systemTimeIsSet($time) {
    $hour = substr($time, 0, 2);
    $minute = substr($time, 3, 2);
    $second = substr($time, 6, 2);
    if ($hour > 0 || $minute > 0 || $second > 0) {
      return(true);
    } else {
      return(false);
    }
  }

  // Get the difference between two dates as a timestamp
  function getSystemDatesDifference($date1, $date2) {
    $t1 = strtotime($date1);
    $t2 = strtotime($date2);
    if ($t2 > $t1) {
      $d = $t2 - $t1;
    } else {
      $d = $t1 - $t2;
    }

    return($d);
  }

  // Check the validity of a local date
  function isLocalNumericDateValid($date) {
    if (($timestamp = strtotime($date)) > 0) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check the validity of a time
  function isTimeValid($time) {
    if (($timestamp = strtotime($time)) > 0) {
      return(true);
    } else {
      return(false);
    }
  }

  // Get the time difference of the clock
  function getTimeDifference() {
    return($timeDifference = $this->timeDifferences[$this->getTimeDifferenceIndex()]);
  }

  // Get the time difference index of the array of differences
  function getTimeDifferenceIndex() {
    if (!$timeDifference = $this->propertyUtils->retrieve($this->propertyTimeDifference)) {
      $timeDifference = "p0";
    }

    return($timeDifference);
  }

  // Set the time difference of the clock
  function setTimeDifference($timeDifference) {
    $this->propertyUtils->store($this->propertyTimeDifference, $timeDifference);
  }

  function getMicroTime() {
    list($usec, $sec) = explode(' ', microtime());

    $mtime = (float)$usec + (float)$sec;

    return($mtime);
  }

}

?>
