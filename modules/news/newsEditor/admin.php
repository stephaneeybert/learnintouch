<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);

$panelUtils->setHeader($mlText[0], "$gNewsUrl/newsStory/admin.php");
$strCommand = "<a href='$gNewsUrl/newsEditor/edit.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine("<B>$mlText[8]</B>", "<B>$mlText[9]</B>", $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$newsEditors = $newsEditorUtils->selectAll();

$panelUtils->openList();
foreach ($newsEditors as $newsEditor) {
  $newsEditorId = $newsEditor->getId();
  $firstname = $newsEditorUtils->getFirstname($newsEditorId);
  $lastname = $newsEditorUtils->getLastname($newsEditorId);
  $email = $newsEditorUtils->getEmail($newsEditorId);

  $strName = $firstname . ' ' . $lastname;

  $strCommand = " <a href='$gNewsUrl/newsEditor/delete.php?newsEditorId=$newsEditorId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($panelUtils->addCell("$strName", "n"), $panelUtils->addCell($email, "n"), $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
