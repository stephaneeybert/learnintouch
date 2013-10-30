<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_GUESTBOOK);

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gAdminUrl/menu.php");
$help = $popupUtils->getHelpPopup($mlText[4], 300, 300);
$panelUtils->setHelp($help);
$strCommand = "<a href='$gGuestbookUrl/empty.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>"
  . " <a href='$gGuestbookUrl/preference.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageSetup' title='$mlText[5]'></a>";

$panelUtils->addLine('', $panelUtils->addCell($strCommand, "nr"));
$panelUtils->addLine();

$guestbooks = $guestbookUtils->selectAll();

foreach ($guestbooks as $guestbook) {
  $guestbookId = $guestbook->getId();
  $body = $guestbook->getBody();
  $releaseDate = $guestbook->getReleaseDate();
  $userId = $guestbook->getUserId();
  $email = $guestbook->getEmail();
  $firstname = $guestbook->getFirstname();
  $lastname = $guestbook->getLastname();

  if ($user = $userUtils->selectById($userId)) {
    $firstname = $user->getFirstname();
    $lastname = $user->getLastname();
    $email = $user->getEmail();
    }

  if ($firstname || $lastname) {
    $strName = "$firstname $lastname";
    } else {
    $strName = $email;
    }

  if ($email) {
    $strName = "<a href='mailto:$email'>$strName</a>";
    }

  $strCommand = "<a href='$gGuestbookUrl/edit.php?guestbookId=$guestbookId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[7]'></a>"
    . " <a href='$gGuestbookUrl/delete.php?guestbookId=$guestbookId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[2]'></a>";

  $panelUtils->addLine("<B>$mlText[1]</B> $strName <B>$mlText[6]</B> $releaseDate", $panelUtils->addCell($strCommand, "nr"));
  $panelUtils->addLine($body, '');
  $panelUtils->addLine();
  }

$strRememberScroll = LibJavaScript::rememberScroll("guestbook_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
