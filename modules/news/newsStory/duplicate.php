<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $newsStoryId = LibEnv::getEnvHttpPOST("newsStoryId");
  $headline = LibEnv::getEnvHttpPOST("headline");

  $headline = LibString::cleanString($headline);

  // Duplicate the newsStory
  $lastNewsStoryId = $newsStoryUtils->duplicate($newsStoryId, '', $headline);
  if ($lastNewsStoryId > 0) {
    $str = LibHtml::urlRedirect("$gNewsUrl/newsStory/edit.php?newsStoryId=$lastNewsStoryId");
    printContent($str);
    return;
    }

  $str = LibHtml::urlRedirect("$gNewsUrl/newsStory/admin.php");
  printContent($str);
  return;

  } else {

  $newsStoryId = LibEnv::getEnvHttpGET("newsStoryId");

  $headline = '';
  $excerpt = '';
  if ($newsStoryId) {
    if ($newsStory = $newsStoryUtils->selectById($newsStoryId)) {
      $randomNumber = LibUtils::generateUniqueId();
      $headline = $newsStory->getHeadline() . NEWS_DUPLICATA . '_' . $randomNumber;
      $excerpt = $newsStory->getExcerpt();
      }
    }

  $panelUtils->setHeader($mlText[0], "$gNewsUrl/newsStory/admin.php");
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<input type='text' name='headline' value='$headline' size='30' maxlength='255'>");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $excerpt);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[7], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('newsStoryId', $newsStoryId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
