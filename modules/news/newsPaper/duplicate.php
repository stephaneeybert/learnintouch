<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $newsPaperId = LibEnv::getEnvHttpPOST("newsPaperId");
  $title = LibEnv::getEnvHttpPOST("title");

  $title = LibString::cleanString($title);

  $releaseDate = $clockUtils->getSystemDate();

  // Duplicate the newsPaper
  if ($newsPaper = $newsPaperUtils->selectById($newsPaperId)) {
    $newsPaper->setTitle($title);
    $newsPaper->setImage($newsPaper->getImage());
    $newsPaper->setHeader($newsPaper->getHeader());
    $newsPaper->setFooter($newsPaper->getFooter());
    $newsPaper->setReleaseDate($releaseDate);
    $newsPaperUtils->insert($newsPaper);
    $lastNewsPaperId = $newsPaperUtils->getLastInsertId();

    // Duplicate the news stories
    $newsStories = $newsStoryUtils->selectByNewsPaper($newsPaperId);
    foreach ($newsStories as $newsStory) {
      $newsStoryId = $newsStory->getId();
      $newsStoryUtils->duplicate($newsStoryId, $lastNewsPaperId, '');
    }

    $str = LibHtml::urlRedirect("$gNewsUrl/newsPaper/edit.php?newsPaperId=$lastNewsPaperId");
    printContent($str);
    return;
  }

  $str = LibHtml::urlRedirect("$gNewsUrl/newsPaper/admin.php");
  printContent($str);
  return;

} else {

  $newsPaperId = LibEnv::getEnvHttpGET("newsPaperId");

  $title = '';
  $excerpt = '';
  if ($newsPaperId) {
    if ($newsPaper = $newsPaperUtils->selectById($newsPaperId)) {
      $randomNumber = LibUtils::generateUniqueId();
      $title = $newsPaper->getTitle() . NEWS_DUPLICATA . '_' . $randomNumber;
      $header = $newsPaper->getHeader();
      $footer = $newsPaper->getFooter();
    }
  }

  $panelUtils->setHeader($mlText[0], "$gNewsUrl/newsPaper/admin.php");
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<input type='text' name='title' value='$title' size='30' maxlength='255'>");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $header);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $footer);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[7], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('newsPaperId', $newsPaperId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
