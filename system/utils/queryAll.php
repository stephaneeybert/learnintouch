<?PHP

require_once("website.php");

$adminUtils->checkForStaffLogin();

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $pattern = LibEnv::getEnvHttpPOST("pattern");
  $replace = LibEnv::getEnvHttpPOST("replace");

  if ($pattern || $replace) {

    if ($pattern && $replace) {
      $strStatements = <<<HEREDOC
update dynpage set content = replace(content, '$pattern', '$replace');
update news_story set headline = replace(headline, '$pattern', '$replace');
update news_story set excerpt = replace(excerpt, '$pattern', '$replace');
update news_story set link = replace(link, '$pattern', '$replace');
update news_story_paragraph set body = replace(body, '$pattern', '$replace');
update news_story_paragraph set header = replace(header, '$pattern', '$replace');
update news_story_paragraph set footer = replace(footer, '$pattern', '$replace');
update navbar_item set url = replace(url, '$pattern', '$replace');
update elearning_exercise_page set text = replace(text, '$pattern', '$replace');
update elearning_exercise set text = replace(text, '$pattern', '$replace');
update elearning_lesson_paragraph set header = replace(header, '$pattern', '$replace');
update elearning_lesson_paragraph set body = replace(body, '$pattern', '$replace');
update elearning_lesson_paragraph set footer = replace(footer, '$pattern', '$replace');
update mail set body = replace(body, '$pattern', '$replace');
update mail_history set body = replace(body, '$pattern', '$replace');
update statistics_visit set visitor_referer = replace(visitor_referer, '$pattern', '$replace');
HEREDOC;
    }

  } else {

    $strStatements = LibEnv::getEnvHttpPOST("strStatements");

    if ($strStatements) {

      $dbNames = $sqlToolsUtils->getDatabaseNames();

      $statements = array();
      $allStatements = explode(';', $strStatements);
      foreach ($allStatements as $statement) {
        if (trim($statement)) {
          $statement = LibString::addTraillingChar($statement, ';');
          array_push($statements, $statement);
        }
      }

      $str = "<br>Database statements:<br>";
      foreach ($statements as $statement) {
        $str .= "<b>$statement</b><br>";
      }
      $str .= "<br>";

      foreach ($dbNames as $dbName) {
        $sqlToolsUtils = new SqlToolsUtils($dbName);

        $str .= "Database name: <b>$dbName</b><br>";

        foreach ($statements as $statement) {
          $sqlToolsUtils->performStatement($statement);

          $errorMessage = $sqlToolsUtils->getErrorMessage();
          if ($errorMessage) {
            $str .= "<br>$errorMessage<br><br>";
          }
        }

        // Release the data source
        $sqlToolsUtils->freeDataSource();
      }

    }

  }

} else {

  $strStatements = '';

}

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<textarea id='strStatements' name='strStatements' cols='80' rows='4'>$strStatements</textarea>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type='text' name='pattern' size='30'>");
$panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), "<input type='text' name='replace' size='30'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
