<?

class NewsPaperUtils extends NewsPaperDB {

  var $mlText;
  var $websiteText;

  var $imagePath;
  var $imageUrl;
  var $imageSize;

  var $chosenNewsHeading;
  var $chosenNewsEditor;

  var $languageUtils;
  var $preferenceUtils;
  var $commonUtils;
  var $popupUtils;
  var $profileUtils;
  var $clockUtils;
  var $playerUtils;
  var $colorboxUtils;
  var $templateUtils;
  var $newsStoryUtils;
  var $newsPublicationUtils;
  var $newsStoryImageUtils;
  var $newsHeadingUtils;

  function NewsPaperUtils() {
    $this->NewsPaperDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imageSize = 200000;
    $this->imagePath = $gDataPath . 'news/newsPaper/image/';
    $this->imageUrl = $gDataUrl . '/news/newsPaper/image';

    $this->chosenNewsHeading = "chosenNewsHeading";
    $this->chosenNewsEditor = "chosenNewsEditor";
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->imagePath)) {
      if (!is_dir($gDataPath . 'news')) {
        mkdir($gDataPath . 'news');
      }
      if (!is_dir($gDataPath . 'news/newsPaper')) {
        mkdir($gDataPath . 'news/newsPaper');
      }
      mkdir($this->imagePath);
      chmod($this->imagePath, 0755);
    }
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  function getNewsStatuses() {
    $this->loadLanguageTexts();

    $statuses = array(
      '-1' => '',
      NEWS_STATUS_PUBLISHED => $this->mlText[5],
      NEWS_STATUS_DEFERRED => $this->mlText[6],
      NEWS_STATUS_ARCHIVED => $this->mlText[7],
    );

    return($statuses);
  }

  // Delete a newspaper
  function deleteNewsPaper($newsPaperId) {
    global $gNewsUrl;

    // Delete the news stories
    if ($newsStories = $this->newsStoryUtils->selectByNewsPaper($newsPaperId)) {
      foreach ($newsStories as $newsStory) {
        $this->newsStoryUtils->deleteNewsStory($newsStory->getId());
      }
    }

    $this->delete($newsPaperId);
  }

  // Remove the non referenced files from the directory
  function deleteUnusedImageFiles() {
    $handle = opendir($this->imagePath);
    while ($oneFile = readdir($handle)) {
      if ($oneFile != "." && $oneFile != ".." && !strstr($oneFile, '*')) {
        if (!$this->imageIsUsed($oneFile)) {
          $oneFile = str_replace(" ", "\\ ", $oneFile);
          if (@file_exists($this->imagePath . $oneFile)) {
            @unlink($this->imagePath . $oneFile);
          }
        }
      }
    }
    closedir($handle);
  }

  // Check if an image is being used
  function imageIsUsed($image) {
    $isUsed = true;

    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByImage($image)) {
      if ($result->getRowCount() < 1) {
        $isUsed = false;
      }
    }

    return($isUsed);
  }

  // Publish a newsPaper
  function publish($newsPaperId) {
    if ($newsPaper = $this->selectById($newsPaperId)) {
      $newsPaper->setNotPublished('');
      $this->update($newsPaper);
    }
  }

  // Archive a newsPaper
  function archive($newsPaperId) {
    if ($newsPaper = $this->selectById($newsPaperId)) {
      $newsPaper->setNotPublished('1');
      $this->update($newsPaper);
    }
  }

  // Detach a news paper from its news publication
  function detachFromNewsPublication($newsPaperId) {
    if ($newsPaper = $this->selectById($newsPaperId)) {
      $newsPaper->setNewsPublicationId('');
      $this->update($newsPaper);
    }
  }

  // Archive the old newspapers
  function archiveOldNewspapers_NOT_USED($newsPublicationId) {
    if ($newsPublication = $this->newsPublicationUtils->selectById($newsPublicationId)) {
      $autoArchive = $newsPublication->getAutoArchive();
      if ($autoArchive && is_numeric($autoArchive)) {
        // Get the date since which to archive the newspapers
        $systemDate = $this->clockUtils->getSystemDate();
        $sinceDate = $this->clockUtils->incrementDays($systemDate, -1 * $autoArchive);
        $this->archiveByReleaseDate($newsPublicationId, $sinceDate, $systemDate);
      }
    }
  }

  // Get the internal links for the newspapers
  // The newspapers are searched using their title or their publication name if any
  function getNewsPaperInternalLinks($searchPattern) {
    $this->loadLanguageTexts();

    $list = array();

    if ($searchPattern) {
      $systemDate = $this->clockUtils->getSystemDate();
      $newsPapers = $this->selectLikePatternInNewsPaperAndNewsPublication($searchPattern, $systemDate);
      foreach ($newsPapers as $newsPaper) {
        $newsPaperId = $newsPaper->getId();
        $title = $newsPaper->getTitle();
        $newsPublicationId = $newsPaper->getNewsPublicationId();
        if ($newsPublication = $this->newsPublicationUtils->selectById($newsPublicationId)) {
          $name = $newsPublication->getName();
        } else {
          $name = '';
        }
        $strName = '';
        if ($name) {
          $strName .= $this->mlText[2] . " " . $name . ": ";
        }
        $strName .= $title;
        $list['SYSTEM_PAGE_NEWSPAPER' . $newsPaperId] = $strName;
      }
    }

    return($list);
  }

  // Get the list of published newspapers
  function getPublishedNewsPaperList($searchPattern = '') {
    $this->loadLanguageTexts();

    $list = array();

    $systemDate = $this->clockUtils->getSystemDate();

    if ($searchPattern) {
      $newsPapers = $this->selectLikePatternAndPublished($searchPattern, $systemDate);
    } else {
      $newsPapers = $this->selectByPublished($systemDate);
    }

    if ($newsPapers) {
      foreach ($newsPapers as $newsPaper) {
        $newsPaperId = $newsPaper->getId();
        $newsPublicationId = $newsPaper->getNewsPublicationId();
        if ($newsPublication = $this->newsPublicationUtils->selectById($newsPublicationId)) {
          $name = $newsPublication->getName();
        } else {
          $name = '';
        }
        $title = $newsPaper->getTitle();
        $list['SYSTEM_PAGE_NEWSPAPER' . $newsPaperId] = $this->mlText[2] . " " . $name . ": " . $title;
      }
    }

    return($list);
  }

  // Get the list of published newspapers for a publication
  function getPublicationNewsPaperList($newsPublicationId) {
    $list = array();

    if ($newsPublication = $this->newsPublicationUtils->selectById($newsPublicationId)) {
      $name = $newsPublication->getName();
      $newsPapers = $this->selectPublished($newsPublicationId);
      foreach ($newsPapers as $newsPaper) {
        $newsPaperId = $newsPaper->getId();
        $title = $newsPaper->getTitle();
        $releaseDate = $newsPaper->getReleaseDate();
        $wTitle = LibString::wordSubtract($title, 6);
        if (strlen($wTitle) < $title) {
          $wTitle .= ' ...';
        }
        $list[$newsPaperId] = $name . ': ' . $releaseDate . ' ' . $wTitle;
      }
    }

    return($list);
  }

  // Get the last published newspaper of the news publication
  function getLastPublishedNewsPaperId($newsPublicationId) {

    // Get the last published newspaper
    if ($newsPapers = $this->selectPublished($newsPublicationId)) {
      if (count($newsPapers) > 0) {
        $newsPaper = $newsPapers[0];
        $newsPaperId = $newsPaper->getId();

        return($newsPaperId);
      }
    }
  }

  // Select the published newspapers of a news publication
  function selectPublished($newsPublicationId = '', $start = false, $rows = false) {
    // If no news publication is specified then
    // get the first news publication
    if (!$newsPublicationId) {
      if ($newsPublications = $this->newsPublicationUtils->selectAll()) {
        if (count($newsPublications) > 0) {
          $newsPublication = $newsPublications[0];
          $newsPublicationId = $newsPublication->getId();
        }
      }
    }

    $systemDate = $this->clockUtils->getSystemDate();
    $newsPapers = $this->selectByNewsPublicationAndPublished($newsPublicationId, $systemDate, $start, $rows);

    return($newsPapers);
  }

  // Select the deferred newspapers of a news publication
  // The published ones with a release date to come
  function selectDeferred($newsPublicationId, $start = false, $rows = false) {
    $systemDate = $this->clockUtils->getSystemDate();
    $newsPapers = $this->selectByDeferred($newsPublicationId, $systemDate, $start, $rows);

    return($newsPapers);
  }

  // Select the archived newspapers of a news publication
  // The published ones with a passed archive date
  function selectArchived($newsPublicationId, $start = false, $rows = false) {
    $systemDate = $this->clockUtils->getSystemDate();
    $newsPapers = $this->selectByArchived($newsPublicationId, $systemDate, $start, $rows);

    return($newsPapers);
  }

  // Select the not published newspapers for a news publication
  function selectNotPublished($newsPublicationId, $start = false, $rows = false) {
    return($this->selectByNewsPublicationIdAndNotPublish($newsPublicationId, $start, $rows));
  }

  // Check if a newspaper is secured
  function isSecured($newsPaperId) {
    $secured = false;

    if ($newsPaper = $this->selectById($newsPaperId)) {
      $newsPublicationId = $newsPaper->getNewsPublicationId();
      if ($newsPublication = $this->newsPublicationUtils->selectById($newsPublicationId)) {
        $secured = $newsPublication->getSecured();
      }
    }

    return($secured);
  }

  // Check if a newspaper should display its news stories by sliding them down within the newspaper
  function slideDownDisplay($newsPaper) {
    $slideDown = false;

    $newsPublicationId = $newsPaper->getNewsPublicationId();
    if ($newsPublication = $this->newsPublicationUtils->selectById($newsPublicationId)) {
      $slideDown = $newsPublication->getSlideDown();
    }

    return($slideDown);
  }

  // Get the news stories for the events
  function collectNewsStoriesForEventsOnSelection($newsPaperId, $newsHeadingId, $period, $eventStartDate, $eventEndDate) {
    $newsStories = array();

    if ($eventStartDate || $eventEndDate) {
      $newsStories = $this->collectNewsStoriesForEvents($newsPaperId, $eventStartDate, $eventEndDate, $newsHeadingId);
    } else if ($period == NEWS_EVENT_SEARCH_TODAY) {
      $newsStories = $this->collectNewsStoriesForEventsForToday($newsPaperId, $newsHeadingId);
    } else if ($period == NEWS_EVENT_SEARCH_TOMORROW) {
      $newsStories = $this->collectNewsStoriesForEventsForTomorrow($newsPaperId, $newsHeadingId);
    } else if ($period == NEWS_EVENT_SEARCH_THISWEEK) {
      $newsStories = $this->collectNewsStoriesForEventsForThisWeek($newsPaperId, $newsHeadingId);
    } else if ($period == NEWS_EVENT_SEARCH_NEXTWEEK) {
      $newsStories = $this->collectNewsStoriesForEventsForNextWeek($newsPaperId, $newsHeadingId);
    } else if ($period == NEWS_EVENT_SEARCH_THISMONTH) {
      $newsStories = $this->collectNewsStoriesForEventsForThisMonth($newsPaperId, $newsHeadingId);
    } else if ($period == NEWS_EVENT_SEARCH_NEXTMONTH) {
      $newsStories = $this->collectNewsStoriesForEventsForNextMonth($newsPaperId, $newsHeadingId);
    } else if ($newsHeadingId) {
      $newsStories = $this->collectNewsStoriesForEvents($newsPaperId, '', '', $newsHeadingId);
    }

    return($newsStories);
  }

  // Get the period label
  function getPeriodLabel($period, $localEventStartDate, $localEventEndDate) {
    $this->loadLanguageTexts();

    $periodLabel = '';
    if ($localEventStartDate || $localEventEndDate) {
      if ($localEventStartDate) {
        $periodLabel .= $this->websiteText[21] . ' ' . $localEventStartDate;
      }
      if ($localEventEndDate) {
        $periodLabel .= ' ' . $this->websiteText[22] . ' ' . $localEventEndDate;
      }
    } else if ($period == NEWS_EVENT_SEARCH_TODAY) {
      $periodLabel = $this->websiteText[12];
    } else if ($period == NEWS_EVENT_SEARCH_TOMORROW) {
      $periodLabel = $this->websiteText[13];
    } else if ($period == NEWS_EVENT_SEARCH_THISWEEK) {
      $periodLabel = $this->websiteText[14];
    } else if ($period == NEWS_EVENT_SEARCH_NEXTWEEK) {
      $periodLabel = $this->websiteText[15];
    } else if ($period == NEWS_EVENT_SEARCH_THISMONTH) {
      $periodLabel = $this->websiteText[16];
    } else if ($period == NEWS_EVENT_SEARCH_NEXTMONTH) {
      $periodLabel = $this->websiteText[17];
    } else if ($newsHeadingId) {
      $periodLabel = '';
    }

    return($periodLabel);
  }

  // Get the news stories for the events for today
  function collectNewsStoriesForEventsForToday($newsPaperId, $newsHeadingId = '') {
    $systemDate = $this->clockUtils->getSystemDate();

    $eventNewsStories = $this->collectNewsStoriesForEvents($newsPaperId, $systemDate, $systemDate, $newsHeadingId);

    return($eventNewsStories);
  }

  // Get the news stories for the events for tomorrow
  function collectNewsStoriesForEventsForTomorrow($newsPaperId, $newsHeadingId = '') {
    $systemDate = $this->clockUtils->getSystemDate();

    $systemDate = $this->clockUtils->incrementDays($systemDate, 1);

    $eventNewsStories = $this->collectNewsStoriesForEvents($newsPaperId, $systemDate, $systemDate, $newsHeadingId);

    return($eventNewsStories);
  }

  // Get the news stories for the events for this week
  function collectNewsStoriesForEventsForThisWeek($newsPaperId, $newsHeadingId = '') {
    $systemDate = $this->clockUtils->getSystemDate();

    $firstWeekDay = $this->clockUtils->getFirstDayOfTheWeek($systemDate);
    $lastWeekDay = $this->clockUtils->getLastDayOfTheWeek($systemDate);

    $eventNewsStories = $this->collectNewsStoriesForEvents($newsPaperId, $firstWeekDay, $lastWeekDay, $newsHeadingId);

    return($eventNewsStories);
  }

  // Get the news stories for the events for next week
  function collectNewsStoriesForEventsForNextWeek($newsPaperId, $newsHeadingId = '') {
    $systemDate = $this->clockUtils->getSystemDate();

    $systemDate = $this->clockUtils->incrementWeeks($systemDate, 1);

    $firstWeekDay = $this->clockUtils->getFirstDayOfTheWeek($systemDate);
    $lastWeekDay = $this->clockUtils->getLastDayOfTheWeek($systemDate);

    $eventNewsStories = $this->collectNewsStoriesForEvents($newsPaperId, $firstWeekDay, $lastWeekDay, $newsHeadingId);

    return($eventNewsStories);
  }

  // Get the news stories for the events for the month of the specified date
  function collectNewsStoriesForEventsForAMonth($newsPaperId, $systemDate, $newsHeadingId = '') {
    $firstMonthDay = $this->clockUtils->getFirstDayOfTheMonth($systemDate);
    $lastMonthDay = $this->clockUtils->getLastDayOfTheMonth($systemDate);

    $eventNewsStories = $this->collectNewsStoriesForEvents($newsPaperId, $firstMonthDay, $lastMonthDay, $newsHeadingId);

    return($eventNewsStories);
  }

  // Get the news stories for the events for this month
  function collectNewsStoriesForEventsForThisMonth($newsPaperId, $newsHeadingId = '') {
    $systemDate = $this->clockUtils->getSystemDate();

    $firstMonthDay = $this->clockUtils->getFirstDayOfTheMonth($systemDate);
    $lastMonthDay = $this->clockUtils->getLastDayOfTheMonth($systemDate);

    $eventNewsStories = $this->collectNewsStoriesForEvents($newsPaperId, $firstMonthDay, $lastMonthDay, $newsHeadingId);

    return($eventNewsStories);
  }

  // Get the news stories for the events for the next 30 days
  function collectNewsStoriesForEventsForNext30Days($newsPaperId, $newsHeadingId = '') {
    $eventNewsStories = $this->collectNewsStoriesForEventsForNbOfComingDays($newsPaperId, 30, $newsHeadingId);

    return($eventNewsStories);
  }

  // Get the news stories for the events for the next 360 days
  function collectNewsStoriesForEventsForNext360Days($newsPaperId, $newsHeadingId = '') {
    $eventNewsStories = $this->collectNewsStoriesForEventsForNbOfComingDays($newsPaperId, 30, $newsHeadingId);

    return($eventNewsStories);
  }

  // Get the news stories for the events for some number of days
  function collectNewsStoriesForEventsForNbOfComingDays($newsPaperId, $nbDays, $newsHeadingId = '') {
    $systemDate = $this->clockUtils->getSystemDate();
    $inNbDaysDate = $this->clockUtils->incrementDays($systemDate, $nbDays);

    $eventNewsStories = $this->collectNewsStoriesForEvents($newsPaperId, $systemDate, $inNbDaysDate, $newsHeadingId);

    return($eventNewsStories);
  }

  // Get the news stories for the events for next month
  function collectNewsStoriesForEventsForNextMonth($newsPaperId, $newsHeadingId = '') {
    $systemDate = $this->clockUtils->getSystemDate();

    $systemDate = $this->clockUtils->incrementMonths($systemDate, 1);

    $firstMonthDay = $this->clockUtils->getFirstDayOfTheMonth($systemDate);
    $lastMonthDay = $this->clockUtils->getLastDayOfTheMonth($systemDate);

    $eventNewsStories = $this->collectNewsStoriesForEvents($newsPaperId, $firstMonthDay, $lastMonthDay, $newsHeadingId);

    return($eventNewsStories);
  }

  // Get the news stories for the events of today
  function collectNewsStoriesForEvents($newsPaperId, $startDate = '', $endDate = '', $newsHeadingId = '') {

    $eventNewsStories = array();

    $newsStories = $this->collectNewsStories($newsPaperId);

    foreach ($newsStories as $newsStory) {
      if ($newsHeadingId && $newsStory->getNewsHeading() != $newsHeadingId) {
        continue;
      }

      $eventStartDate = $newsStory->getEventStartDate();
      $eventEndDate = $newsStory->getEventEndDate();

      if (!$this->clockUtils->systemDateIsSet($eventStartDate)) {
        continue;
      } else if ($startDate && $this->clockUtils->systemDateIsSet($eventStartDate) && !$this->clockUtils->systemDateIsSet($eventEndDate)) {
        if (!$this->clockUtils->systemDateIsEqual($eventStartDate, $startDate)) {
          continue;
        }
      } else if ($this->clockUtils->systemDateIsSet($eventStartDate) && $this->clockUtils->systemDateIsSet($eventEndDate)) {
        if (($endDate && $this->clockUtils->systemDateIsGreater($eventStartDate, $endDate)) || ($startDate && $this->clockUtils->systemDateIsGreater($startDate, $eventEndDate))) {
          continue;
        }
      }

      array_push($eventNewsStories, $newsStory);
    }

    return($eventNewsStories);
  }

  // Check if a news publication offers a link to the archives of the newspaper
  function withArchive($newsPaper) {
    $with = false;

    $newsPublicationId = $newsPaper->getNewsPublicationId();
    if ($newsPublication = $this->newsPublicationUtils->selectById($newsPublicationId)) {
      $with = $newsPublication->getWithArchive();
    }

    return($with);
  }

  // Check if a news publication offers a link to the other newspaper
  function withOthers($newsPaper) {
    $with = false;

    $newsPublicationId = $newsPaper->getNewsPublicationId();
    if ($newsPublication = $this->newsPublicationUtils->selectById($newsPublicationId)) {
      $with = $newsPublication->getWithOthers();
    }

    return($with);
  }

  // Check if a news publication offers a link to the heading's news stories
  function withByHeading($newsPaper) {
    $with = false;

    $newsPublicationId = $newsPaper->getNewsPublicationId();
    if ($newsPublication = $this->newsPublicationUtils->selectById($newsPublicationId)) {
      $with = $newsPublication->getWithByHeading();
    }

    return($with);
  }

  // Check if a news publication hides the headings
  function hideHeading($newsPaper) {
    $hide = false;

    $newsPublicationId = $newsPaper->getNewsPublicationId();
    if ($newsPublication = $this->newsPublicationUtils->selectById($newsPublicationId)) {
      $hide = $newsPublication->getHideHeading();
    }

    return($hide);
  }

  // Render the player
  function renderPlayer($newsStory) {
    global $gDataUrl;

    $str = '';

    $audio = $newsStory->getAudio();

    if ($audio) {
      $autostart = $this->preferenceUtils->getValue("NEWS_PLAYER_AUTOSTART");

      $this->playerUtils->setAutostart($autostart);

      $str = $this->playerUtils->renderPlayer("$gDataUrl/news/newsStory/audio/$audio");
    }

    return($str);
  }

  // Render the list of newspapers for a publication
  function renderList($newsPublicationId = '') {
    global $gNewsUrl;
    global $gIsPhoneClient;

    // If no news publication is specified then
    // get the first news publication
    if (!$newsPublicationId) {
      if ($newsPublications = $this->newsPublicationUtils->selectAll()) {
        if (count($newsPublications) > 0) {
          $newsPublication = $newsPublications[0];
          $newsPublicationId = $newsPublication->getId();
        }
      }
    }

    $str = '';

    $str .= "\n<div class='newspaper_list'>";

    $str .= "\n<table border='0' width='100%' cellspacing='0' cellpadding='0'>";

    if ($newsPapers = $this->selectByNewsPublicationIdAndPublish($newsPublicationId)) {
      foreach ($newsPapers as $newsPaper) {
        $newsPaperId = $newsPaper->getId();
        $title = $newsPaper->getTitle();
        $releaseDate = $newsPaper->getReleaseDate();

        $releaseDate = $this->clockUtils->systemToLocalNumericDate($releaseDate);

        $strName = "<a href='$gNewsUrl/newsPaper/display.php?newsPaperId=$newsPaperId'>$title</a>";

        $str .= "\n<tr>";
        $str .= "\n<td><div class='newspaper_list_name'>$strName</div>";
        $str .= "</td><td>";
        $str .= "\n<div class='newspaper_list_release'>$releaseDate</div></td>";
        $str .= "\n</tr>";
      }
    }

    $str .= "\n</table>";

    $str .= "\n</div>";

    return($str);
  }

  // Print the newspaper
  function printNewsPaper($newsPaperId) {
    $str = '';

    $str .= LibJavaScript::getJSLib();

    $str .= "\n<script type='text/javascript'>printPage();</script>";

    $str .= "\n<div class='newspaper'>";

    $str .= $this->renderNewsPaper($newsPaperId);

    $str .= "\n</div>";

    return($str);
  }

  // Render the newspaper
  function render($newsPaperId, $archiveDate = '') {
    global $gImagesUserUrl;
    global $gTemplateUrl;
    global $gNewsUrl;
    global $gJSNoStatus;
    global $gIsPhoneClient;

    $this->loadLanguageTexts();

    $str = '';

    $str .= "\n<div class='newspaper'>";

    $str .= $this->renderNewsPaper($newsPaperId, $archiveDate);

    $newsPaper = $this->selectById($newsPaperId);

    $newsPaperId = $newsPaper->getId();

    if (!$gIsPhoneClient) {
      $str .= "\n<div class='newspaper_icons'>";

      $str .= ' ' . $this->popupUtils->getDialogPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_PRINTER . "' class='no_style_image_icon' title='" .  $this->websiteText[3] . "' alt='' />", "$gNewsUrl/newsPaper/print.php?newsPaperId=$newsPaperId", 600, 600);

      $str .= ' ' . $this->popupUtils->getDialogPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_EMAIL_FRIEND . "' class='no_style_image_icon' title='" . $this->websiteText[4] . "' alt='' />", "$gNewsUrl/newsPaper/send.php?newsPaperId=$newsPaperId", 600, 600);

      $str .= "\n</div>";
    }

    if ($this->withArchive($newsPaper)) {
      $str .= "\n<div><a href='$gNewsUrl/newsPaper/display_archives.php?newsPaperId=$newsPaperId'>"
        . $this->websiteText[11]
        . "</a></div>";
    }

    if ($this->withOthers($newsPaper)) {
      $newsPublicationId = $newsPaper->getNewsPublicationId();
      $str .= '<br />' . $this->websiteText[9] . '<br />';
      if ($newsPapers = $this->selectByNewsPublicationIdAndPublish($newsPublicationId)) {
        foreach ($newsPapers as $newsPaper) {
          $newsPaperId = $newsPaper->getId();
          $title = $newsPaper->getTitle();
          $strName = "<a href='$gNewsUrl/newsPaper/display.php?newsPaperId=$newsPaperId'>$title</a>";
          $str .= "\n<div class='newspaper_list_name'>$strName</div>";
        }
      }
    }

    $str .= "\n</div>";

    return($str);
  }

  // Check if an image is centered above the headline
  function imageIsAboveHeadline($newsPaperId) {
    $isAligned = false;

    if ($newsPaper = $this->selectById($newsPaperId)) {
      $newsPublicationId = $newsPaper->getNewsPublicationId();
      $isAligned = $this->newsPublicationUtils->imageIsAboveHeadline($newsPublicationId);
    }

    return($isAligned);
  }

  // Check if an image is aligned left or right in the headline
  function imageIsInHeadlineCorner($newsPaperId) {
    $isAligned = false;

    if ($newsPaper = $this->selectById($newsPaperId)) {
      $newsPublicationId = $newsPaper->getNewsPublicationId();
      $isAligned = $this->newsPublicationUtils->imageIsInHeadlineCorner($newsPublicationId);
    }

    return($isAligned);
  }

  // Check if an image is aligned left or right in the excerpt
  function imageIsInExcerptCorner($newsPaperId) {
    $isAligned = false;

    if ($newsPaper = $this->selectById($newsPaperId)) {
      $newsPublicationId = $newsPaper->getNewsPublicationId();
      $isAligned = $this->newsPublicationUtils->imageIsInExcerptCorner($newsPublicationId);
    }

    return($isAligned);
  }

  // Check if an image is centered above the excerpt
  function imageIsAboveExcerpt($newsPaperId) {
    $isAligned = false;

    if ($newsPaper = $this->selectById($newsPaperId)) {
      $newsPublicationId = $newsPaper->getNewsPublicationId();
      $isAligned = $this->newsPublicationUtils->imageIsAboveExcerpt($newsPublicationId);
    }

    return($isAligned);
  }

  // Get the number of columns in which to display the newspaper
  function getNbColumns($newsPaperId) {
    global $gIsPhoneClient;

    $nbColumns = 1;

    if (!$gIsPhoneClient) {
      if ($newsPaper = $this->selectById($newsPaperId)) {
        $newsPublicationId = $newsPaper->getNewsPublicationId();
        $nbColumns = $this->newsPublicationUtils->getNbColumns($newsPublicationId);
      }
    }

    return($nbColumns);
  }

  // Get the alignment of the image in the excerpt
  function getImageAlignment($newsPaperId) {
    $align = '';

    if ($newsPaper = $this->selectById($newsPaperId)) {
      $newsPublicationId = $newsPaper->getNewsPublicationId();
      $align = $this->newsPublicationUtils->getImageAlignment($newsPublicationId);
    }

    return($align);
  }

  // Render the first image of the news story
  function renderFirstImage($newsStory) {
    global $gNewsUrl;
    global $gUtilsUrl;
    global $gJSNoStatus;

    $str = '';

    $image = '';
    $newsStoryId = $newsStory->getId();
    if ($newsStoryImages = $this->newsStoryImageUtils->selectByNewsStoryId($newsStoryId)) {
      if (count($newsStoryImages) > 0) {
        $newsStoryImage = $newsStoryImages[0];
        $image = $newsStoryImage->getImage();
      }
    }

    $imageFilePath = $this->newsStoryImageUtils->imageFilePath;
    $imageFileUrl = $this->newsStoryImageUtils->imageFileUrl;

    if ($image && @file_exists($imageFilePath . $image)) {
      if (LibImage::isImage($image)) {
        $width = $this->preferenceUtils->getValue("NEWS_STORY_IMAGE_SMALL_WIDTH");

        if (!LibImage::isGif($image)) {
          $filename = $imageFilePath . $image;

          $imageLengthIsHeight = $this->newsStoryUtils->imageLengthIsHeight();
          if ($imageLengthIsHeight) {
            $width = LibImage::getWidthFromHeight($filename, $width);
          }

          $filename = urlencode($filename);

          $strUrl = $gUtilsUrl . "/printImage.php?filename=" . $filename . "&amp;width=$width&amp;height=";
        } else {
          $strUrl = "$imageFileUrl/$image";
        }

        $imageAlign = '';
        $strImageAlign = '';
        $newsPaperId = $newsStory->getNewsPaper();
        if ($this->imageIsInHeadlineCorner($newsPaperId) || $this->imageIsInExcerptCorner($newsPaperId)) {
          $imageAlign = $this->getImageAlignment($newsPaperId);
          $strImageAlign = "align='$imageAlign'";
        }

        $strImg = "<img class='newspaper_story_image_file' src='$strUrl' title='' alt='' width='$width' $strImageAlign />";

        $strImg = "<a href='$imageFileUrl/$image' rel='no_style_colorbox' $gJSNoStatus>$strImg</a>";

        if ($imageAlign == 'left' || $imageAlign == 'right') {
          $str = "\n<div class='newspaper_story_image' style='float:$imageAlign;'>" . $strImg . "</div>";
        } else {
          $str = "\n<div class='newspaper_story_image'>" . $strImg . "</div>";
        }
      }
    }

    return($str);
  }

  // Render the news stories for a heading
  function renderNewsHeadingStories($newsPaperId, $newsHeadingId) {
    global $gNewsUrl;

    $newsPaper = $this->selectById($newsPaperId);

    $listIndex = LibEnv::getEnvHttpPOST("listIndex");
    if (LibString::isEmpty($listIndex)) {
      $listIndex = LibEnv::getEnvHttpGET("listIndex");
    }

    $listStep = $this->preferenceUtils->getValue("NEWS_LIST_STEP");

    // Collect the publishable news stories for a newsPaper
    // Sort the news stories by news headings
    $newsStories = $this->newsStoryUtils->selectByNewsHeading($newsHeadingId, $listIndex, $listStep);
    $listNbItems = $this->newsStoryUtils->countFoundRows();

    $str = "\n<div class='newspaper'>";

    $str .= $this->renderContent($newsPaper, $newsStories, true);

    $paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
    $paginationUtils->addHiddenVariable("newsPaperId", $newsPaper->getId());
    $paginationUtils->addHiddenVariable("newsHeadingId", $newsHeadingId);
    $paginationLinks = $paginationUtils->render();
    if ($paginationLinks) {
      $str .= "<div>" . $paginationLinks . "</div>";
    }

    $str .= "\n</div>";

    return($str);
  }

  // Render the newspaper content
  function renderNewsPaper($newsPaperId, $archiveDate = '') {
    $str = '';

    $newsStoryList = $this->collectNewsStories($newsPaperId, $archiveDate);

    $newsPaper = $this->selectById($newsPaperId);

    $title = $newsPaper->getTitle();

    if (!$this->preferenceUtils->getValue("NEWS_HIDE_HEADER")) {
      $str .= "\n<div class='newspaper_title'>" . $title . "</div>";

      $strImage = $this->renderImage($newsPaper);
      if ($strImage) {
        $str .= "\n<div class='newspaper_image'>" . $strImage . "</div>";
      }

      $header = $newsPaper->getHeader();

      if ($header) {
        $str .= "\n<div class='newspaper_header'>" . $header . "</div>";
      }
    }

    $hideHeading = $this->hideHeading($newsPaper);

    $str .= $this->renderContent($newsPaper, $newsStoryList, $hideHeading);

    $footer = $newsPaper->getFooter();

    $str .= "\n<div class='newspaper_footer'>" . $footer . "</div>";

    return($str);
  }

  // Render the newspaper archived content
  function renderNewsPaperArchives($newsPaperId) {
    $str = '';

    $str = "\n<div class='newspaper'>";

    $listIndex = LibEnv::getEnvHttpPOST("listIndex");
    if (LibString::isEmpty($listIndex)) {
      $listIndex = LibEnv::getEnvHttpGET("listIndex");
    }

    $listStep = $this->preferenceUtils->getValue("NEWS_LIST_STEP");

    $archiveDate = $this->clockUtils->getSystemDate();

    $newsStories = $this->newsStoryUtils->selectByNewsPaperAndArchived($newsPaperId, $archiveDate, $listIndex, $listStep);
    $listNbItems = $this->newsStoryUtils->countFoundRows();

    $newsPaper = $this->selectById($newsPaperId);

    $title = $newsPaper->getTitle();

    if (!$this->preferenceUtils->getValue("NEWS_HIDE_HEADER")) {
      $str .= "\n<div class='newspaper_title'>" . $title . "</div>";

      $strImage = $this->renderImage($newsPaper);
      if ($strImage) {
        $str .= "\n<div class='newspaper_image'>" . $strImage . "</div>";
      }

      $header = $newsPaper->getHeader();

      if ($header) {
        $str .= "\n<div class='newspaper_header'>" . $header . "</div>";
      }
    }

    $str .= $this->renderContent($newsPaper, $newsStories, true);

    $paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
    $paginationUtils->addHiddenVariable("newsPaperId", $newsPaperId);
    $paginationUtils->addHiddenVariable("archiveDate", $archiveDate);
    $paginationLinks = $paginationUtils->render();
    if ($paginationLinks) {
      $str .= "<div>" . $paginationLinks . "</div>";
    }

    $footer = $newsPaper->getFooter();

    $str .= "\n<div class='newspaper_footer'>" . $footer . "</div>";

    $str .= "\n</div>";

    return($str);
  }

  // Render the newspaper content
  function renderContent($newsPaper, $newsStoryList, $hideHeading) {
    global $gNewsUrl;
    global $gJSNoStatus;
    global $gTemplateUrl;
    global $gIsPhoneClient;

    $this->loadLanguageTexts();

    $newsPaperId = $newsPaper->getId();

    $nbColumns = $this->getNbColumns($newsPaperId);

    $str = '';

    $str .= $this->colorboxUtils->renderJsColorbox() . $this->colorboxUtils->renderWebsiteColorbox();

    // Get the number of news stories per column
    // Add one per column if there is a remainder
    if ($nbColumns > 1) {
      $columnNewsStoryNb = floor(count($newsStoryList) / $nbColumns);
      if (count($newsStoryList) % $nbColumns) {
        $columnNewsStoryNb++;
      }
    } else {
      $columnNewsStoryNb = count($newsStoryList);
    }

    $tweeterId = $this->profileUtils->getTwitterId();
    $facebookApplicationId = $this->profileUtils->getFacebookApplicationId();

    // Render the news stories in one or several columns
    $str .= "\n<table border='0' width='100%' cellpadding='0' cellspacing='0'>";

    // Set an equal width in all columns
    $str .= '<tr>';
    for ($i = 0; $i < $nbColumns; $i++) {
      $columnWidth = floor(100 / $nbColumns);
      $str .= "<td width='$columnWidth%'></td>";
    }
    $str .= '</tr>';

    $str .= "<tr><td style='vertical-align:top;'><div class='newspaper_column'>";

    // Dish out the news stories in the columns
    $columnNewsStoryIndex = 0;
    $previousNewsHeadingId = '';
    $moreThanOneHeading = false;
    foreach ($newsStoryList as $newsStory) {
      if ($columnNewsStoryIndex == $columnNewsStoryNb) {
        $columnNewsStoryIndex = 0;
        $str .= "</div></td><td style='vertical-align:top;'><div class='newspaper_column'>";
      }
      $columnNewsStoryIndex++;

      $newsStoryId = $newsStory->getId();
      $headline = $newsStory->getHeadline();
      $excerpt = $newsStory->getExcerpt();
      $releaseDate = $newsStory->getReleaseDate();
      $newsHeadingId = $newsStory->getNewsHeading();
      $newsEditorId = $newsStory->getNewsEditor();

      $releaseDate = $this->clockUtils->systemToLocalNumericDate($releaseDate);

      if (!$hideHeading) {
        // Display the heading on a new column
        if ($previousNewsHeadingId && $newsHeadingId != $previousNewsHeadingId) {
          $moreThanOneHeading = true;
        }
        if ($newsHeadingId != $previousNewsHeadingId || $columnNewsStoryIndex == 1) {
          if ($newsHeading = $this->newsHeadingUtils->selectById($newsHeadingId)) {
            if ($previousNewsHeadingId && $this->withByHeading($newsPaper)) {
              $str .= "<a href='$gNewsUrl/newsPaper/display_heading.php?newsPaperId=$newsPaperId&newsHeadingId=$previousNewsHeadingId' $gJSNoStatus>" .  $this->websiteText[1] . ' "' . $previousNewsHeadingName . "'</a>";
            }
            if ($moreThanOneHeading && $this->withByHeading($newsPaper)) {
              $strHeadingName = "<a href='$gNewsUrl/newsPaper/display_heading.php?newsPaperId=$newsPaperId&newsHeadingId=$newsHeadingId' $gJSNoStatus title='" . $this->websiteText[1] . "'>"
                . $newsHeading->getName()
                . "</a>";
            } else {
              $strHeadingName = $newsHeading->getName();
            }
            $str .= "<div class='newspaper_heading' style='clear:both;'>"
              . $this->newsHeadingUtils->renderImage($newsHeadingId)
              . "<div class='newspaper_heading_name'>"
              . $strHeadingName
              . "</div>"
              . "<div class='newspaper_heading_description'>"
              . $newsHeading->getDescription()
              . "</div>"
              . "</div>";
            $previousNewsHeadingName = $newsHeading->getName();
          }
          $previousNewsHeadingId = $newsHeadingId;
        }
      }

      if ($this->newsStoryUtils->hasParagraph($newsStory) && $this->slideDownDisplay($newsPaper)) {
        $strLink = "javascript:void(0);";
        $target = "onclick=\"slideDownDisplay('$newsStoryId'); return false;\"";
      } else if ($this->newsStoryUtils->hasLink($newsStory)) {
        $strLink = $newsStory->getLink();
        $strLink = $this->templateUtils->renderPageUrl($strLink);
        $target = "onclick=\"window.open(this.href, '_blank'); return false;\"";
      } else if ($this->newsStoryUtils->hasParagraph($newsStory)) {
        $strLink = "$gNewsUrl/newsStory/display.php?newsStoryId=$newsStoryId";
        $target = '';
      } else {
        $strLink = '';
        $target = '';
      }

      $str .= "\n<div class='newspaper_story' style='clear:both;'>";

      if ($strLink) {
        $strHeadline = "<a href='$strLink' $target $gJSNoStatus title='" . $this->newsStoryUtils->websiteText[55] . "'>$headline</a>";
      } else {
        $strHeadline = $headline;
      }

      if ($this->imageIsInHeadlineCorner($newsPaperId)) {
        $str .= "\n<div class='newspaper_headline'>"
          . $this->renderFirstImage($newsStory)
          . $strHeadline
          . "</div>";
      } else {
        if ($this->imageIsAboveHeadline($newsPaperId)) {
          $str .= $this->renderFirstImage($newsStory);
        }
        $str .= "\n<div class='newspaper_headline'>$strHeadline</div>";
      }

      if (!$this->preferenceUtils->getValue("NEWS_HIDE_RELEASE")) {
        $str .= "\n<div class='newspaper_release'>$releaseDate</div>";
      }

      $hidePlayer = $this->preferenceUtils->getValue("NEWS_PAPER_HIDE_PLAYER");
      if (!$hidePlayer) {
        $strPlayer = $this->renderPlayer($newsStory);
        if ($strPlayer) {
          $audioDownload = $this->preferenceUtils->getValue("ELEARNING_DISPLAY_AUDIO_DOWNLOAD");
          if ($audioDownload) {
            $strPlayer .= ' ' . $this->newsStoryUtils->renderDownload($newsStory);
          }
          $str .= "\n<div class='newspaper_player'>$strPlayer</div>";
        }
      }

      if ($excerpt && $this->imageIsInExcerptCorner($newsPaperId)) {
        $str .= "\n<div class='newspaper_excerpt'>"
          . $this->renderFirstImage($newsStory);
        if ($strLink) {
          $str .= "<a href='$strLink' $target $gJSNoStatus title='" . $this->newsStoryUtils->websiteText[55] . "'>$excerpt</a>";
        } else {
          $str .= $excerpt;
        }
        $str .= "</div>";
      } else {
        if ($this->imageIsAboveExcerpt($newsPaperId)) {
          $str .= $this->renderFirstImage($newsStory);
        }
        if ($excerpt) {
          $str .= "\n<div class='newspaper_excerpt'>";
          if ($strLink) {
            $str .= "<a href='$strLink' $target $gJSNoStatus title='" . $this->newsStoryUtils->websiteText[55] . "'>$excerpt</a>";
          } else {
            $str .= $excerpt;
          }
          $str .= "</div>";
        }
      }

      if ($strLink) {
        $str .= "<div class='newspaper_read_more'><a href='$strLink' $target $gJSNoStatus>" . $this->websiteText[8] . '</a></div>';
      }

      if (!$this->preferenceUtils->getValue("NEWS_HIDE_SOCIAL_BUTTONS") && !$this->slideDownDisplay($newsPaper) && $this->newsStoryUtils->hasParagraph($newsStory)) {
        $str .= "<div class='newspaper_social_buttons'>";
        $str .= $this->commonUtils->renderSocialNetworksButtons($headline, $strLink);
        $str .= " </div>";
      }

      if (!$this->preferenceUtils->getValue("NEWS_PAPER_HIDE_EDITOR")) {
        $strEditor = $this->newsStoryUtils->renderEditorName($newsStory);
        if ($strEditor) {
          $str .= "\n<div class='newspaper_editor'>"
            . $strEditor
            . "</div>";
        }
      }

      if ($this->slideDownDisplay($newsPaper) && $this->newsStoryUtils->hasParagraph($newsStory)) {
        $str .= "<div id='slide_down_$newsStoryId' style='display:none;'>"
          . $this->newsStoryUtils->renderParagraphs($newsStoryId)
          . "</div>";
      }

      $str .= "\n</div>";
    }

    if ($moreThanOneHeading && $previousNewsHeadingId && $this->withByHeading($newsPaper)) {
      $str .= "<a href='$gNewsUrl/newsPaper/display_heading.php?newsPaperId=$newsPaperId&newsHeadingId=$newsHeadingId' $gJSNoStatus>" .  $this->websiteText[1] . ' "' . $previousNewsHeadingName . '"</a>';
    }

    $str .= '</div></td></tr>';

    $str .= "\n</table>";

    $str .= <<<HEREDOC
<script type="text/javascript">
function slideDownDisplay(newsStoryId) {
  var id = 'slide_down_' + newsStoryId;
  $("#"+id).slideToggle('fast', function() {
    // Animation complete
  });
  void(0);
}
</script>
HEREDOC;

    return($str);
  }

  // Collect the published news stories for a newspaper
  function collectNewsStories($newsPaperId, $archiveDate = '') {
    if (!$archiveDate) {
      $archiveDate = $this->clockUtils->getSystemDate();
    }

    $newsStories = $this->newsStoryUtils->selectByNewsPaperAndPublished($newsPaperId, $archiveDate);

    return($newsStories);
  }

  // Collect the headings for a newspaper
  function collectNewsHeadings($newsPaperId) {
    $newsHeadings = array('' => ' ');

    $previousNewsHeadingId = '';

    $newsStories = $this->collectNewsStories($newsPaperId);
    foreach ($newsStories as $newsStory) {
      $newsHeadingId = $newsStory->getNewsHeading();
      if ($newsHeadingId != $previousNewsHeadingId) {
        if ($newsHeading = $this->newsHeadingUtils->selectById($newsHeadingId)) {
          $newsHeadings[$newsHeadingId] = $newsHeading->getName();
        }
      }
      $previousNewsHeadingId = $newsHeadingId;
    }

    return($newsHeadings);
  }

  // Get the width of the image
  function getImageWidth() {
    global $gIsPhoneClient;

    if ($gIsPhoneClient) {
      $width = $this->preferenceUtils->getValue("NEWS_PAPER_PHONE_IMAGE_WIDTH");
    } else {
      $width = $this->preferenceUtils->getValue("NEWS_PAPER_IMAGE_WIDTH");
    }

    return($width);
  }

  // Render the image
  function renderImage($newsPaper) {
    global $gUtilsUrl;

    if (!$newsPaper) {
      return;
    }

    $image = $newsPaper->getImage();

    $imageFilePath = $this->imagePath;
    $imageFileUrl = $this->imageUrl;

    if ($image && @file_exists($imageFilePath . $image)) {
      $width = $this->getImageWidth();

      $libFlash = new LibFlash();
      if (LibImage::isImage($image)) {
        if ($width && !LibImage::isGif($image)) {
          // Resize the image
          $filename = urlencode($imageFilePath . $image);
          $strUrl = $gUtilsUrl . "/printImage.php?filename=" . $filename
            . "&amp;width=$width&amp;height=";
        } else {
          $strUrl = "$imageFileUrl/$image";
        }

        $str = "<img class='newspaper_image_file' src='$strUrl' title='' alt='' />";
      } else if ($libFlash->isFlashFile("$imageFileUrl/$image")) {
        $str = $libFlash->renderObject("$imageFileUrl/$image");
      }
    } else {
      $str = '';
    }

    return($str);
  }

  // Render the styling elements for the editing of the css style properties
  function renderStylingElementsForList() {
    $str = "<div class='newspaper_list'>The list of newspapers"
      . "<div class='newspaper_list_name'>The name of newpaper</div>"
      . "<div class='newspaper_list_release'>The release date of a newspaper</div>"
      . "</div>";

    return($str);
  }

  // Render the styling elements for the editing of the css style properties
  function renderStylingElements() {
    global $gStylingImage;
    global $gImagesUserUrl;

    $str = "<div class='newspaper'>A newspaper"
      . "<div class='newspaper_title'>The newspaper title</div>"
      . "<div class='newspaper_image'>The image of the newspaper"
      . "<img class='newspaper_image_file' src='$gStylingImage' title='The border of the image of the newspaper' alt='' />"
      . "</div>"
      . "<div class='newspaper_header'>The header of the newspaper</div>"
      . "<div class='newspaper_column'>A column of news stories"
      . "<div class='newspaper_heading'>A heading"
      . "<div class='newspaper_heading_image'>The image of the news heading"
      . "<img class='newspaper_heading_image_file' src='$gStylingImage' title='The border of the image of the news heading' alt='' />"
      . "</div>"
      . "<div class='newspaper_heading_name'>The name of the heading</div>"
      . "<div class='newspaper_heading_description'>The description of the heading</div>"
      . "</div>"
      . "<div class='newspaper_story'>A news story"
      . "<div class='newspaper_headline'>The headline of the news story</div>"
      . "<div class='newspaper_release'>The release date of the news story</div>"
      . "<div class='newspaper_player'>The audio player of the news story</div>"
      . "<div class='newspaper_story_image'>The image of the news story"
      . "<img class='newspaper_story_image_file' src='$gStylingImage' title='The border of the image of the news story' alt='' />"
      . "</div>"
      . "<div class='newspaper_excerpt'>The excerpt of the news story</div>"
      . "<div class='newspaper_read_more'>The link to the news story</div>"
      . "<div class='newspaper_social_buttons'>The social networks buttons</div>"
      . "<div class='newspaper_editor'>The editor of the news story</div>"
      . "</div>"
      . "</div>"
      . "<div class='newspaper_footer'>The footer of the newspaper</div>"
      . "<div class='newspaper_icons'>"
      . "<input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_PRINTER . "' class='no_style_image_icon' title='An icon' />"
      . "</div>"
      . "<div class='newsstory_social_buttons'>The social networks buttons</div>"
      . "</div>";

    return($str);
  }

}

?>
