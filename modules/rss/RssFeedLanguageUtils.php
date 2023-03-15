<?

class RssFeedLanguageUtils extends RssFeedLanguageDB {

  function __construct() {
    parent::__construct();
  }

  // Add a rss feed
  function add() {
    $rss = new RssFeedLanguage();
    $this->insert($rss);
    $rssFeedLanguageId = $this->getLastInsertId();

    return($rssFeedLanguageId);
  }

  // Duplicate a rss feed
  function duplicate($rssFeedLanguageId) {
    if ($rss = $this->selectById($rssFeedLanguageId)) {
      $this->insert($rss);
      $duplicatedRssFeedId = $this->getLastInsertId();

      return($duplicatedRssFeedId);
    }
  }

  // Delete a rss feed
  function deleteRssFeed($rssFeedLanguageId) {
    $this->delete($rssFeedLanguageId);
  }

  // Render the rss feed
  function render($rssFeedLanguageId) {
    global $gCommonImagesUrl;
    global $gImageRSS;
    global $gRssUrl;
    global $gJSNoStatus;
    global $gTemplateUrl;

    if (!$rss = $this->selectById($rssFeedLanguageId)) {
      return;
    }

    $url = $rss->getUrl();
    $title = $rss->getTitle();

    $str = '';

    if ($url) {
      $str = $this->parseRSS($url, $title);
    }

    return($str);
  }

  // Parse the RSS news feed
  function parseRSS($rssUrl, $title) {
    global $gJSNoStatus;

    $str = '';

    $xmlDocument = new DOMDocument("1.0", "UTF-8");
    $xmlDocument->load($rssUrl);
    $channelNode = $xmlDocument->getElementsByTagName('channel')->item(0);
    if ($channelNode && !$title) {
      $title = $channelNode->getElementsByTagName('title')->item(0)->nodeValue;
    }

    $xmlNodes = $xmlDocument->getElementsByTagName('item');

    if (count($xmlNodes) > 0) {
      $str .= "\n<div class='rss_feed'>";
      $str .= "<div class='rss_feed_title'>" . $title . "</div>";
      foreach ($xmlNodes as $xmlNode) {
        $title = $xmlNode->getElementsByTagName('title')->item(0)->nodeValue;
        $link = $xmlNode->getElementsByTagName('link')->item(0)->nodeValue;
//        $description = $xmlNode->getElementsByTagName('description')->item(0)->nodeValue;
//        $guid = $xmlNode->getElementsByTagName('guid')->item(0)->nodeValue;
//        $date = $xmlNode->getElementsByTagName('date')->item(0)->nodeValue;

        $str .= "<div class='rss_feed_newsstory'>"
          . "<div class='rss_feed_headline'>"
          . "<a href='$link' $gJSNoStatus>$title</a>"
          . "</div>"
          . "</div>";
      }
      $str .= "</div>";
    }

    return($str);
  }

}

?>
