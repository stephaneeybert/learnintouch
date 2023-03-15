<?

class NewsStoryParagraphUtils extends NewsStoryParagraphDB {

  var $newsPaperUtils;
  var $newsStoryUtils;

  function NewsStoryParagraphUtils() {
    parent::__construct();
  }

  // Add a paragraph to a news story
  function add($newsStoryId, $body = '') {
    $newsStoryParagraph = new NewsStoryParagraph();
    $newsStoryParagraph->setBody($body);
    $newsStoryParagraph->setNewsStoryId($newsStoryId);
    $this->insert($newsStoryParagraph);
  }

  // Delete a paragraph from a news story
  function deleteParagraph($newsStoryParagraphId) {
    global $gNewsUrl;

    // Delete the references from the search engine
    if ($newsStoryParagraph = $this->selectById($newsStoryParagraphId)) {
      $newsStoryId = $newsStoryParagraph->getNewsStoryId();
    }

    $this->delete($newsStoryParagraphId);
  }

  // Get the first paragraph of a news story
  // Otherwise get the first paragraph of the first news story of the first published newspaper
  function getFirstParagraph($newsStoryId = '') {
    $newsStoryParagraphId = '';

    if (!$newsStoryId) {
      if ($newsPapers = $this->newsPaperUtils->selectPublished()) {
        if (count($newsPapers) > 0) {
          $newsPaper = $newsPapers[0];
          $newsPaperId = $newsPaper->getId();

          if ($newsStories = $this->newsStoryUtils->selectByNewsPaper($newsPaperId)) {
            if (count($newsStories) > 0) {
              $newsStory = $newsStories[0];
              $newsStoryId = $newsStory->getId();
            }
          }
        }
      }
    }

    if ($newsStoryParagraphs = $this->selectByNewsStoryId($newsStoryId)) {
      if (count($newsStoryParagraphs) > 0) {
        $newsStoryParagraph = $newsStoryParagraphs[0];
        $newsStoryParagraphId = $newsStoryParagraph->getId();
      }
    }

    return($newsStoryParagraphId);
  }

  // Get the previous paragraph
  function getPreviousParagraphId($newsStoryParagraphId) {
    $previousParagraphId = '';

    if ($newsStoryParagraph = $this->selectById($newsStoryParagraphId)) {
      $newsStoryId = $newsStoryParagraph->getNewsStoryId();

      if ($newsStoryParagraphs = $this->selectByNewsStoryId($newsStoryId)) {
        foreach ($newsStoryParagraphs as $newsStoryParagraph) {
          $wNewsStoryParagraphId = $newsStoryParagraph->getId();
          if ($wNewsStoryParagraphId == $newsStoryParagraphId) {
            return($previousParagraphId);
          }
          $previousParagraphId = $wNewsStoryParagraphId;
        }
      }
    }
  }

  // Get the next paragraph
  function getNextParagraphId($newsStoryParagraphId) {
    $previousParagraphId = '';

    if ($newsStoryParagraph = $this->selectById($newsStoryParagraphId)) {
      $newsStoryId = $newsStoryParagraph->getNewsStoryId();

      if ($newsStoryParagraphs = $this->selectByNewsStoryId($newsStoryId)) {
        foreach ($newsStoryParagraphs as $newsStoryParagraph) {
          $wNewsStoryParagraphId = $newsStoryParagraph->getId();
          if ($previousParagraphId == $newsStoryParagraphId) {
            return($wNewsStoryParagraphId);
          }
          $previousParagraphId = $wNewsStoryParagraphId;
        }
      }
    }
  }

}

?>
