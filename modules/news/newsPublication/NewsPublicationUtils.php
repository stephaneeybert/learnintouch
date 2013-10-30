<?

class NewsPublicationUtils extends NewsPublicationDB {

  var $mlText;

  var $languageUtils;
  var $clockUtils;
  var $newsPaperUtils;

  function NewsPublicationUtils() {
    $this->NewsPublicationDB();
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  // Get the list of all news publications
  function getNewsPublicationList($searchPattern = '') {
    $this->loadLanguageTexts();

    $list = array();

    if ($searchPattern) {
      $newsPublications = $this->selectLikePattern($searchPattern);
    } else {
      $newsPublications = $this->selectAll();
    }

    if ($newsPublications) {
      foreach ($newsPublications as $newsPublication) {
        $newsPublicationId = $newsPublication->getId();
        $name = $newsPublication->getName();
        $list['SYSTEM_PAGE_NEWSPUBLICATION' . $newsPublicationId] = $this->mlText[0] . " " . $name . " (" . $this->mlText[1] . ")";
      }
    }

    return($list);
  }

  // Archive the old newspapers
  function archiveOldNewspapers() {
    $systemDate = $this->clockUtils->getSystemDate();
    $newsPublications = $this->selectAll();
    foreach ($newsPublications as $newsPublication) {
      $newsPublicationId = $newsPublication->getId();
      $autoArchive = $newsPublication->getAutoArchive();
      if ($autoArchive && is_numeric($autoArchive)) {
        // Get the date since which to archive the newspapers
        $sinceDate = $this->clockUtils->incrementDays($systemDate, -1 * $autoArchive);
        $this->newsPaperUtils->archiveByReleaseDate($newsPublicationId, $sinceDate, $systemDate);
      }
    }
  }

  // Delete the old newspapers
  function deleteOldNewspapers() {
    $systemDate = $this->clockUtils->getSystemDate();
    $newsPublications = $this->selectAll();
    foreach ($newsPublications as $newsPublication) {
      $newsPublicationId = $newsPublication->getId();
      $autoDelete = $newsPublication->getAutoDelete();
      if ($autoDelete && is_numeric($autoDelete)) {
        // Get the date since which to delete the newspapers
        $sinceDate = $this->clockUtils->incrementDays($systemDate, -1 * $autoDelete);
        if ($newsPapers = $this->newsPaperUtils->selectByPublicationAndArchiveDate($newsPublicationId, $sinceDate)) {
          foreach ($newsPapers as $newsPaper) {
            $newsPaperId = $newsPaper->getId();
            $this->newsPaperUtils->deleteNewsPaper($newsPaperId, true);
          }
        }
      }
    }
  }

  // Get the number of columns in which to display the newspaper
  function getNbColumns($newsPublicationId) {
    $nbColumns = 1;

    if ($newsPublication = $this->selectById($newsPublicationId)) {
      $nbColumns = $newsPublication->getNbColumns();
    }

    return($nbColumns);
  }

  // Check if an image is centered above the headline
  function imageIsAboveHeadline($newsPublicationId) {
    $isAligned = false;

    if ($newsPublication = $this->selectById($newsPublicationId)) {
      $imageAlign = $newsPublication->getAlign();
      if ($imageAlign == NEWS_ABOVE_HEADLINE) {
        $isAligned = true;
      }
    }

    return($isAligned);
  }

  // Check if an image is aligned left or right in the headline
  function imageIsInHeadlineCorner($newsPublicationId) {
    $isAligned = false;

    if ($newsPublication = $this->selectById($newsPublicationId)) {
      $imageAlign = $newsPublication->getAlign();
      if ($imageAlign == NEWS_LEFT_CORNER_HEADLINE || $imageAlign == NEWS_RIGHT_CORNER_HEADLINE || !$imageAlign) {
        $isAligned = true;
      }
    }

    return($isAligned);
  }

  // Check if an image is centered above the excerpt
  function imageIsAboveExcerpt($newsPublicationId) {
    $isAligned = false;

    if ($newsPublication = $this->selectById($newsPublicationId)) {
      $imageAlign = $newsPublication->getAlign();
      if ($imageAlign == NEWS_ABOVE_EXCERPT) {
        $isAligned = true;
      }
    }

    return($isAligned);
  }

  // Check if an image is aligned left or right in the excerpt
  function imageIsInExcerptCorner($newsPublicationId) {
    $isAligned = false;

    if ($newsPublication = $this->selectById($newsPublicationId)) {
      $imageAlign = $newsPublication->getAlign();
      if ($imageAlign == NEWS_LEFT_CORNER_EXCERPT || $imageAlign == NEWS_RIGHT_CORNER_EXCERPT) {
        $isAligned = true;
      }
    }

    return($isAligned);
  }

  // Get the alignment of the image in the excerpt
  function getImageAlignment($newsPublicationId) {
    $htmlAlign = '';

    if ($newsPublication = $this->selectById($newsPublicationId)) {
      $align = $newsPublication->getAlign();
      if ($align == NEWS_LEFT_CORNER_EXCERPT || $align == NEWS_LEFT_CORNER_HEADLINE) {
        $htmlAlign = 'left';
      } else if ($align == NEWS_RIGHT_CORNER_EXCERPT || $align == NEWS_RIGHT_CORNER_HEADLINE) {
        $htmlAlign = 'right';
      }
    }

    return($htmlAlign);
  }

  // Check if a news publication is secured
  function isSecured($newsPublicationId) {
    $secured = false;

    if ($newsPublication = $this->selectById($newsPublicationId)) {
      $secured = $newsPublication->getSecured();
    }

    return($secured);
  }

  // Render the list of news publications
  function renderList() {
    global $gNewsUrl;
    global $gIsPhoneClient;

    $str = '';

    $str .= "\n<div class='newspublication_list'>";

    $str .= "\n<table border='0' width='100%' cellspacing='0' cellpadding='0'>";

    if ($newsPublications = $this->selectAll()) {
      foreach ($newsPublications as $newsPublication) {
        $newsPublicationId = $newsPublication->getId();
        $name = $newsPublication->getName();
        $description = $newsPublication->getDescription();

        $strName = "<a href='$gNewsUrl/newsPublication/display.php?newsPublicationId=$newsPublicationId'>$name</a>";

        $str .= "\n<tr>";
        $str .= "\n<td><div class='newspublication_list_name'>$strName</div>";
        $str .= "</td><td>";
        $str .= "\n<div class='newspublication_list_description'>$description</div></td>";
        $str .= "\n</tr>";
      }
    }

    $str .= "\n</table>";

    $str .= "\n</div>";

    return($str);
  }

  // Render a news publication
  function renderNewsPublication($newsPublicationId = '') {
    $str = '';

    // If no news publication is specified then get the first news publication
    if (!$newsPublicationId) {
      if ($newsPublications = $this->selectAll()) {
        if (count($newsPublications) > 0) {
          $newsPublication = $newsPublications[0];
          $newsPublicationId = $newsPublication->getId();
        }
      }
    }

    // Render the last newspaper of the news publication
    if ($newsPaperId = $this->newsPaperUtils->getLastPublishedNewsPaperId($newsPublicationId)) {
      $str = $this->newsPaperUtils->render($newsPaperId);
    }

    return($str);
  }

  // Print the news publication
  function printNewsPublication($newsPublicationId) {
    $str = '';

    // Print the last newspaper of the news publication
    if ($newsPaperId = $this->newsPaperUtils->getLastPublishedNewsPaperId($newsPublicationId)) {
      $str = $this->newsPaperUtils->printNewsPaper($newsPaperId);
    }

    return($str);
  }

  // Render the styling elements for the editing of the css style properties
  function renderStylingElements() {
    $str = "<div class='newspublication_list'>The list of publications"
      . "<div class='newspublication_list_name'>The name of a publication</div>"
      . "<div class='newspublication_list_description'>The description of a publication</div>"
      . "</div>";

    return($str);
  }

}

?>
