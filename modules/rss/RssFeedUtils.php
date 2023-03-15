<?

class RssFeedUtils extends RssFeedDB {

  var $mlText;

  var $languageUtils;
  var $rssFeedLanguageUtils;

  function __construct() {
    parent::__construct();
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  // Add an rss feed
  function add() {
    $rss = new RssFeed();
    $this->insert($rss);
    $rssFeedId = $this->getLastInsertId();

    return($rssFeedId);
  }

  // Duplicate a rss feed
  function duplicate($rssFeedId) {
    if ($rss = $this->selectById($rssFeedId)) {
      $this->insert($rss);
      $duplicatedRssFeedId = $this->getLastInsertId();

      return($duplicatedRssFeedId);
    }
  }

  // Delete a rss feed
  function deleteRssFeed($rssFeedId) {
    if ($rssFeedLanguages = $this->rssFeedLanguageUtils->selectByRssFeedId($rssFeedId)) {
      foreach ($rssFeedLanguages as $rssFeedLanguage) {
        $this->rssFeedLanguageUtils->delete($rssFeedLanguage->getId());
      }
    }

    $this->delete($rssFeedId);
  }

  // Get the available languages for the RSS feed
  function getAvailableLanguages($rssFeedId, $excludeUsedOnes = false) {
    $this->loadLanguageTexts();

    $languageNames = $this->languageUtils->getActiveLanguageNames();
    $languageNames = array_merge(array('' => $this->mlText[0]), $languageNames);

    // Remove the already used languages
    if ($excludeUsedOnes) {
      if ($rssFeedLanguages = $this->rssFeedLanguageUtils->selectByRssFeedId($rssFeedId)) {
        foreach ($rssFeedLanguages as $rssFeedLanguage) {
          $language = $rssFeedLanguage->getLanguage();
          unset($languageNames[$language]);
        }
      }
    }

    return($languageNames);
  }

  // When creating an element in the template system, it is necessary to create its tags
  // A dummy rendering is used for this operation
  function renderTags() {
    $str = "\n<div class='rss_feed'>The RSS feed"
      . "<div class='rss_feed_title'>The title</div>"
      . "<div class='rss_feed_newsstory'>A news story"
      . "<div class='rss_feed_headline'>The headline of a news story</div>"
      . "</div>"
      . "</div>";

    return($str);
  }

  // Render the rss feed
  function render($rssFeedId) {
    if (!$rssFeed = $this->selectById($rssFeedId)) {
      return;
    }

    $languageCode = $this->languageUtils->getCurrentLanguageCode();

    if (!$rssFeedLanguage = $this->rssFeedLanguageUtils->selectByLanguageAndRssFeedId($languageCode, $rssFeedId)) {
      if (!$rssFeedLanguage = $this->rssFeedLanguageUtils->selectByNoLanguageAndRssFeedId($rssFeedId)) {
        return;
      }
    }

    $rssFeedLanguageId = $rssFeedLanguage->getId();

    $str = $this->rssFeedLanguageUtils->render($rssFeedLanguageId);

    return($str);
  }

}

?>
