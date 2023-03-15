<?

class StatisticsPageUtils extends StatisticsPageDB {

  var $templateUtils;
  var $clockUtils;

  function __construct() {
    parent::__construct();
  }

  // Render the page id from a url
  function renderPageId($url) {
    $page = '';
    if (strstr($url, 'engine') && strstr($url, 'pageId') && !strstr($url, '://') && !strstr($url, 'SYSTEM_PAGE_USER_UNSUBSCRIBE')) {
      $parsedUrl = parse_url($url);
      if (isset($parsedUrl['query'])) {
        parse_str($parsedUrl['query'], $parameters);
        if (count($parameters) > 0) {
          $name1 = key($parameters);
          $value1 = $parameters[$name1];
          if ($name1 == 'pageId' && is_numeric($value1)) {
            $page = $value1;
          } else {
            if (count($parameters) > 1) {
              next($parameters);
              $name2 = key($parameters);
              $value2 = $parameters[$name2];
              if (is_numeric($value2)) {
                $page = $value1 . $value2;
              }
            }
          }
        }
      }
    }

    return($page);
  }

  function logPageHit() {
    global $REQUEST_URI;

    $page = $this->renderPageId($REQUEST_URI);

    $page = LibString::stripQuotes($page);

    if (!$page) {
      return;
    }

    if (!$this->templateUtils->isValidPageId($page)) {
      return;
    }

    // Get today's date
    $today = $this->clockUtils->getSystemDate();
    $year = substr($today, 0, 4);
    $month = substr($today, 5, 2);

    if (!$statisticsPage = $this->selectByPageAndYearAndMonth($page, $year, $month)) {
      $statisticsPage = new StatisticsPage();
      $statisticsPage->setHits(1);
      $statisticsPage->setPage($page);
      $statisticsPage->setYear($year);
      $statisticsPage->setMonth($month);
      $this->insert($statisticsPage);
    } else {
      $this->addHit($statisticsPage);
    }
  }

  // Delete the statistics of the previous year
  function deleteOldHits() {
    // Get the system date
    $today = $this->clockUtils->getSystemDate();
    $year = substr($today, 0, 4);

    $this->deleteOldStat($year);
  }

}

?>
