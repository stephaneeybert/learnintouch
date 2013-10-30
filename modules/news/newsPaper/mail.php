<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);
$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $newsPaperId = LibEnv::getEnvHttpPOST("newsPaperId");
  $unsubscribeLink = LibEnv::getEnvHttpPOST("unsubscribeLink");

  $mailId = '';

  if ($newsPaper = $newsPaperUtils->selectById($newsPaperId)) {
    $title = $newsPaper->getTitle();
    $releaseDate = $newsPaper->getReleaseDate();

    $content = $templateUtils->renderDefaultModelCssPageProperties();

    $content .= $newsPaperUtils->render($newsPaperId);

    $releaseDate = $clockUtils->systemToLocalNumericDate($releaseDate);

    // Add a link to read the news paper from the website if too difficult in the mail
    $strLink = "$mlText[4] <a href='$gNewsUrl/newsPaper/display.php?newsPaperId=$newsPaperId' $gJSNoStatus>$mlText[5]</a>";
    $content = $strLink . '<br><br>' . $content . '<br><br>' . $strLink;

    // Add a link to unsubscribe from the mailing list
    if ($unsubscribeLink && !strstr($content, 'SYSTEM_PAGE_USER_UNSUBSCRIBE')) {
      $strLink = "$mlText[7] <a href='$gTemplateUrl/display.php?pageId=SYSTEM_PAGE_USER_UNSUBSCRIBE' $gJSNoStatus>$mlText[5]</a>";
      $content .= '<br><br>' . $strLink;
    }

    $adminId = $adminUtils->getLoggedAdminId();

    // Create a mail with the newspaper content
    $mail = new Mail();
    $mail->setSubject($title . ' ' . $releaseDate);
    $mail->setBody($content);
    $mail->setAdminId($adminId);
    $mailUtils->insert($mail);
    $mailId = $mailUtils->getLastInsertId();
    }

  $str = LibHtml::urlRedirect("$gMailUrl/send.php?mailId=$mailId");
  printContent($str);
  return;

  } else {

  $newsPaperId = LibEnv::getEnvHttpGET("newsPaperId");

  $title = '';
  $releaseDate = '';
  if ($newsPaper = $newsPaperUtils->selectById($newsPaperId)) {
    $title = $newsPaper->getTitle();
    $releaseDate = $newsPaper->getReleaseDate();
    }

  $help = $popupUtils->getHelpPopup($mlText[6], 300, 200);
  $panelUtils->setHelp($help);
  $panelUtils->setHeader($mlText[0], "$gNewsUrl/newsPaper/admin.php");
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $title);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $releaseDate);
  $panelUtils->addLine();
  $label = $popupUtils->getTipPopup($mlText[8], $mlText[9], 300, 300);
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='unsubscribeLink' CHECKED value='1'>");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('newsPaperId', $newsPaperId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
