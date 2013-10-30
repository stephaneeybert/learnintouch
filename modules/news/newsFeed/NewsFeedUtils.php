<?

class NewsFeedUtils extends NewsFeedDB {

  var $websiteText;

  var $imagePath;
  var $imageUrl;
  var $imageSize;

  var $languageUtils;
  var $preferenceUtils;
  var $commonUtils;
  var $profileUtils;
  var $clockUtils;
  var $templateUtils;
  var $newsStoryImageUtils;
  var $newsStoryUtils;
  var $newsPaperUtils;
  var $newsEditorUtils;
  var $fileUploadUtils;

  function NewsFeedUtils() {
    $this->NewsFeedDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imagePath = $gDataPath . 'news/newsFeed/image/';
    $this->imageUrl = $gDataUrl . '/news/newsFeed/image';
    $this->imageSize = 200000;
  }

  function loadLanguageTexts() {
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->imagePath)) {
      if (!is_dir($gDataPath . 'news')) {
        mkdir($gDataPath . 'news');
      }
      if (!is_dir($gDataPath . 'news/newsFeed')) {
        mkdir($gDataPath . 'news/newsFeed');
      }
      mkdir($this->imagePath);
      chmod($this->imagePath, 0755);
    }
  }

  // Add a news feed
  function add() {
    $newsFeed = new NewsFeed();
    $this->insert($newsFeed);
    $newsFeedId = $this->getLastInsertId();

    return($newsFeedId);
  }

  // Duplicate a news feed
  function duplicate($newsFeedId) {
    if ($newsFeed = $this->selectById($newsFeedId)) {
      $this->insert($newsFeed);
      $duplicatedNewsFeedId = $this->getLastInsertId();

      return($duplicatedNewsFeedId);
    }
  }

  // Delete a news feed
  function deleteNewsFeed($newsFeedId) {
    $this->delete($newsFeedId);
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

  // Check if an image is centered above the headline
  function imageIsAboveHeadline($newsFeed) {
    $isAligned = false;

    $imageAlign = $newsFeed->getImageAlign();
    if ($imageAlign == NEWS_ABOVE_HEADLINE) {
      $isAligned = true;
    }

    return($isAligned);
  }

  // Check if an image is aligned left or right in the headline
  function imageIsInHeadlineCorner($newsFeed) {
    $isAligned = false;

    $imageAlign = $newsFeed->getImageAlign();
    if ($imageAlign == NEWS_LEFT_CORNER_HEADLINE || $imageAlign == NEWS_RIGHT_CORNER_HEADLINE || !$imageAlign) {
      $isAligned = true;
    }

    return($isAligned);
  }

  // Check if an image is centered above the excerpt
  function imageIsAboveExcerpt($newsFeed) {
    $isAligned = false;

    $imageAlign = $newsFeed->getImageAlign();
    if ($imageAlign == NEWS_ABOVE_EXCERPT) {
      $isAligned = true;
    }

    return($isAligned);
  }

  // Check if an image is aligned left or right in the excerpt
  function imageIsInExcerptCorner($newsFeed) {
    $isAligned = false;

    $imageAlign = $newsFeed->getImageAlign();
    if ($imageAlign == NEWS_LEFT_CORNER_EXCERPT || $imageAlign == NEWS_RIGHT_CORNER_EXCERPT) {
      $isAligned = true;
    }

    return($isAligned);
  }

  // Get the alignment of the image in the excerpt
  function getImageAlignment($newsFeed) {
    $htmlAlign = '';

    $align = $newsFeed->getImageAlign();
    if ($align == NEWS_LEFT_CORNER_EXCERPT || $align == NEWS_LEFT_CORNER_HEADLINE) {
      $htmlAlign = 'left';
    } else if ($align == NEWS_RIGHT_CORNER_EXCERPT || $align == NEWS_RIGHT_CORNER_HEADLINE) {
      $htmlAlign = 'right';
    }

    return($htmlAlign);
  }

  // Render the first image of the news story
  function renderFirstImage($newsStory, $newsFeed) {
    global $gNewsUrl;
    global $gUtilsUrl;
    global $gJSNoStatus;

    $this->loadLanguageTexts();

    $str = '';

    if (!$newsFeed->getWithImage()) {
      return;
    }

    $image = '';
    $newsStoryId = $newsStory->getId();
    if ($newsStoryImages = $this->newsStoryImageUtils->selectByNewsStoryId($newsStoryId)) {
      if (count($newsStoryImages) > 0) {
        $newsStoryImage = $newsStoryImages[0];
        $image = $newsStoryImage->getImage();
      }
    }

    if ($this->newsStoryUtils->hasLink($newsStory)) {
      $strLink = $newsStory->getLink();
      $strLink = $this->templateUtils->renderPageUrl($strLink);
      $target = "onclick=\"window.open(this.href, '_blank'); return(false);\"";
    } else if ($this->newsStoryUtils->hasParagraph($newsStory)) {
      $strLink = "$gNewsUrl/newsStory/display.php?newsStoryId=$newsStoryId";
      $target = '';
    } else {
      $strLink = '';
      $target = '';
    }

    $imageFilePath = $this->newsStoryImageUtils->imageFilePath;
    $imageFileUrl = $this->newsStoryImageUtils->imageFileUrl;

    if ($image && @file_exists($imageFilePath . $image)) {
      if (LibImage::isImage($image)) {
        $width = $newsFeed->getImageWidth();

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
        if ($this->imageIsInHeadlineCorner($newsFeed) || $this->imageIsInExcerptCorner($newsFeed)) {
          $imageAlign = $this->getImageAlignment($newsFeed);
          $strImageAlign = "align='$imageAlign'";
        }

        $strImg = "<img class='news_feed_story_image_file' src='$strUrl' title='" .  $this->websiteText[55] . "' alt='' width='$width' $strImageAlign />";

        if ($strLink) {
          $strImg = "<a href='$strLink' $target $gJSNoStatus>$strImg</a>";
        }

        if ($imageAlign == 'left' || $imageAlign == 'right') {
          $str = "\n<div class='news_feed_story_image' style='float:$imageAlign;'>" . $strImg . "</div>";
        } else {
          $str = "\n<div class='news_feed_story_image'>" . $strImg . "</div>";
        }
      }
    }

    return($str);
  }

  // Render the header
  function renderHeader() {
    global $gNewsUrl;

    $languageCode = $this->languageUtils->getCurrentAdminLanguageCode();
    $code = LibJavaScript::renderJQueryDatepickerLanguageCode($languageCode);

    $str = <<<HEREDOC
<script type="text/javascript">
function toggleEventSearch(newsFeedId) {
  var id = 'event_search_' + newsFeedId;
  $("#"+id).slideToggle('fast', function() {
    // Animation complete
  });
  void(0);
}

function closeEventSearch(newsFeedId) {
  var id = 'event_search_' + newsFeedId;
  $("#"+id).slideUp('fast', function() {
    // Animation complete
  });
}

function submitOptionsSearch(newsFeedId, newsPaperId, displayNone, searchDisplayAsPage) {
  var formId = 'form_event_search_' + newsFeedId;
  if (('form_event_search_'+newsFeedId) in document.forms) {
    var newsHeadingId = document.forms['form_event_search_'+newsFeedId].elements['newsHeadingId'].value;
  }
  var eventStartDate = document.forms['form_event_search_'+newsFeedId].elements['eventStartDate'].value;
  var eventEndDate = document.forms['form_event_search_'+newsFeedId].elements['eventEndDate'].value;
  if (newsHeadingId == undefined) { newsHeadingId = ''; }
  if (eventStartDate == undefined) { eventStartDate = ''; }
  if (eventEndDate == undefined) { eventEndDate = ''; }
  var buttons = document.getElementById(formId).elements;
  var period = '';
  for (i = 0; i < buttons.length; i++) {
    if (buttons[i].name.indexOf('eventSearchPeriod', 0) != -1) {
      var button = buttons[i];
      if (button.checked) {
        period = encodeURIComponent(button.value);
      }
    }
  }
  var eventStartDate = encodeURIComponent(eventStartDate);
  var eventEndDate = encodeURIComponent(eventEndDate);
  if (searchDisplayAsPage == 1) {
    var url = '$gNewsUrl/newsPaper/display_selection.php?newsFeedId='+newsFeedId+'&newsPaperId='+newsPaperId+'&newsHeadingId='+newsHeadingId+'&eventStartDate='+eventStartDate+'&eventEndDate='+eventEndDate+'&period='+period;
    window.location = url;
  } else {
    var url = '$gNewsUrl/newsPaper/get_searched_events.php?newsFeedId='+newsFeedId+'&newsPaperId='+newsPaperId+'&newsHeadingId='+newsHeadingId+'&eventStartDate='+eventStartDate+'&eventEndDate='+eventEndDate+'&period='+period;
    ajaxAsynchronousRequest(url, updateNewsFeedEvents);
    if (displayNone) {
      closeEventSearch(newsFeedId);
    }
  }
}

function submitCalendarSearch(newsFeedId, newsPaperId, displayNone, searchDisplayAsPage, selectedDate) {
  var eventStartDate = encodeURIComponent(selectedDate);
  var eventEndDate = encodeURIComponent(selectedDate);
  if (searchDisplayAsPage == 1) {
    var url = '$gNewsUrl/newsPaper/display_selection.php?newsFeedId='+newsFeedId+'&newsPaperId='+newsPaperId+'&eventStartDate='+eventStartDate+'&eventEndDate='+eventEndDate;
    window.location = url;
  } else {
    var url = '$gNewsUrl/newsPaper/get_searched_events.php?newsFeedId='+newsFeedId+'&newsPaperId='+newsPaperId+'&eventStartDate='+eventStartDate+'&eventEndDate='+eventEndDate;
    ajaxAsynchronousRequest(url, updateNewsFeedEvents);
    if (displayNone) {
      closeEventSearch(newsFeedId);
    }
  }
}

function updateNewsFeedEvents(responseText) {
  var response = eval('(' + responseText + ')');
  var newsFeedId = response.newsFeedId;
  var message = response.message;
  var content = response.content;
  var periodLabel = response.periodLabel;
  var formId = 'form_event_search_' + newsFeedId;
  var contentId = 'news_feed_' + newsFeedId;
  $('#news_feed_'+newsFeedId).find("#news_feed_event_period").html(periodLabel);
  $('#news_feed_'+newsFeedId).find("#news_feed_event_message").html(message);
  $('#news_feed_'+newsFeedId).children('.news_feed_newsstories').children('.news_feed_newsstory').remove();
  $('#news_feed_'+newsFeedId).children('.news_feed_newsstories').append(content);
}

$(function() {
  // Set all datepickers in the specified language
  $.datepicker.setDefaults($.datepicker.regional['$code']);

// TODO
  // Date pickers for the options search fields
//  $(".no_style_date_field").datepicker({
//  });
});
</script>

HEREDOC;

    if ($this->clockUtils->isUSDateFormat()) {
      $str .= <<<HEREDOC
<script type='text/javascript'>
$(function() {
// TODO
//  $(".no_style_date_field").datepicker({
//    dateFormat:'mm/dd/yy'
//  });
});
</script>
HEREDOC;
    } else {
      $str .= <<<HEREDOC
<script type='text/javascript'>
$(function() {
// TODO
//  $(".no_style_date_field").datepicker({
//    dateFormat:'dd-mm-yy'
//  });
});
</script>
HEREDOC;
    }

    return($str);
  }

  // Render the news feed
  function render($newsFeedId) {
    global $gNewsUrl;
    global $gJsUrl;
    global $gCommonImagesUrl;
    global $gImagesUserUrl;
    global $gImageRSS;

    $this->loadLanguageTexts();

    $str = '';

    if ($newsFeed = $this->selectById($newsFeedId)) {
      $newsPaperId = $newsFeed->getNewsPaperId();
      if ($newsPaper = $this->newsPaperUtils->selectById($newsPaperId)) {
        $str = "<div id='news_feed_$newsFeedId' class='news_feed'>";

        $str .= "<div class='news_feed_title'>" . $newsPaper->getTitle() . "</div>";

        $image = $newsFeed->getImage();
        if ($image) {
          $str .= "\n<div class='news_feed_image'>" 
            . "<img class='news_feed_image_file' src='$this->imageUrl/$image' title='' alt='' />"
            . "</div>";
        }

        $searchTitle = $newsFeed->getSearchTitle();
        $searchOptions = $newsFeed->getSearchOptions();
        $searchCalendar = $newsFeed->getSearchCalendar();
        $displayUpcoming = $newsFeed->getDisplayUpcoming();
        $searchDisplayAsPage = $newsFeed->getSearchDisplayAsPage();

        $newsStories = array();

        if ($searchOptions || $searchCalendar) {
          $str .= "<div class='news_feed_event'>";
          $str .= "<div class='news_feed_event_title'>";
          if ($searchTitle) {
            $str .= "<a href='javascript:void(0);' onclick=\"toggleEventSearch('$newsFeedId'); return false;\" class='no_style_image_icon'>$searchTitle</a>";
            $strDisplayNone = 'display:none;';
            $displayNone = true;
          } else {
            $strDisplayNone = '';
            $displayNone = false;
          }
          $str .= "</div>";
          $str .= "<div id='event_search_$newsFeedId' style='$strDisplayNone'>";
          if ($searchCalendar) {
            $str .= "<div id='news_feed_inline_datepicker_$newsFeedId' class='news_feed_inline_datepicker'></div>";

            $calendarNewsStories = $this->newsPaperUtils->collectNewsStoriesForEventsForNext360Days($newsPaperId);
//            $calendarNewsStories = $this->newsPaperUtils->collectNewsStoriesForEventsForThisMonth($newsPaperId);
            $comingEventsDates = '[';
            foreach ($calendarNewsStories as $newsStory) {
              $eventStartDate = $newsStory->getEventStartDate();
              $eventEndDate = $newsStory->getEventEndDate();
              $eventDate = substr($eventStartDate, 0, 10);
              while ($this->clockUtils->systemDateIsGreaterOrEqual($eventEndDate, $eventDate)) {
                if (!strstr($comingEventsDates, $eventDate)) {
                  $comingEventsDates .= "'$eventDate', ";
                }
                $previousEventDate = $eventDate;
                $eventDate = $this->clockUtils->incrementDays($eventDate, 1);
                // Make sure the event date is correctly incremented, otherwise exit the loop
                // The objective is to prevent an infinite loop blocking the http response
                if (!$this->clockUtils->systemDateIsGreaterOrEqual($eventDate, $previousEventDate)) {
                  break;
                }
              }
            }
            $comingEventsDates .= "]";
            $comingEventsDates = str_replace(', ]', ']', $comingEventsDates);

            $label = $this->websiteText[28];

            $str .= <<<HEREDOC
<script type='text/javascript'>
  var comingEventsDates_$newsFeedId = $comingEventsDates;

  function isEventDate_$newsFeedId(date) {
    var year = date.getFullYear().toString();
    var month = (date.getMonth() + 1).toString();
    if (month.length == 1) {
      month = "0" + month;
    }
    var day = date.getDate().toString();
    if (day.length == 1) {
      day = "0" + day;
    }
    var dateAsString = year + "-" + month + "-" + day;
    return $.inArray(dateAsString, comingEventsDates_$newsFeedId) == -1 ? [false] : [true, 'newsFeedId_$newsFeedId ' + dateAsString, '$label'];
  }

  function getMonthEvents_$newsFeedId(year, month) {
    var firstDayOfTheMonth = '';
    month = month + '';
    if (year != undefined && month != undefined) {
      if (month.length == 1) {
        month = "0" + month;
      }
      firstDayOfTheMonth = year + '-' + month + '-' + '01';
      firstDayOfTheMonth = encodeURIComponent(firstDayOfTheMonth);
      var url = '$gNewsUrl/newsPaper/get_month_events.php?newsFeedId=$newsFeedId&newsPaperId=$newsPaperId&eventStartDate='+firstDayOfTheMonth;
      ajaxAsynchronousRequest(url, addEventDates);
    }
  }
 
  function addEventDates(responseText) {
    var response = eval('(' + responseText + ')');
    var eventDates = response.eventDates;
    $.each(eventDates, function(index, eventDate) {
      if ($.inArray(eventDate, comingEventsDates_$newsFeedId) == -1) {
        comingEventsDates_$newsFeedId.push(eventDate);
      }
    });
  }

</script>
HEREDOC;

            if ($this->clockUtils->isUSDateFormat()) {
              $str .= <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $("#news_feed_inline_datepicker_$newsFeedId").datepicker({
    dateFormat:'mm/dd/yy',
    onSelect: function(selectedDate, instance) {
      submitCalendarSearch('$newsFeedId', '$newsPaperId', '$displayNone', '$searchDisplayAsPage', selectedDate);
    },
    beforeShowDay: isEventDate_$newsFeedId,
    onChangeMonthYear: getMonthEvents_$newsFeedId
  });
});
</script>
HEREDOC;
            } else {
              $str .= <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $("#news_feed_inline_datepicker_$newsFeedId").datepicker({
    dateFormat:'dd-mm-yy',
    onSelect: function(selectedDate, instance) {
      submitCalendarSearch('$newsFeedId', '$newsPaperId', '$displayNone', '$searchDisplayAsPage', selectedDate);
    },
    beforeShowDay: isEventDate_$newsFeedId,
    onChangeMonthYear: getMonthEvents_$newsFeedId
  });
/*
An attempt at hovering, but the month name sucks
    $(".ui-state-default").live("mouseenter", function() {
        var day = $(this).text();
    if (day.length == 1) {
      day = "0" + day;
    }
var month = $(this).closest('.ui-datepicker').find('.ui-datepicker-month').text();
var year  = $(this).closest('.ui-datepicker').find('.ui-datepicker-year').text();
var date = year + '-' + month + '-' + day;
        $(this).html("<span style='margin:0px; padding:0px; background-color:red;'>" + day + "</span>");
    });
    $(".ui-state-default").live("mouseleave", function() {
        var day = stripTags($(this).text());
        $(this).html(day);
    });
*/
});
</script>
HEREDOC;
            }
            $str .= <<<HEREDOC
<style type="text/css">
.ui-datepicker-inline {
  width:auto ! important;
}
</style>
HEREDOC;
          }
          if ($searchOptions) {
            $str .= "<form action='' method='post' name='form_event_search_$newsFeedId' id='form_event_search_$newsFeedId'>";
            $newsHeadings = $this->newsPaperUtils->collectNewsHeadings($newsPaperId);
            if (count($newsHeadings) > 2) {
              $str .= "<div class='news_feed_event_label'>" . $this->websiteText[27] . "</div>";
              foreach ($newsHeadings as $newsHeadingId => $newsHeadingName) {
                if ($newsHeadingId) {
                  $str .= "<div class='news_feed_event_radio'><input type='radio' name='newsHeadingId' value='$newsHeadingId' style='vertical-align:middle;'> <span onclick=\"clickAdjacentInputElement(this);\">" . $newsHeadingName . "</span></div>";
                }
              }
            }
            $str .= "<div class='news_feed_event_label'>" . $this->websiteText[20] . "</div>"
            . "<div class='news_feed_event_radio'><input type='radio' name='eventSearchPeriod' value='" . NEWS_EVENT_SEARCH_TODAY . "' style='vertical-align:middle;'> <span onclick=\"clickAdjacentInputElement(this);\">" . $this->websiteText[12] . "</span></div>"
            . "<div class='news_feed_event_radio'><input type='radio' name='eventSearchPeriod' value='" . NEWS_EVENT_SEARCH_TOMORROW . "' style='vertical-align:middle;'> <span onclick=\"clickAdjacentInputElement(this);\">" . $this->websiteText[13] . "</span></div>"
            . "<div class='news_feed_event_radio'><input type='radio' name='eventSearchPeriod' value='" . NEWS_EVENT_SEARCH_THISWEEK . "' style='vertical-align:middle;'> <span onclick=\"clickAdjacentInputElement(this);\">" . $this->websiteText[14] . "</span></div>"
            . "<div class='news_feed_event_radio'><input type='radio' name='eventSearchPeriod' value='" . NEWS_EVENT_SEARCH_NEXTWEEK . "' style='vertical-align:middle;'> <span onclick=\"clickAdjacentInputElement(this);\">" . $this->websiteText[15] . "</span></div>"
            . "<div class='news_feed_event_radio'><input type='radio' name='eventSearchPeriod' value='" . NEWS_EVENT_SEARCH_THISMONTH . "' style='vertical-align:middle;'> <span onclick=\"clickAdjacentInputElement(this);\">" . $this->websiteText[16] . "</span></div>"
            . "<div class='news_feed_event_radio'><input type='radio' name='eventSearchPeriod' value='" . NEWS_EVENT_SEARCH_NEXTMONTH . "' style='vertical-align:middle;'> <span onclick=\"clickAdjacentInputElement(this);\">" . $this->websiteText[17] . "</span></div>"
            . "<div class='news_feed_event_label'>" . $this->websiteText[23] . "</div>"
            . "<div class='news_feed_event_field'>" . $this->websiteText[21] . " <input class='news_feed_event_field_input no_style_date_field' type='text' name='eventStartDate' id='eventStartDate' value='' size='10' maxlength='10'></div>"
            . "<div class='news_feed_event_field'>" . $this->websiteText[22] . " <input class='news_feed_event_field_input no_style_date_field' type='text' name='eventEndDate' id='eventEndDate' value='' size='10' maxlength='10'></div>"
            . "<div>"
            // An input field is required to have the browser submit the form on Enter key press
            // Otherwise a form with more than one input field is not submitted
            . "<input type='submit' value='' style='display:none;' />"
            . "<div class='news_feed_event_search'>" . "<a href='javascript:void(0);' title='" . $this->websiteText[19] . "' onclick=\"submitOptionsSearch('$newsFeedId', '$newsPaperId', '$displayNone', '$searchDisplayAsPage'); return false;\">" . $this->websiteText[18] . "</a></div>"
            . "</div>"
            . "</form>";
          }
          $str .= "</div>" . "</div>";
          if ($displayUpcoming) {
            $newsStories = $this->newsPaperUtils->collectNewsStoriesForEventsForNext30Days($newsPaperId);
          }
        } else {
          $newsStories = $this->newsPaperUtils->collectNewsStories($newsPaperId);
        }

        $str .= "<div id='news_feed_event_period' class='news_feed_event_period'></div>";

        $str .= "<div id='news_feed_event_message' class='news_feed_event_message'></div>";

        $str .= "<div class='news_feed_newsstories'>";

        $str .= $this->renderFeedStories($newsFeed, $newsStories);

        $str .= "\n</div>";

        if (!$this->preferenceUtils->getValue("NEWS_HIDE_RSS")) {
          $url = "$gNewsUrl/newsFeed/displayRSS.php?newsFeedId=" . $newsFeed->getId();
          $label = "<a href='$url' onclick=\"window.open(this.href, '_blank'); return(false);\"><img class='news_feed_image_file' src='$gCommonImagesUrl/$gImageRSS' title='$url' alt='' /></a>";
        } else {
          $label = '';
        }
        $str .= "\n<div class='news_feed_rss'>$label</div>";

        $str .= "\n</div>";
      }
    }

    return($str);
  }

  // Render the news feed stories
  function renderFeedStories($newsFeed, $newsStories) {
    $this->loadLanguageTexts();

    $str = '';

    $newsPaperId = $newsFeed->getNewsPaperId();
    if ($newsPaper = $this->newsPaperUtils->selectById($newsPaperId)) {
      $maxDisplayNumber = $newsFeed->getMaxDisplayNumber();
      if (!$maxDisplayNumber) {
        $maxDisplayNumber = $this->preferenceUtils->getValue("NEWS_FEED_MAX_DISPLAY_NUMBER");
      }

      $nbStories = 1;
      foreach ($newsStories as $newsStory) {
        // Check if the maximum number of news stories has already been reached
        if ($maxDisplayNumber && $nbStories > $maxDisplayNumber) {
          break;
        }
        $nbStories++;

        $newsStoryId = $newsStory->getId();
        $headline = $newsStory->getHeadline();

        $str .= $this->renderFeedStory($newsFeed, $newsPaper, $newsStory);
      }
    }

    return($str);
  }

  // Render a news feed story
  function renderFeedStory($newsFeed, $newsPaper, $newsStory) {
    global $gNewsUrl;
    global $gTemplateUrl;
    global $gJSNoStatus;

    $str = "<div class='news_feed_newsstory'>";

    $newsStoryId = $newsStory->getId();
    $headline = $newsStory->getHeadline();

    $strHeadline = "<a href='$gNewsUrl/newsStory/display.php?newsStoryId=$newsStoryId' $gJSNoStatus>" . $headline . "</a>";

    if ($this->imageIsInHeadlineCorner($newsFeed)) {
      $str .= "<div class='news_feed_headline'>"
        . $this->renderFirstImage($newsStory, $newsFeed)
        . $strHeadline
        . "</div>";
    } else {
      if ($this->imageIsAboveHeadline($newsFeed)) {
        $str .= $this->renderFirstImage($newsStory, $newsFeed);
      }
      $str .= "<div class='news_feed_headline'>" . $strHeadline  ."</div>";
    }

    if ($newsFeed->getWithExcerpt()) {
      $excerpt = $newsStory->getExcerpt();
      if ($excerpt) {
        $strExcerpt = "<a href='$gNewsUrl/newsStory/display.php?newsStoryId=$newsStoryId' $gJSNoStatus>" 
          . $excerpt 
          . "</a>";
        if ($this->imageIsInExcerptCorner($newsFeed)) {
          $str .= "\n<div class='news_feed_excerpt'>"
            . $this->renderFirstImage($newsStory, $newsFeed)
            . $strExcerpt
            . "</div>";
        } else {
          if ($this->imageIsAboveExcerpt($newsFeed)) {
            $str .= $this->renderFirstImage($newsStory, $newsFeed);
          }
          $str .= "\n<div class='news_feed_excerpt'>"
            . $strExcerpt
            . "</div>";
        }
      }
    }

    $hideRelease = $this->preferenceUtils->getValue("NEWS_HIDE_FEED_RELEASE");
    if (!$hideRelease) {
      $releaseDate = $newsPaper->getReleaseDate();
      $releaseDate = $this->clockUtils->systemToLocalNumericDate($releaseDate);
    } else {
      $releaseDate = '';
    }

    $str .= "\n<div class='news_feed_release'>$releaseDate</div>";

    $str .= "<div class='news_feed_read_next'><a href='$gNewsUrl/newsStory/display.php?newsStoryId=$newsStoryId' $gJSNoStatus>" . $this->websiteText[8] . '</a></div>';

    $str .= "</div>";

    return($str);
  }

  // Render an RSS feed
  function renderRSS($newsFeedId) {
    global $gNewsUrl;
    global $gHomeUrl;

    if (!$newsFeedId) {
      return;
    }

    if ($newsFeed = $this->selectById($newsFeedId)) {
      $newsPaperId = $newsFeed->getNewsPaperId();
      $newsPaper = $this->newsPaperUtils->selectById($newsPaperId);
    }

    $websiteName = $this->profileUtils->getProfileValue("website.name");
    $websiteEmail = $this->profileUtils->getProfileValue("website.email");

    $str = '';
    $str .= <<<HEREDOC
<?xml version="1.0" encoding="UTF-8"?>
<rdf:RDF xmlns:rdf='http://www.w3.org/1999/02/22-rdf-syntax-ns#'
xmlns='http://my.netscape.com/rdf/simple/0.9/'
xmlns:dc='http://purl.org/dc/elements/1.1/'>
HEREDOC;

    $str .= "\n<channel>";
    $str .= "\n<title>" . $newsPaper->getTitle() . "</title>";
    $str .= "\n<link>$gNewsUrl/newsFeed/displayRSS.php?newsFeedId=$newsFeedId</link>";
    $str .= "\n<description>The news from $gHomeUrl</description>";
    $str .= "\n</channel>";

    $image = $newsFeed->getImage();
    if ($image) {
      $width = $imageWidth = LibImage::getWidth($this->imagePath . $image);
      $height = $imageHeight = LibImage::getHeight($this->imagePath . $image);

      $str .= "\n<image>";
      $str .= "\n<title>The news from $gHomeUrl</title>";
      $str .= "\n<url>" . $this->imageUrl . '/' . $image . "</url>";
      $str .= "\n<link>$gNewsUrl/newsFeed/displayRSS.php?newsFeedId=$newsFeedId</link>";
      $str .= "\n<width>$width</width>";
      $str .= "\n<height>$height</height>";
      $str .= "\n<description>$websiteName</description>";
      $str .= "\n</image>";
    }

    // For each news heading get the news stories data
    $newsStories = $this->newsPaperUtils->collectNewsStories($newsPaper->getId());

    foreach ($newsStories as $newsStory) {
      $newsStoryId = $newsStory->getId();
      $headline = $newsStory->getHeadline();
      $excerpt = $newsStory->getExcerpt();
      $releaseDate = $newsStory->getReleaseDate();

      // Get the editor name
      $newsEditorId = $newsStory->getNewsEditor();
      if ($newsEditorId) {
        if ($newsEditor = $this->newsEditorUtils->selectById($newsEditorId)) {
          $firstname = $this->newsEditorUtils->getFirstname($newsEditorId);
          $lastname = $this->newsEditorUtils->getLastname($newsEditorId);
          $editorName =  $firstname . ' ' . $lastname;
        }
      } else {
        $editorName = '';
      }

      $url = "$gNewsUrl/newsStory/display.php?newsStoryId=$newsStoryId";

      $str .= "\n<item>";
      $str .= "\n<title>$headline</title>";
      $str .= "\n<description>$excerpt</description>";
      $str .= "\n<link>$url</link>";
      $str .= "\n<dc:subject>$headline</dc:subject>";
      $str .= "\n<dc:creator>$editorName</dc:creator>";
      $str .= "\n<dc:date>$releaseDate</dc:date>";
      $str .= "\n</item>";
    }

    $str .= "\n</rdf:RDF>";

    return($str);
  }

  // Render an image cycle of the news feed
  function renderImageCycleInTemplateElement($newsFeedId) {
    $width = $this->preferenceUtils->getValue("NEWS_FEED_CYCLE_WIDTH_TEMPLATE");

    $str = $this->renderImageCycle($newsFeedId, $width);

    return($str);
  }

  // Render an image cycle of the news feed
  function renderImageCycleInPage($newsFeedId) {
    $width = $this->preferenceUtils->getValue("NEWS_FEED_CYCLE_WIDTH_PAGE");

    $str = $this->renderImageCycle($newsFeedId, $width);

    return($str);
  }

  // Render an image cycle of the news feed
  function renderImageCycle($newsFeedId, $width) {
    global $gNewsUrl;
    global $gJSNoStatus;

    $this->loadLanguageTexts();

    $str = '';

    $items = array();

    $imageFilePath = $this->newsStoryImageUtils->imageFilePath;

    if ($newsFeed = $this->selectById($newsFeedId)) {
      $newsPaperId = $newsFeed->getNewsPaperId();
      $newsPaper = $this->newsPaperUtils->selectById($newsPaperId);
      $newsStories = $this->newsPaperUtils->collectNewsStories($newsPaperId);

      $maxDisplayNumber = $newsFeed->getMaxDisplayNumber();
      if (!$maxDisplayNumber) {
        $maxDisplayNumber = $this->preferenceUtils->getValue("NEWS_FEED_MAX_DISPLAY_NUMBER");
      }

      $nbStories = 1;
      foreach ($newsStories as $newsStory) {
        // Check if the maximum number of news stories has already been reached
        if ($maxDisplayNumber && $nbStories > $maxDisplayNumber) {
          break;
        }
        $nbStories++;

        $strNewsStory = $this->renderFeedStory($newsFeed, $newsPaper, $newsStory);

        $item = "<div class='news_feed_cycle_newsstory'>"
          . $strNewsStory
          . "</div>";

        array_push($items, $item);
      }
    }

    $str = '';

    $image = $newsFeed->getImage();
    if ($image) {
      $str .= "\n<div class='news_feed_image'>" 
        . "<img class='news_feed_image_file' src='$this->imageUrl/$image' title='' alt='' />"
        . "</div>";
    }

    if (count($items) > 0) {
      $timeout = $this->preferenceUtils->getValue("NEWS_FEED_CYCLE_TIMEOUT");

      $str .= "<div class='news_feed_cycle'>"
        . $this->commonUtils->renderImageCycle('news_feed_cycle_' . $newsFeedId, $items, false, $timeout)
        . "</div>";
    }

    return($str);
  }

}

?>
