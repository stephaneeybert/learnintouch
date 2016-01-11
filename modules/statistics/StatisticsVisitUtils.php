<?php

class StatisticsVisitUtils extends StatisticsVisitDB {

  var $mlText;

  // Minimum time for two hits to count as two visits
  // This is to avoid adding a visit on every hit
  var $visitDuration;

  // The bar colors
  var $visitorsColor;
  var $visitsColor;

  // The names of the robots
  var $robotNames;

  // The names of the browsers
  var $browserNames;

  // The names of the operating systems
  var $osNames;

  // The unknow referer
  var $unknownReferer;

  // Property names
  var $propertyCounterTime;
  var $propertyVisitDuration;

  var $preferences;

  var $languageUtils;
  var $preferenceUtils;
  var $clockUtils;
  var $statisticsRefererUtils;
  var $templateUtils;
  var $statisticsPageUtils;
  var $adminModuleUtils;
  var $propertyUtils;

  function StatisticsVisitUtils() {
    $this->StatisticsVisitDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    // It is expressed in minutes
    $this->visitDuration = 15;

    $this->visitorsColor = "#9933CC";
    $this->visitsColor = "#9999FF";

    $this->robotNames = array('Google', 'LinkWalker', 'MSN', 'Grub', 'WiseNut', 'YahooSlurp', 'Alexa', 'OpenFind');

    $this->browserNames = array('WebTV', 'Lynx', 'Opera', 'Safari', 'Firefox', 'Netscape', 'Konqueror', 'Mozilla', 'MSIE');

    $this->phoneNames = array('SonyEricsson', 'NEC');

    $this->osNames = array('WebTV', 'SunOS', 'Unix', 'OS2', 'MacOS', 'FreeBSD', 'Linux', 'Windows95', 'Windows98', 'WindowsNT', 'WindowsMe', 'Windows2000', 'WindowsXP');

    $this->unknownReferer = 0;

    $this->propertyCounterTime = "STATISTICS_COUNTER_TIME";
    $this->propertyVisitDuration = "STATISTICS_VISIT_DURATION";
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  function loadPreferences() {
    $this->loadLanguageTexts();

    $this->preferences = array(
      "STATISTICS_DISPLAY_ROBOT" =>
      array($this->mlText[29], '', PREFERENCE_TYPE_BOOLEAN, ''),
        "STATISTICS_DISPLAY_BROWSER" =>
        array($this->mlText[30], '', PREFERENCE_TYPE_BOOLEAN, ''),
          "STATISTICS_DISPLAY_OS" =>
          array($this->mlText[31], '', PREFERENCE_TYPE_BOOLEAN, ''),
            "STATISTICS_DISPLAY_WEEKDAY" =>
            array($this->mlText[32], '', PREFERENCE_TYPE_BOOLEAN, ''),
              "STATISTICS_DISPLAY_HOUR" =>
              array($this->mlText[33], '', PREFERENCE_TYPE_BOOLEAN, ''),
                "STATISTICS_NB_PAGES" =>
                array($this->mlText[36], $this->mlText[37], PREFERENCE_TYPE_SELECT, array(10 => "10", 20 => "20", 50 => "50", 100 => "100"))
              );

    $this->preferenceUtils->init($this->preferences);
  }

  // Render the referers
  function renderReferer($currentYearMonth = '') {
    global $gUtilsUrl;
    global $gStatisticsImagePath;
    global $gStatisticsImageUrl;
    global $gJSNoStatus;
    global $PHP_SELF;

    $this->loadLanguageTexts();

    // The height of a bar
    $barHeight = 10;

    // Maximum height of a bar
    $maxBarWidth = 120;

    $currentYear = substr($currentYearMonth, 0, 4);
    $currentMonth = substr($currentYearMonth, 4, 2);

    $thisMonth = date("m", $this->clockUtils->getLocalTimeStamp());
    $thisYear = date("Y", $this->clockUtils->getLocalTimeStamp());
    $lastYear = $thisYear - 1;
    if (!($currentMonth > 0 && $currentMonth <= 12)) {
      $currentMonth = $thisMonth;
    }
    if (!($currentYear > 0)) {
      $currentYear = $thisYear;
    }

    $str = "<table border='0' cellpadding='2' cellspacing='2'><tr>";

    $str .= "<td nowrap align='center'><b>" . $this->mlText[26]
      . " " . ucfirst(strftime("%B", strtotime("$currentYear-$currentMonth-01")))
      . " " . $currentYear
      . "</b></td>";

    $str .= "</tr><tr>";

    $list = array();
    for ($month = $thisMonth; $month >= 1; $month--) {
      $monthName = ucfirst(strftime("%B", strtotime("$thisYear-$month-01")));
      $list["$thisYear$month"] = $thisYear . ' ' . $monthName;
    }
    for ($month = 12; $month >= 1; $month--) {
      $monthName = ucfirst(strftime("%B", strtotime("$lastYear-$month-01")));
      $list["$lastYear$month"] = $lastYear . ' ' . $monthName;
    }
    $strSelectMonth = LibHtml::getSelectList("currentYearMonth", $list, $currentYearMonth, true);
    $strSelect = "<form action='$PHP_SELF' method='post'>"
      . "<b>" . $this->mlText[25] . "</b> $strSelectMonth "
      . "</form>";

    $str .= "<td nowrap align='right'>" . $strSelect . "</td>";

    $str .= "</tr></table>";

    $str .= "<br>";

    // Get the colors
    $visitsColor = urlencode($this->visitsColor);

    $referers = array();

    // Get the list of referers
    $listReferers = array();
    $statisticsReferers = $this->statisticsRefererUtils->selectAll();
    foreach ($statisticsReferers as $statisticsReferer) {
      array_push($listReferers, $statisticsReferer->getUrl());
    }

    // Init the values
    foreach ($listReferers as $refererName) {
      $referers[$refererName] = 0;
    }

    // Get the number of visits per referer
    $referersVisits = $this->countByReferer($currentYear, $currentMonth);
    foreach ($referersVisits as $refererVisits) {
      list($visitCount, $visitorReferer) = $refererVisits;

      if ($visitorReferer && !LibUtils::isRelativeUrl($visitorReferer)) {
        foreach ($listReferers as $refererName) {
          if (stristr($visitorReferer, $refererName)) {
            if (!isset($referers[$refererName])) {
              $referers[$refererName] = 0;
            }
            $referers[$refererName] += $visitCount;
            break;
          }
        }

        if (!$this->isRobot($visitorReferer)) {
          $this->unknownReferer += $visitCount;
        }
      }
    }

    // Sort the values by decreasing order
    arsort($referers);

    // Get the total number of visits
    $totalVisits = 0;
    foreach ($referers as $refererName => $visits) {
      $totalVisits += $visits;
    }
    $totalVisits += $this->unknownReferer;

    $str .= "<table border='0' cellpadding='2' cellspacing='2'>";

    $str .= "<tr><td nowrap><b>" . $this->mlText[27] . "</b></td>"
      . "<td nowrap><b>" . $this->mlText[4] . "</b></td>"
      . "<td nowrap><b>" . $this->mlText[20] . "</b></td>"
      . "<td nowrap></td>"
      . "</tr>";

    $str .= "<tr><td><br></td><td></td><td></td><td></td></tr>";

    // Add the visits from the unknown referer
    $unknown = array($this->mlText[28] => $this->unknownReferer);
    $referers = array_merge($referers, $unknown);

    foreach ($referers as $refererName => $visits) {
      // Calculate the bar width based on the percentage values
      if ($totalVisits > 0) {
        $visitsWidth = round(($visits * $maxBarWidth) / $totalVisits);
      } else {
        $visitsWidth = 0;
      }

      // Have non null height to display an image
      if ($visitsWidth == 0) {
        $visitsWidth = 1;
      }

      if ($totalVisits > 0) {
        $percentage = round(($visits * 100) / $totalVisits);
      } else {
        $percentage = 0;
      }

      // Create the image
      $url = $gUtilsUrl . "/printBarImage.php?color=$visitsColor&width=$visitsWidth&height=$barHeight";
      $imageVisits = "<img src='$url' title='' alt='' />";

      $iconFilename = "referer" . $refererName . ".png";
      $iconFile = $gStatisticsImagePath . $iconFilename;
      $iconUrl = $gStatisticsImageUrl . "/" . $iconFilename;
      if (is_file($iconFile)) {
        $srcIcon = "<img src='$iconUrl"
          . "' title='' alt='' />";
      } else {
        $srcIcon = '';
      }

      if ($statisticsReferer = $this->statisticsRefererUtils->selectByUrl($refererName)) {
        $name = $statisticsReferer->getName();
        $description = $statisticsReferer->getDescription();
        $url = $statisticsReferer->getUrl();
        if ($name) {
          $strName = $name;
        } else {
          $strName = $refererName;
        }
        $strReferer = "<a href='$url' onclick=\"window.open(this.href, '_blank'); return(false);\" $gJSNoStatus title='$description'>"
          . "$strName</a>";
      } else {
        $strReferer = $refererName;
      }

      $str .= "<tr><td nowrap>$srcIcon $strReferer</td>"
        . "<td nowrap align='center'>$visits</td>"
        . "<td nowrap align='center'>$percentage</td>"
        . "<td nowrap>$imageVisits</td>"
        . "</tr>";
    }

    $str .= "</table>";

    return($str);
  }

  // Render the statistics of the page hits for the month
  // If no month is given then use the last month
  function renderPageHits($currentYearMonth = '') {
    global $gUtilsUrl;
    global $gJSNoStatus;
    global $PHP_SELF;

    $this->loadLanguageTexts();

    // The height of a bar
    $barHeight = 10;

    // Maximum width of a bar
    $maxBarWidth = 120;

    $currentYear = substr($currentYearMonth, 0, 4);
    $currentMonth = substr($currentYearMonth, 4, 2);

    $thisMonth = date("m", $this->clockUtils->getLocalTimeStamp());
    $thisYear = date("Y", $this->clockUtils->getLocalTimeStamp());
    $lastYear = $thisYear - 1;
    if (!($currentMonth > 0 && $currentMonth <= 12)) {
      $currentMonth = $thisMonth;
    }
    if (!($currentYear > 0)) {
      $currentYear = $thisYear;
    }

    // Get the colors
    $hitsColor = urlencode($this->visitsColor);

    $str = "<table border='0' cellpadding='2' cellspacing='2'><tr>";

    $str .= "<td nowrap align='center'><b>" . $this->mlText[7]
      . " " . ucfirst(strftime("%B", strtotime("$currentYear-$currentMonth-01")))
      . " " . $currentYear
      . "</b></td>";

    $str .= "</tr><tr>";

    $list = array();
    for ($month = $thisMonth; $month >= 1; $month--) {
      $monthName = ucfirst(strftime("%B", strtotime("$thisYear-$month-01")));
      $list["$thisYear$month"] = $thisYear . ' ' . $monthName;
    }
    for ($month = 12; $month >= 1; $month--) {
      $monthName = ucfirst(strftime("%B", strtotime("$lastYear-$month-01")));
      $list["$lastYear$month"] = $lastYear . ' ' . $monthName;
    }
    $strSelectMonth = LibHtml::getSelectList("currentYearMonth", $list, $currentYearMonth, true);
    $strSelect = "<form action='$PHP_SELF' method='post'>"
      . "<b>" . $this->mlText[25] . "</b> $strSelectMonth "
      . "</form>";

    $str .= "<td nowrap align='right'>" . $strSelect . "</td>";

    $str .= "</tr></table>";

    $str .= "<br>";

    $str .= "<table border='0' cellpadding='2' cellspacing='2'>";

    $str .= "<tr><td nowrap><b>" . $this->mlText[8] . "</b></td>"
      . "<td nowrap><b>" . $this->mlText[9] . "</b></td>"
      . "<td nowrap><b>" . $this->mlText[20] . "</b></td>"
      . "<td nowrap></td>"
      . "</tr>";

    $str .= "<tr><td><br></td><td></td><td></td><td></td></tr>";

    // Get the page names
    $nbPages = $this->preferenceUtils->getValue("STATISTICS_NB_PAGES");

    // Calculate the total number of hits per month
    $listHits = array();
    $totalHits = 0;
    $statisticsPages = $this->statisticsPageUtils->selectByYearAndMonth($currentYear, $currentMonth, $nbPages);
    foreach ($statisticsPages as $statisticsPage) {
      $hits = $statisticsPage->getHits();
      $totalHits += $hits;
    }

    foreach ($statisticsPages as $statisticsPage) {
      $page = $statisticsPage->getPage();
      $hits = $statisticsPage->getHits();

      // Calculate the bar width based on the percentage values
      if ($totalHits > 0) {
        $hitWidth = round(($hits * $maxBarWidth) / $totalHits);
        $percentage = round(($hits * 100) / $totalHits);
      } else {
        $hitWidth = 0;
        $percentage = 0;
      }

      // Have non null width to display an image
      if ($hitWidth == 0) {
        $hitWidth = 1;
      }

      // Create the image
      $url = $gUtilsUrl . "/printBarImage.php?color=$hitsColor&width=$hitWidth&height=$barHeight";
      $imageHits = "<img src='$url' title='' alt='' />";

      $pageName = $this->templateUtils->getPageName($page);
      if (isset($pageName)) {
        $strUrl = $this->templateUtils->renderPageUrl($page);

        $str .= "<tr><td nowrap>"
          . "<a href='$strUrl' onclick=\"window.open(this.href, '_blank'); return(false);\" $gJSNoStatus
          title='mlText'>"
          . $pageName
          . "</a>"
          . "</td>"
          . "<td nowrap align='center'>$hits</td>"
          . "<td nowrap align='center'>$percentage</td>"
          . "<td nowrap>$imageHits</td>"
          . "</tr>";
      }
    }

    $str .= "</table>";

    return($str);
  }

  // Render the statistics for the days of a month
  // If no month is given then use the last month
  function renderDays($currentYearMonth = '') {
    global $gUtilsUrl;
    global $gCommonImagesUrl;
    global $PHP_SELF;

    $this->loadLanguageTexts();

    // The width of a bar
    $barWidth = 10;

    // Maximum height of a bar
    $maxBarHeight = 120;

    $currentYear = substr($currentYearMonth, 0, 4);
    $currentMonth = substr($currentYearMonth, 4, 2);

    $thisMonth = date("m", $this->clockUtils->getLocalTimeStamp());
    $thisYear = date("Y", $this->clockUtils->getLocalTimeStamp());
    $lastYear = $thisYear - 1;
    if (!($currentMonth > 0 && $currentMonth <= 12)) {
      $currentMonth = $thisMonth;
    }
    if (!($currentYear > 0)) {
      $currentYear = $thisYear;
    }

    // Get the number of days for the month
    $nbDays = date("t", $this->clockUtils->getLocalTimeStamp());

    // Get the colors
    $visitorsColor = urlencode($this->visitorsColor);
    $visitsColor = urlencode($this->visitsColor);

    $maxVisitors = 0;
    $maxVisits = 0;
    $listVisitors = array();
    $listVisits = array();
    for ($day = 1; $day <= $nbDays; $day++) {
      $visitors = $this->countDayVisitors($currentYear, $currentMonth, $day);
      $visits = $this->countDayVisits($currentYear, $currentMonth, $day);
      $maxVisitors = max($maxVisitors, $visitors);
      $maxVisits = max($maxVisits, $visits);
      $listVisitors[$day] = $visitors;
      $listVisits[$day] = $visits;
    }

    $str = "<table border='0' cellpadding='2' cellspacing='2'><tr>";

    $str .= "<td nowrap align='center'><b>" . $this->mlText[14]
      . " " . ucfirst(strftime("%B", strtotime("$currentYear-$currentMonth-01")))
      . " " . $currentYear
      . "</b></td>";

    $str .= "</tr><tr>";

    $str .= "<td><table border='0' width='100%' cellpadding='2' cellspacing='2'><tr>";
    $str .= "<td nowrap>";

    $url = $gUtilsUrl . "/printBarImage.php?color=$visitorsColor&width=$barWidth&height=$barWidth";
    $image = "<img src='$url' title='' alt='' />";
    $str .= "$image " . $this->mlText[3];

    $str .= "<br>";

    $url = $gUtilsUrl . "/printBarImage.php?color=$visitsColor&width=$barWidth&height=$barWidth";
    $image = "<img src='$url' title='' alt='' />";
    $str .= "$image " . $this->mlText[4];

    $str .= "</td>";
    $str .= "<td align='right'>";
    $list = array();
    for ($month = $thisMonth; $month >= 1; $month--) {
      $monthName = ucfirst(strftime("%B", strtotime("$thisYear-$month-01")));
      $list["$thisYear$month"] = $thisYear . ' ' . $monthName;
    }
    for ($month = 12; $month >= 1; $month--) {
      $monthName = ucfirst(strftime("%B", strtotime("$lastYear-$month-01")));
      $list["$lastYear$month"] = $lastYear . ' ' . $monthName;
    }
    $strSelectMonth = LibHtml::getSelectList("currentYearMonth", $list, $currentYearMonth, true);
    $strSelect = "<form action='$PHP_SELF' method='post'>"
      . "<b>" . $this->mlText[25] . "</b> $strSelectMonth "
      . "</form>";
    $str .= $strSelect;
    $str .= "</td>";
    $str .= "</tr></table></td>";

    $str .= "</tr><tr>";

    $str .= "<td><table border='0' width='100%' cellspacing='2' cellpadding='2'><tr>";

    for ($day = 1; $day <= $nbDays; $day++) {
      // Calculate the bar widths based on the percentage values
      if ($maxVisits > 0) {
        $visitorsHeight = round(($listVisitors[$day] * $maxBarHeight) / $maxVisits);
      } else {
        $visitorsHeight = 0;
      }
      if ($maxVisits > 0) {
        $visitsHeight = round(($listVisits[$day] * $maxBarHeight) / $maxVisits);
      } else {
        $visitsHeight = 0;
      }

      // Have non null heights to display an image
      if ($visitorsHeight == 0) {
        $visitorsHeight = 1;
      }

      if ($visitsHeight == 0) {
        $visitsHeight = 1;
      }

      // Create the images
      $url = $gUtilsUrl . "/printBarImage.php?color=$visitorsColor&width=$barWidth&height=$visitorsHeight";
      $imageVisitors = "<img src='$url' title='' alt='' />";
      $url = $gUtilsUrl . "/printBarImage.php?color=$visitsColor&width=$barWidth&height=$visitsHeight";
      $imageVisits = "<img src='$url' title='' alt='' />";

      $str .= "<td valign='bottom' nowrap>$imageVisitors$imageVisits</td>";
    }

    $str .= "</tr><tr>";

    for ($day = 1; $day <= $nbDays; $day++) {
      $str .= "<td align='center' valign='bottom' nowrap>$day</td>";
    }

    $str .= "</tr></table></td>";

    $str .= "</tr></table>";

    $str .= "<br><br><table border='0' cellpadding='2' cellspacing='2'>";
    $str .= "<tr><td nowrap><b>"
      . $this->mlText[13]
      . "</b></td><td><b>"
      . $this->mlText[3]
      . "</b></td><td><b>"
      . $this->mlText[4]
      . "</b></td></tr>";

    $str .= "<tr><td><br></td><td></td><td></td></tr>";

    for ($day = 1; $day <= $nbDays; $day++) {
      $dayName = ucwords(strftime("%e %B %Y", strtotime("$currentYear-$currentMonth-$day")));
      $str .= "<tr><td nowrap>$dayName</td>"
        . "<td align='center'>$listVisitors[$day]</td>"
        . "<td align='center'>$listVisits[$day]</td>"
        . "</tr>";
    }

    $str .= "</table>";

    return($str);
  }

  // Render the statistics for all the months of the year
  function renderMonths() {
    global $gUtilsUrl;

    $this->loadLanguageTexts();

    // The width of a bar
    $barWidth = 10;

    // Maximum height of a bar
    $maxBarHeight = 120;

    $currentYear = date("Y", $this->clockUtils->getLocalTimeStamp());
    $currentMonth = date("m", $this->clockUtils->getLocalTimeStamp());

    // Get the colors
    $visitorsColor = urlencode($this->visitorsColor);
    $visitsColor = urlencode($this->visitsColor);

    $maxVisitors = 0;
    $maxVisits = 0;
    if ($currentMonth == 12) {
      $startMonth = 1;
      $startYear = $currentYear;
    } else {
      $startMonth = $currentMonth + 1;
      $startYear = $currentYear - 1;
    }
    $month = $startMonth;
    $year = $startYear;
    $listVisitors = array();
    $listVisits = array();
    for ($i = 0; $i < 12; $i++) {
      $visitors = $this->countMonthVisitors($year, $month);
      $visits = $this->countMonthVisits($year, $month);
      $maxVisitors = max($maxVisitors, $visitors);
      $maxVisits = max($maxVisits, $visits);
      $listVisitors[$i] = $visitors;
      $listVisits[$i] = $visits;
      $listYearMonth[$i] = array($year, $month);
      $month++;
      if ($month > 12) {
        $month = 1;
        $year++;
      }
    }

    $str = "<table border='0' cellpadding='2' cellspacing='2'><tr>";

    $str .= "<td nowrap align='center'><b>" . $this->mlText[5] . " $currentYear</b></td>";

    $str .= "</tr><tr>";

    $url = $gUtilsUrl . "/printBarImage.php?color=$visitorsColor&width=$barWidth&height=$barWidth";
    $image = "<img src='$url' title='' alt='' />";
    $str .= "<td nowrap>$image " . $this->mlText[3] . "</td>";

    $str .= "</tr><tr>";

    $url = $gUtilsUrl . "/printBarImage.php?color=$visitsColor&width=$barWidth&height=$barWidth";
    $image = "<img src='$url' title='' alt='' />";
    $str .= "<td nowrap>$image " . $this->mlText[4] . "</td>";

    $str .= "</tr><tr>";

    $str .= "<td align='center'><table border='0' width='100%' cellpadding='2' cellspacing='2'><tr>";

    for ($i = 0; $i < 12; $i++) {
      // Calculate the bar heights based on the percentage values
      if ($maxVisits > 0) {
        $visitorsHeight = round(($listVisitors[$i] * $maxBarHeight) / $maxVisits);
      } else {
        $visitorsHeight = 0;
      }
      if ($maxVisits > 0) {
        $visitsHeight = round(($listVisits[$i] * $maxBarHeight) / $maxVisits);
      } else {
        $visitsHeight = 0;
      }

      // Have non null heights to display an image
      if ($visitorsHeight == 0) {
        $visitorsHeight = 1;
      }

      if ($visitsHeight == 0) {
        $visitsHeight = 1;
      }

      $url = $gUtilsUrl . "/printBarImage.php?color=$visitorsColor&width=$barWidth&height=$visitorsHeight";
      $imageVisitors = "<img src='$url' title='' alt='' />";
      $url = $gUtilsUrl . "/printBarImage.php?color=$visitsColor&width=$barWidth&height=$visitsHeight";
      $imageVisits = "<img src='$url' title='' alt='' />";

      $str .= "<td valign='bottom' nowrap>$imageVisitors$imageVisits</td>";
    }

    $str .= "</tr><tr>";

    for ($i = 0; $i < 12; $i++) {
      list($year, $month) = $listYearMonth[$i];
      $monthName = ucfirst(strftime("%B", strtotime("$year-$month-01")));
      $str .= "<td valign='bottom' nowrap>$monthName</td>";
    }

    $str .= "</tr></table></td>";

    $str .= "</tr></table>";

    $str .= "<br><br><table border='0' cellpadding='2' cellspacing='2'>";
    $str .= "<tr><td width='30%'><b>"
      . $this->mlText[6]
      . "</b></td><td width='30%'><b>"
      . $this->mlText[3]
      . "</b></td><td width='30%'><b>"
      . $this->mlText[4]
      . "</b></td></tr>";

    $str .= "<tr><td><br></td><td></td><td></td></tr>";

    for ($i = 0; $i < 12; $i++) {
      list($year, $month) = $listYearMonth[$i];
      $monthName = ucfirst(strftime("%B", strtotime("$year-$month-01")));
      $str .= "<tr><td>$monthName</td>"
        . "<td align='center'>$listVisitors[$i]</td>"
        . "<td align='center'>$listVisits[$i]</td>"
        . "</tr>";
    }

    $str .= "</table>";

    return($str);
  }

  // Render the statistics for the week days of the all the weeks of the year
  function renderWeekDays() {
    global $gUtilsUrl;

    $this->loadLanguageTexts();

    // The width of a bar
    $barWidth = 10;

    // Maximum height of a bar
    $maxBarHeight = 120;

    $currentMonth = date("m", $this->clockUtils->getLocalTimeStamp());
    $currentYear = date("Y", $this->clockUtils->getLocalTimeStamp());

    // Get the colors
    $visitsColor = urlencode($this->visitsColor);

    $maxVisitors = 0;
    $maxVisits = 0;
    $listVisitors = array();
    $listVisits = array();
    for ($day = 1; $day <= 7; $day++) {
      $visits = $this->countWeekDayVisits($currentYear, $day);
      $maxVisits = max($maxVisits, $visits);
      $listVisits[$day] = $visits;
    }

    $str = "<table border='0' cellpadding='2' cellspacing='2'><tr>";

    $str .= "<td nowrap align='center'><b>" . $this->mlText[15]
      . " " . $currentYear
      . "</b></td>";

    $str .= "</tr><tr>";

    $url = $gUtilsUrl . "/printBarImage.php?color=$visitsColor&width=$barWidth&height=$barWidth";
    $image = "<img src='$url' title='' alt='' />";
    $str .= "<td nowrap>$image " . $this->mlText[4] . "</td>";

    $str .= "</tr><tr>";

    $str .= "<td align='center'><table border='0' width='100%' cellpadding='2' cellspacing='2'><tr>";

    for ($day = 1; $day <= 7; $day++) {
      // Calculate the bar widths based on the percentage values
      if ($maxVisits > 0) {
        $visitsHeight = round(($listVisits[$day] * $maxBarHeight) / $maxVisits);
      } else {
        $visitsHeight = 0;
      }

      // Have non null heights to display an image
      if ($visitsHeight == 0) {
        $visitsHeight = 1;
      }

      // Create the images
      $url = $gUtilsUrl . "/printBarImage.php?color=$visitsColor&width=$barWidth&height=$visitsHeight";
      $imageVisits = "<img src='$url' title='' alt='' />";

      $str .= "<td align='center' valign='bottom' nowrap>$imageVisits</td>";
    }

    $str .= "</tr><tr>";

    // Get the full names of the week days
    $dayName = array();
    for ($day = 1; $day <= 7; $day++) {
      // The July 5 2004 was a Monday and this from here to eternity!!
      $wDay = $day + 4;
      $dayName[$day] = ucfirst(strftime("%A", strtotime("2004-07-$wDay")));
    }

    for ($day = 1; $day <= 7; $day++) {
      $str .= "<td align='center' valign='bottom' nowrap>$dayName[$day]</td>";
    }

    $str .= "</tr></table></td>";

    $str .= "</tr></table>";

    $str .= "<br><br><table border='0' cellpadding='2' cellspacing='2'>";
    $str .= "<tr><td nowrap><b>"
      . $this->mlText[13]
      . "</b></td><td><b>"
      . $this->mlText[4]
      . "</b></td></tr>";

    $str .= "<tr><td><br></td><td></td><td></td></tr>";

    for ($day = 1; $day <= 7; $day++) {
      $str .= "<tr><td nowrap>$dayName[$day]</td>"
        . "<td align='center'>$listVisits[$day]</td>"
        . "</tr>";
    }

    $str .= "</table>";

    return($str);
  }

  // Render the statistics for the hours of all the days of the year
  function renderHours() {
    global $gUtilsUrl;

    $this->loadLanguageTexts();

    // The width of a bar
    $barWidth = 10;

    // Maximum height of a bar
    $maxBarHeight = 120;

    $currentMonth = date("m", $this->clockUtils->getLocalTimeStamp());
    $currentYear = date("Y", $this->clockUtils->getLocalTimeStamp());

    // Get the colors
    $visitsColor = urlencode($this->visitsColor);

    $maxVisitors = 0;
    $maxVisits = 0;
    $listVisitors = array();
    $listVisits = array();
    for ($hour = 0; $hour < 24; $hour++) {
      $visits = $this->countHourVisits($currentYear, $hour);
      $maxVisits = max($maxVisits, $visits);
      $listVisits[$hour] = $visits;
    }

    $str = "<table border='0' cellpadding='2' cellspacing='2'><tr>";

    $str .= "<td nowrap align='center'><b>" . $this->mlText[16]
      . " " . $currentYear
      . "</b></td>";

    $str .= "</tr><tr>";

    $url = $gUtilsUrl . "/printBarImage.php?color=$visitsColor&width=$barWidth&height=$barWidth";
    $image = "<img src='$url' title='' alt='' />";
    $str .= "<td nowrap>$image " . $this->mlText[4] . "</td>";

    $str .= "</tr><tr>";

    $str .= "<td align='center'><table border='0' width='100%' cellpadding='2' cellspacing='2'><tr>";

    for ($hour = 0; $hour < 24; $hour++) {
      // Calculate the bar widths based on the percentage values
      if ($maxVisits > 0) {
        $visitsHeight = round(($listVisits[$hour] * $maxBarHeight) / $maxVisits);
      } else {
        $visitsHeight = 0;
      }

      // Have non null heights to display an image
      if ($visitsHeight == 0) {
        $visitsHeight = 1;
      }

      // Create the images
      $url = $gUtilsUrl . "/printBarImage.php?color=$visitsColor&width=$barWidth&height=$visitsHeight";
      $imageVisits = "<img src='$url' title='' alt='' />";

      $str .= "<td valign='bottom' nowrap>$imageVisits</td>";
    }

    $str .= "</tr><tr>";

    for ($hour = 0; $hour < 24; $hour++) {
      $str .= "<td align='center' valign='bottom' nowrap>$hour</td>";
    }

    $str .= "</tr></table></td>";

    $str .= "</tr></table>";

    $str .= "<br><br><table border='0' cellpadding='2' cellspacing='2'>";
    $str .= "<tr><td nowrap><b>"
      . $this->mlText[17]
      . "</b></td><td><b>"
      . $this->mlText[4]
      . "</b></td></tr>";

    $str .= "<tr><td><br></td><td></td><td></td></tr>";

    for ($hour = 0; $hour < 24; $hour++) {
      $str .= "<tr><td nowrap>$hour</td>"
        . "<td align='center'>$listVisits[$hour]</td>"
        . "</tr>";
    }

    $str .= "</table>";

    return($str);
  }

  // Render the statistics by browser
  function renderBrowsers() {
    global $gUtilsUrl;
    global $gStatisticsImagePath;
    global $gStatisticsImageUrl;

    $this->loadLanguageTexts();

    // The height of a bar
    $barHeight = 10;

    // Maximum height of a bar
    $maxBarWidth = 120;

    $currentYear = date("Y", $this->clockUtils->getLocalTimeStamp());

    // Get the colors
    $visitsColor = urlencode($this->visitsColor);

    $browsers = array();

    // Get the number of visits per browser
    $actualBrowsers = $this->countByBrowser();
    foreach ($actualBrowsers as $actualBrowser) {
      list($count, $visitorBrowser) = $actualBrowser;

      $isBrowser = false;
      foreach ($this->browserNames as $browserName) {
        // There is one function per browser
        eval("\$isBrowser = \$this->browserIs$browserName(\$visitorBrowser);");
        if ($isBrowser) {
          // Increment instead of assign the count as different referer strings
          // can relate to the same browser
          if (!isset($browsers[$browserName])) {
            $browsers[$browserName] = 0;
          }
          $browsers[$browserName] += $count;
          break;
        }
      }

      $browserName = 'Unknown';
      if (!$isBrowser && !$this->isRobot($visitorBrowser)) {
        // Increment instead of assign the count as different referer strings
        // can relate to the same browser
        if (!isset($browsers[$browserName])) {
          $browsers[$browserName] = 0;
        }
        $browsers[$browserName] += $count;
      }
    }

    // Sort the values by decreasing order
    arsort($browsers);

    // Get the total number of visits
    $totalVisits = 0;
    foreach ($browsers as $browserName => $visits) {
      $totalVisits += $visits;
    }

    $str = "<table border='0' cellpadding='2' cellspacing='2'><tr>";

    $str .= "<td nowrap align='center'><b>" . $this->mlText[18]
      . " " . $currentYear
      . "</b></td></tr>";

    $str .= "</tr></table>";

    $str .= "<br>";

    $str .= "<table border='0' cellpadding='2' cellspacing='2'>";

    $str .= "<tr><td nowrap><b>" . $this->mlText[19] . "</b></td>"
      . "<td nowrap><b>" . $this->mlText[4] . "</b></td>"
      . "<td nowrap><b>" . $this->mlText[20] . "</b></td>"
      . "<td nowrap></td>"
      . "</tr>";

    $str .= "<tr><td><br></td><td></td><td></td><td></td></tr>";

    foreach ($browsers as $browserName => $visits) {
      // Calculate the bar width based on the percentage values
      if ($totalVisits > 0) {
        $visitsWidth = round(($visits * $maxBarWidth) / $totalVisits);
      } else {
        $visitsWidth = 0;
      }

      // Have non null height to display an image
      if ($visitsWidth == 0) {
        $visitsWidth = 1;
      }

      if ($totalVisits > 0) {
        $percentage = round(($visits * 100) / $totalVisits);
      } else {
        $percentage = 0;
      }

      // Create the image
      $url = $gUtilsUrl . "/printBarImage.php?color=$visitsColor&width=$visitsWidth&height=$barHeight";
      $imageVisits = "<img src='$url' title='' alt='' />";

      $iconFilename = "browser" . $browserName . ".png";
      $iconFile = $gStatisticsImagePath . $iconFilename;
      $iconUrl = $gStatisticsImageUrl . "/" . $iconFilename;
      if (is_file($iconFile)) {
        $srcIcon = "<img src='$iconUrl" . "' title='' alt='' />";
      } else {
        $srcIcon = '';
      }

      $str .= "<tr><td nowrap>$srcIcon $browserName</td>"
        . "<td nowrap align='center'>$visits</td>"
        . "<td nowrap align='center'>$percentage</td>"
        . "<td nowrap>$imageVisits</td>"
        . "</tr>";
    }

    $str .= "</table>";

    return($str);
  }

  // Render the statistics by mobile phones
  function renderPhones() {
    global $gUtilsUrl;
    global $gStatisticsImagePath;
    global $gStatisticsImageUrl;

    // Check if the phones must be rendered
    if ($this->adminModuleUtils->moduleGrantedToAdmin(MODULE_PHONE_MODEL)) {
      return;
    }

    $this->loadLanguageTexts();

    // The height of a bar
    $barHeight = 10;

    // Maximum height of a bar
    $maxBarWidth = 120;

    $currentYear = date("Y", $this->clockUtils->getLocalTimeStamp());

    // Get the colors
    $visitsColor = urlencode($this->visitsColor);

    $phones = array();

    // Get the number of visits per phone
    $actualBrowsers = $this->countByBrowser();
    foreach ($actualBrowsers as $actualBrowser) {
      list($count, $visitorBrowser) = $actualBrowser;

      $isPhone = false;
      foreach ($this->phoneNames as $phoneName) {
        // There is one function per phone
        eval("\$isPhone = \$this->phoneIs$phoneName(\$visitorBrowser);");
        if ($isPhone) {
          if (!isset($phones[$phoneName])) {
            $phones[$phoneName] = 0;
          }
          $phones[$phoneName] += $count;
          break;
        }
      }
    }

    // Sort the values by decreasing order
    arsort($phones);

    // Get the total number of visits
    $totalVisits = 0;
    foreach ($phones as $phoneName => $visits) {
      $totalVisits += $visits;
    }

    $str = "<table border='0' cellpadding='2' cellspacing='2'><tr>";

    $str .= "<td nowrap align='center'><b>" . $this->mlText[34]
      . " " . $currentYear
      . "</b></td></tr>";

    $str .= "</tr></table>";

    $str .= "<br>";

    $str .= "<table border='0' cellpadding='2' cellspacing='2'>";

    $str .= "<tr><td nowrap><b>" . $this->mlText[35] . "</b></td>"
      . "<td nowrap><b>" . $this->mlText[4] . "</b></td>"
      . "<td nowrap><b>" . $this->mlText[20] . "</b></td>"
      . "<td nowrap></td>"
      . "</tr>";

    $str .= "<tr><td><br></td><td></td><td></td><td></td></tr>";

    foreach ($phones as $phoneName => $visits) {
      // Calculate the bar width based on the percentage values
      if ($totalVisits > 0) {
        $visitsWidth = round(($visits * $maxBarWidth) / $totalVisits);
      } else {
        $visitsWidth = 0;
      }

      // Have non null height to display an image
      if ($visitsWidth == 0) {
        $visitsWidth = 1;
      }

      if ($totalVisits > 0) {
        $percentage = round(($visits * 100) / $totalVisits);
      } else {
        $percentage = 0;
      }

      // Create the image
      $url = $gUtilsUrl . "/printBarImage.php?color=$visitsColor&width=$visitsWidth&height=$barHeight";
      $imageVisits = "<img src='$url' title='' alt='' />";

      $iconFilename = "phone" . $phoneName . ".png";
      $iconFile = $gStatisticsImagePath . $iconFilename;
      $iconUrl = $gStatisticsImageUrl . "/" . $iconFilename;
      if (is_file($iconFile)) {
        $srcIcon = "<img src='$iconUrl"
          . "' title='' alt='' />";
      } else {
        $srcIcon = '';
      }

      $str .= "<tr><td nowrap>$srcIcon $phoneName</td>"
        . "<td nowrap align='center'>$visits</td>"
        . "<td nowrap align='center'>$percentage</td>"
        . "<td nowrap>$imageVisits</td>"
        . "</tr>";
    }

    $str .= "</table>";

    return($str);
  }

  // Render the statistics by robot
  function renderRobots() {
    global $gUtilsUrl;
    global $gStatisticsImagePath;
    global $gStatisticsImageUrl;

    $this->loadLanguageTexts();

    // The height of a bar
    $barHeight = 10;

    // Maximum height of a bar
    $maxBarWidth = 120;

    $currentYear = date("Y", $this->clockUtils->getLocalTimeStamp());

    // Get the colors
    $visitsColor = urlencode($this->visitsColor);

    $robots = array();

    // Get the number of visits per robot
    $actualBrowsers = $this->countByBrowser();
    foreach ($actualBrowsers as $actualBrowser) {
      list($count, $visitorBrowser) = $actualBrowser;

      foreach ($this->robotNames as $robotName) {
        // There is one function per robot
        eval("\$isBrowser = \$this->robotIs$robotName(\$visitorBrowser);");
        if ($isBrowser) {
          if (!isset($robots[$robotName])) {
            $robots[$robotName] = 0;
          }
          $robots[$robotName] += $count;
          break;
        }
      }
    }

    // Sort the values by decreasing order
    arsort($robots);

    // Get the total number of visits
    $totalVisits = 0;
    foreach ($robots as $robotName => $visits) {
      $totalVisits += $visits;
    }

    $str = "<table border='0' cellpadding='2' cellspacing='2'><tr>";

    $str .= "<td nowrap align='center'><b>" . $this->mlText[23]
      . " " . $currentYear
      . "</b></td></tr>";

    $str .= "</tr></table>";

    $str .= "<br>";

    $str .= "<table border='0' cellpadding='2' cellspacing='2'>";

    $str .= "<tr><td nowrap><b>" . $this->mlText[24] . "</b></td>"
      . "<td nowrap><b>" . $this->mlText[4] . "</b></td>"
      . "<td nowrap><b>" . $this->mlText[20] . "</b></td>"
      . "<td nowrap></td>"
      . "</tr>";

    $str .= "<tr><td><br></td><td></td><td></td><td></td></tr>";

    foreach ($robots as $robotName => $visits) {
      // Calculate the bar width based on the percentage values
      if ($totalVisits > 0) {
        $visitsWidth = round(($visits * $maxBarWidth) / $totalVisits);
      } else {
        $visitsWidth = 0;
      }

      // Have non null height to display an image
      if ($visitsWidth == 0) {
        $visitsWidth = 1;
      }

      if ($totalVisits > 0) {
        $percentage = round(($visits * 100) / $totalVisits);
      } else {
        $percentage = 0;
      }

      // Create the image
      $url = $gUtilsUrl . "/printBarImage.php?color=$visitsColor&width=$visitsWidth&height=$barHeight";
      $imageVisits = "<img src='$url' title='' alt='' />";

      $iconFilename = "robot" . $robotName . ".png";
      $iconFile = $gStatisticsImagePath . $iconFilename;
      $iconUrl = $gStatisticsImageUrl . "/" . $iconFilename;
      if (is_file($iconFile)) {
        $srcIcon = "<img src='$iconUrl"
          . "' title='' alt='' />";
      } else {
        $srcIcon = '';
      }

      $str .= "<tr><td nowrap>$srcIcon $robotName</td>"
        . "<td nowrap align='center'>$visits</td>"
        . "<td nowrap align='center'>$percentage</td>"
        . "<td nowrap>$imageVisits</td>"
        . "</tr>";
    }

    $str .= "</table>";

    return($str);
  }

  // Checking it is not a robot
  function isRobot($visitorBrowser) {
    if (
      $this->robotIsGoogle($visitorBrowser)
      || $this->robotIsLinkWalker($visitorBrowser)
      || $this->robotIsMSN($visitorBrowser)
      || $this->robotIsGrub($visitorBrowser)
      || $this->robotIsWiseNut($visitorBrowser)
      || $this->robotIsYahooSlurp($visitorBrowser)
      || $this->robotIsAlexa($visitorBrowser)
      || $this->robotIsOpenFind($visitorBrowser)
    ) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check the robot
  function robotIsGoogle($visitorBrowser) {
    return(stristr($visitorBrowser, "Googlebot"));
  }

  // Check the robot
  function robotIsLinkWalker($visitorBrowser) {
    return(stristr($visitorBrowser, "LinkWalker"));
  }

  // Check the robot
  function robotIsMSN($visitorBrowser) {
    return(stristr($visitorBrowser, "msnbot"));
  }

  // Check the robot
  function robotIsGrub($visitorBrowser) {
    return(stristr($visitorBrowser, "grub"));
  }

  // Check the robot
  function robotIsWiseNut($visitorBrowser) {
    return(stristr($visitorBrowser, "WiseNut"));
  }

  // Check the robot
  function robotIsYahooSlurp($visitorBrowser) {
    return(stristr($visitorBrowser, "Yahoo! Slurp"));
  }

  // Check the robot
  function robotIsAlexa($visitorBrowser) {
    return(stristr($visitorBrowser, "ia_archiver"));
  }

  // Check the robot
  function robotIsOpenFind($visitorBrowser) {
    return(stristr($visitorBrowser, "openfind"));
  }

  // Check the browser
  function browserIsWebTV($visitorBrowser) {
    return(stristr($visitorBrowser, "WebTV"));
  }

  // Check the browser
  function browserIsLynx($visitorBrowser) {
    return(stristr($visitorBrowser, "Lynx"));
  }

  // Check the browser
  function browserIsOpera($visitorBrowser) {
    return(stristr($visitorBrowser, "Opera"));
  }

  // Check the browser
  function browserIsSafari($visitorBrowser) {
    return(stristr($visitorBrowser, "Safari"));
  }

  // Check the browser
  function browserIsFirefox($visitorBrowser) {
    return(stristr($visitorBrowser, "Firefox"));
  }

  // Check the browser
  function browserIsNetscape($visitorBrowser) {
    return(stristr($visitorBrowser, "Netscape"));
  }

  // Check the browser
  function browserIsKonqueror($visitorBrowser) {
    return(stristr($visitorBrowser, "Konqueror"));
  }

  // Check the browser
  function browserIsMozilla($visitorBrowser) {
    return(stristr($visitorBrowser, "Mozilla") && !stristr($visitorBrowser, "MSIE"));
  }

  // Check the browser
  function browserIsMSIE($visitorBrowser) {
    return(stristr($visitorBrowser, "MSIE"));
  }

  // Check the phone
  function phoneIsSonyEricsson($visitorBrowser) {
    return(stristr($visitorBrowser, "SonyEricsson"));
  }

  // Check the phone
  function phoneIsNEC($visitorBrowser) {
    return(stristr($visitorBrowser, "e808"));
  }

  // Render the statistics by operating system
  function renderOss() {
    global $gUtilsUrl;
    global $gStatisticsImagePath;
    global $gStatisticsImageUrl;

    $this->loadLanguageTexts();

    // The height of a bar
    $barHeight = 10;

    // Maximum height of a bar
    $maxBarWidth = 120;

    $currentYear = date("Y", $this->clockUtils->getLocalTimeStamp());

    // Get the colors
    $visitsColor = urlencode($this->visitsColor);

    $oss = array();

    // Get the number of visits per os
    $actualBrowsers = $this->countByBrowser();
    foreach ($actualBrowsers as $actualBrowser) {
      list($count, $visitorBrowser) = $actualBrowser;

      $isOs = false;
      foreach ($this->osNames as $osName) {
        // There is one function per os
        eval("\$isOs = \$this->osIs$osName(\$visitorBrowser);");
        if ($isOs) {
          if (!isset($oss[$osName])) {
            $oss[$osName] = 0;
          }
          $oss[$osName] += $count;
          break;
        }
      }

      $osName = 'Unknown';
      if (!$isOs && !$this->isRobot($visitorBrowser)) {
        if (!isset($oss[$osName])) {
          $oss[$osName] = 0;
        }
        $oss[$osName] += $count;
      }

    }

    // Sort the values by decreasing order
    arsort($oss);

    // Get the total number of visits
    $totalVisits = 0;
    foreach ($oss as $osName => $visits) {
      $totalVisits += $visits;
    }

    $str = "<table border='0' cellpadding='2' cellspacing='2'><tr>";

    $str .= "<td nowrap align='center'><b>" . $this->mlText[21]
      . " " . $currentYear
      . "</b></td></tr>";

    $str .= "</tr></table>";

    $str .= "<br>";

    $str .= "<table border='0' cellpadding='2' cellspacing='2'>";

    $str .= "<tr><td nowrap><b>" . $this->mlText[22] . "</b></td>"
      . "<td nowrap><b>" . $this->mlText[4] . "</b></td>"
      . "<td nowrap><b>" . $this->mlText[20] . "</b></td>"
      . "<td nowrap></td>"
      . "</tr>";

    $str .= "<tr><td><br></td><td></td><td></td><td></td></tr>";

    foreach ($oss as $osName => $visits) {
      // Calculate the bar width based on the percentage values
      if ($totalVisits > 0) {
        $visitsWidth = round(($visits * $maxBarWidth) / $totalVisits);
      } else {
        $visitsWidth = 0;
      }

      // Have non null height to display an image
      if ($visitsWidth == 0) {
        $visitsWidth = 1;
      }

      if ($totalVisits > 0) {
        $percentage = round(($visits * 100) / $totalVisits);
      } else {
        $percentage = 0;
      }

      // Create the image
      $url = $gUtilsUrl . "/printBarImage.php?color=$visitsColor&width=$visitsWidth&height=$barHeight";
      $imageVisits = "<img src='$url' title='' alt='' />";

      $iconFilename = "os" . $osName . ".png";
      $iconFile = $gStatisticsImagePath . $iconFilename;
      $iconUrl = $gStatisticsImageUrl . "/" . $iconFilename;
      if (is_file($iconFile)) {
        $srcIcon = "<img src='$iconUrl"
          . "' title='' alt='' />";
      } else {
        $srcIcon = '';
      }

      $str .= "<tr><td nowrap>$srcIcon $osName</td>"
        . "<td nowrap align='center'>$visits</td>"
        . "<td nowrap align='center'>$percentage</td>"
        . "<td nowrap>$imageVisits</td>"
        . "</tr>";
    }

    $str .= "</table>";

    return($str);
  }

  // Check the operating system
  function osIsWebTV($visitorBrowser) {
    return(stristr($visitorBrowser, "WebTV"));
  }

  // Check the operating system
  function osIsSunOS($visitorBrowser) {
    return(stristr($visitorBrowser, "SunOS"));
  }

  // Check the operating system
  function osIsUnix($visitorBrowser) {
    return(stristr($visitorBrowser, "Unix"));
  }

  // Check the operating system
  function osIsOS2($visitorBrowser) {
    return(stristr($visitorBrowser, "OS2"));
  }

  // Check the operating system
  function osIsMacOS($visitorBrowser) {
    return(stristr($visitorBrowser, "MacOS") || stristr($visitorBrowser, "Macintosh") || stristr($visitorBrowser, "Mac_PowerPC"));
  }

  // Check the operating system
  function osIsFreeBSD($visitorBrowser) {
    return(stristr($visitorBrowser, "FreeBSD"));
  }

  // Check the operating system
  function osIsLinux($visitorBrowser) {
    return(stristr($visitorBrowser, "Linux"));
  }

  // Check the operating system
  function osIsWindows95($visitorBrowser) {
    return(stristr($visitorBrowser, "Windows 95"));
  }

  // Check the operating system
  function osIsWindows98($visitorBrowser) {
    return(stristr($visitorBrowser, "Windows 98") || stristr($visitorBrowser, "Win98"));
  }

  // Check the operating system
  function osIsWindowsNT($visitorBrowser) {
    return(stristr($visitorBrowser, "Windows NT"));
  }

  // Check the operating system
  function osIsWindowsMe($visitorBrowser) {
    return(stristr($visitorBrowser, "Windows Me"));
  }

  // Check the operating system
  function osIsWindows2000($visitorBrowser) {
    return(stristr($visitorBrowser, "Windows 2000"));
  }

  // Check the operating system
  function osIsWindowsXP($visitorBrowser) {
    return(stristr($visitorBrowser, "Windows XP"));
  }

  // Get the system date and time of the server
  function getSystemDateTime() {
    $systemDateTime = $this->clockUtils->getSystemDateTime();

    return($systemDateTime);
  }

  // Get the visit duration
  function getVisitDuration() {
    if (!$visitDuration = $this->propertyUtils->retrieve($this->propertyVisitDuration)) {
      $visitDuration = $this->visitDuration;
    }

    return($visitDuration);
  }

  // Set the visit duration
  // It is the minimum amount of time between two visits
  function setVisitDuration($visitDuration) {
    $this->propertyUtils->store($this->propertyVisitDuration, $visitDuration);
  }

  // Get the counter date
  function getCounterDate() {
    $counterDate = substr($this->getCounterTime(), 0, 10);

    return($counterDate);
  }

  // Get the counter time
  function getCounterTime() {
    if (!$counterTime = $this->propertyUtils->retrieve($this->propertyCounterTime)) {
      $this->resetCounterTime();
    }

    if (!$counterTime) {
      $counterTime = $this->getSystemDateTime();
    }

    return($counterTime);
  }

  // Set the counter date
  // It is the date from which to start counting the visits and the visitors
  function setCounterTime($counterTime) {
    $this->propertyUtils->store($this->propertyCounterTime, $counterTime);
  }

  // Reset the counter date to the current time stamp
  function resetCounterTime() {
    $this->setCounterTime($this->getSystemDateTime());
  }

  // Get the number of visitors since the counter date and time
  function getCounterVisitors() {
    return($this->countVisitors($this->getCounterTime()));
  }

  // Get the number of visits since the counter date and time
  function getCounterVisits() {
    return($this->countVisits($this->getCounterTime()));
  }

  function logVisit() {
    global $REMOTE_ADDR, $HTTP_USER_AGENT;

    // Do not collect the visitors if the module is not granted to the web site
    // Can't yet be done as the system does not offer module access management from the website
    //    if (!$this->websiteUtils->isCurrentWebsiteModule(MODULE_STATISTICS)) {
    //      return;
    //      }

    // Check if a robot visit must be logged
    $displayRobot = $this->preferenceUtils->getValue("STATISTICS_DISPLAY_ROBOT");
    if ($this->isRobot($HTTP_USER_AGENT) && !$displayRobot) {
      return;
    }

    // Get the host address of the visitor
    $remoteHostAddress = $REMOTE_ADDR;

    // Get the browser of the visitor
    $visitorBrowser = $HTTP_USER_AGENT;

    // Get the refering url of the visitor
    $HTTP_REFERER = LibEnv::getEnvSERVER('HTTP_REFERER');
    $visitorReferer = $HTTP_REFERER;

    // Get the time stamp
    $visitDateTime = $this->getSystemDateTime();

    // Get today's date
    $day = $this->getTodaysSystemDate();

    if ($this->isNewVisit($remoteHostAddress)) {
      $statisticsVisit = new StatisticsVisit();
      $statisticsVisit->setVisitDateTime($visitDateTime);
      $statisticsVisit->setVisitorHostAddress($remoteHostAddress);
      $statisticsVisit->setVisitorBrowser($visitorBrowser);
      $statisticsVisit->setVisitorReferer($visitorReferer);
      $this->insert($statisticsVisit);
    }

    $this->statisticsPageUtils->logPageHit();
  }

  // Delete the old statistics
  function deleteOldVisits() {
    $today = $this->clockUtils->getSystemDate();

    $year = substr($today, 0, 4);
    $year -= STATISTICS_DELETE_AFTER_YEAR;

    if ($this->countOldVisits($year) > 0) {
      $this->deleteOldStat($year);

      $this->resetCounterTime();
    }

    $this->statisticsPageUtils->deleteOldHits();
  }

  // Get today's system date
  function getTodaysSystemDate() {
    $today = $this->clockUtils->getSystemDate();

    return($today);
  }

  // Check if the current hit is counted as a visit
  function isNewVisit($remoteHostAddress) {
    if ($statisticsVisit = $this->selectHostLastVisit($remoteHostAddress)) {
      $hostLastVisitDateTime = strtotime($statisticsVisit->getVisitDateTime());

      // Check if the current hit is late enough
      if ($hostLastVisitDateTime + ($this->getVisitDuration() * 60) >= time()) {
        return(false);
      }
    }

    return(true);
  }

}

/* Countries Code

$negara = array(
"ad" => "Andorra",
"ae" => "United Arab Emirates",
"af" => "Afghanistan",
"ag" => "Antigua and Barbuda",
"ai" => "Anguilla",
"al" => "Albania",
"am" => "Armenia",
"an" => "Netherlands Antilles",
"ao" => "Angola",
"aq" => "Antarctica",
"ar" => "Argentina",
"as" => "American Samoa",
"at" => "Austria",
"au" => "Australia",
"aw" => "Aruba",
"az" => "Azerbaijan",
"ba" => "Bosnia Herzegovina",
"bb" => "Barbados",
"bd" => "Bangladesh",
"be" => "Belgium",
"bf" => "Burkina Faso",
"bg" => "Bulgaria",
"bh" => "Bahrain",
"bi" => "Burundi",
"bj" => "Benin",
"bm" => "Bermuda",
"bn" => "Brunei Darussalam",
"bo" => "Bolivia",
"br" => "Brazil",
"bs" => "Bahamas",
"bt" => "Bhutan",
"bv" => "Bouvet Island",
"bw" => "Botswana",
"by" => "Belarus",
"bz" => "Belize",
"ca" => "Canada",
"cc" => "Cocos (Keeling) Islands",
"cf" => "Central African Republic",
"cg" => "Congo",
"ch" => "Switzerland",
"ci" => "Cote DIvoire",
"ck" => "Cook Islands",
"cl" => "Chile",
"cm" => "Cameroon",
"cn" => "China",
"co" => "Colombia",
"cr" => "Costa Rica",
"cs" => "Czechoslovakia",
"cu" => "Cuba",
"cv" => "Cape Verde",
"cx" => "Christmas Island",
"cy" => "Cyprus",
"cz" => "Czech Republic",
"de" => "Germany",
"dj" => "Djibouti",
"dk" => "Denmark",
"dm" => "Dominica",
"do" => "Dominican Republic",
"dz" => "Algeria",
"ec" => "Ecuador",
"ee" => "Estonia",
"eg" => "Egypt",
"eh" => "Western Sahara",
"er" => "Eritrea",
"es" => "Spain",
"et" => "Ethiopia",
"fi" => "Finland",
"fj" => "Fiji",
"fk" => "Falkland Islands (Malvinas)",
"fm" => "Micronesia",
"fo" => "Faroe Islands",
"fr" => "France",
"fx" => "France (Metropolitan)",
"ga" => "Gabon",
"gb" => "Great Britain (UK)",
"gd" => "Grenada",
"ge" => "Georgia",
"gf" => "French Guiana",
"gh" => "Ghana",
"gi" => "Gibraltar",
"gl" => "Greenland",
"gm" => "Gambia",
"gn" => "Guinea",
"gp" => "Guadeloupe",
"gq" => "Equatorial Guinea",
"gr" => "Greece",
"gs" => "S. Georgia and S. Sandwich Islands",
"gt" => "Guatemala",
"gu" => "Guam",
"gw" => "Guinea-Bissau",
"gy" => "Guyana",
"hk" => "Hong Kong",
"hm" => "Heard and McDonald Islands",
"hn" => "Honduras",
"hr" => "Croatia (Hrvatska)",
"ht" => "Haiti",
"hu" => "Hungary",
"id" => "Indonesia",
"ie" => "Ireland",
"il" => "Israel",
"in" => "India",
"io" => "British Indian Ocean Territory",
"iq" => "Iraq",
"ir" => "Iran",
"is" => "Iceland",
"it" => "Italy",
"jm" => "Jamaica",
"jo" => "Jordan",
"jp" => "Japan",
"ke" => "Kenya",
"kg" => "Kyrgyzstan",
"kh" => "Cambodia",
"ki" => "Kiribati",
"km" => "Comoros",
"kn" => "Saint Kitts and Nevis",
"kp" => "North Korea",
"kr" => "South Korea",
"kw" => "Kuwait",
"ky" => "Cayman Islands",
"kz" => "Kazakhstan",
"la" => "Laos",
"lb" => "Lebanon",
"lc" => "Saint Lucia",
"li" => "Liechtenstein",
"lk" => "Sri Lanka",
"lr" => "Liberia",
"ls" => "Lesotho",
"lt" => "Lithuania",
"lu" => "Luxembourg",
"lv" => "Latvia",
"ly" => "Libya",
"ma" => "Morocco",
"mc" => "Monaco",
"md" => "Moldova",
"mg" => "Madagascar",
"mh" => "Marshall Islands",
"mk" => "Macedonia",
"ml" => "Mali",
"mm" => "Myanmar",
"mn" => "Mongolia",
"mo" => "Macau",
"mp" => "Northern Mariana Islands",
"mq" => "Martinique",
"mr" => "Mauritania",
"ms" => "Montserrat",
"mt" => "Malta",
"mu" => "Mauritius",
"mv" => "Maldives",
"mw" => "Malawi",
"mx" => "Mexico",
"my" => "Malaysia",
"mz" => "Mozambique",
"na" => "Namibia",
"nc" => "New Caledonia",
"ne" => "Niger",
"nf" => "Norfolk Island",
"ng" => "Nigeria",
"ni" => "Nicaragua",
"nl" => "Netherlands",
"no" => "Norway",
"np" => "Nepal",
"nr" => "Nauru",
"nt" => "Neutral Zone",
"nu" => "Niue",
"nz" => "New Zealand (Aotearoa)",
"om" => "Oman",
"pa" => "Panama",
"pe" => "Peru",
"pf" => "French Polynesia",
"pg" => "Papua New Guinea",
"ph" => "Philippines",
"pk" => "Pakistan",
"pl" => "Poland",
"pm" => "St. Pierre and Miquelon",
"pn" => "Pitcairn",
"pr" => "Puerto Rico",
"pt" => "Portugal",
"pw" => "Palau",
"py" => "Paraguay",
"qa" => "Qatar",
"re" => "Reunion",
"ro" => "Romania",
"ru" => "Russian Federation",
"rw" => "Rwanda",
"sa" => "Saudi Arabia",
"sb" => "Solomon Islands",
"sc" => "Seychelles",
"sd" => "Sudan",
"se" => "Sweden",
"sg" => "Singapore",
"sh" => "St. Helena",
"si" => "Slovenia",
"sj" => "Svalbard and Jan Mayen Islands",
"sk" => "Slovak Republic",
"sl" => "Sierra Leone",
"sm" => "San Marino",
"sn" => "Senegal",
"so" => "Somalia",
"sr" => "Suriname",
"st" => "Sao Tome and Principe",
"su" => "USSR (Former)",
"sv" => "El Salvador",
"sy" => "Syria",
"sz" => "Swaziland",
"tc" => "Turks and Caicos Islands",
"td" => "Chad",
"tf" => "French Southern Territories",
"tg" => "Togo",
"th" => "Thailand",
"tj" => "Tajikistan",
"tk" => "Tokelau",
"tm" => "Turkmenistan",
"tn" => "Tunisia",
"to" => "Tonga",
"tp" => "East Timor",
"tr" => "Turkey",
"tt" => "Trinidad and Tobago",
"tv" => "Tuvalu",
"tw" => "Taiwan",
"tz" => "Tanzania",
"ua" => "Ukraine",
"ug" => "Uganda",
"uk" => "United Kingdom",
"um" => "US Minor Outlying Islands",
"us" => "United States",
"uy" => "Uruguay",
"uz" => "Uzbekistan",
"va" => "Vatican City State (Holy See)",
"vc" => "Saint Vincent and the Grenadines",
"ve" => "Venezuela",
"vg" => "Virgin Islands (British)",
"vi" => "Virgin Islands (US)",
"vn" => "Vietnam",
"vu" => "Vanuatu",
"wf" => "Wallis and Futuna Islands",
"ws" => "Samoa",
"ye" => "Yemen",
"yt" => "Mayotte",
"yu" => "Yugoslavia",
"za" => "South Africa",
"zm" => "Zambia",
"zr" => "Zaire",
"zw" => "Zimbabwe",
"biz" => "Business",
"com" => "Commercial",
"edu" => "Educational",
"gov" => "US Government",
"int" => "International",
"mil" => "US Military",
"net" => "Network",
"org" => "Non-Profit Organization",
"info" => "Info",
"arpa" => "Old-Style Arpanet",
"nato" => "NATO Field"
);

 */

?>
