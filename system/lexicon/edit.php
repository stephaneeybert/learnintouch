<?PHP

require_once("website.php");
require_once($gLexiconPath . "includes.php");
require_once($gAdminPath . "includes.php");

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $lexiconEntryId = LibEnv::getEnvHttpPOST("lexiconEntryId");
  $name = LibEnv::getEnvHttpPOST("name");
  $explanation = LibEnv::getEnvHttpPOST("explanation");
  $image = LibEnv::getEnvHttpPOST("image");

  $name = LibString::cleanString($name);
  $explanation = LibString::cleanString($explanation);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[3]);
  }

  // The explanation or an image is required
  if (!$explanation && !$image) {
    array_push($warnings, $mlText[4]);
  }

  if (count($warnings) == 0) {

    if ($lexiconEntry = $lexiconEntryUtils->selectById($lexiconEntryId)) {
      $lexiconEntry->setName($name);
      $lexiconEntry->setExplanation($explanation);
      $lexiconEntryUtils->update($lexiconEntry);
    } else {
      $lexiconEntry = new LexiconEntry();
      $lexiconEntry->setName($name);
      $lexiconEntry->setExplanation($explanation);
      $lexiconEntryUtils->insert($lexiconEntry);
    }

  }

  $str = LibHtml::urlRedirect("$gLexiconUrl/admin.php");
  printContent($str);
  exit;

} else {

  $lexiconEntryId = LibEnv::getEnvHttpGET("lexiconEntryId");
  $name = LibEnv::getEnvHttpGET("name");

  $currentLanguageCode = $languageUtils->getCurrentLanguageCode();

  $explanation = '';
  $image = '';
  if ($lexiconEntryId) {
    if ($lexiconEntry = $lexiconEntryUtils->selectById($lexiconEntryId)) {
      $name = $lexiconEntry->getName();
      $explanation = $lexiconEntry->getExplanation();
      $image = $lexiconEntry->getImage();
    }
  }

}

if ($lexiconEntryId) {
  $title = $mlText[7];
} else {
  $title = $mlText[0];
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($title, "$gLexiconUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$strImage = '';
if ($image) {
  $imageUrl = $lexiconEntryUtils->imageFileUrl;
  $strImage = "<img src='$imageUrl/$image' border='0' title='' href=''>";
}
$strCommand = "<a href='$gLexiconUrl/image.php?lexiconEntryId=$lexiconEntryId' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[14]'></a>";
$label = $popupUtils->getTipPopup($mlText[12], $mlText[13], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strCommand . ' ' . $strImage);
$panelUtils->addLine();
$strCommand = "<a href='$gLexiconUrl/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageList' title='$mlText[9]'></a>";
$panelUtils->openForm($PHP_SELF);
$label = $popupUtils->getTipPopup($mlText[1], $mlText[5], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' id='name' name='name' value='$name' size='30' maxlength='50'> $strCommand");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[2], $mlText[6], 100, 100);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<textarea id='explanation' name='explanation' cols='28' rows='8'>$explanation</textarea>");
$panelUtils->addLine();
// Do not offer a suggestion when modifying an existing entry
if (!$lexiconEntryId && $lexiconEntryUtils->suggestDefinitions()) {
  $strJsSuggest = $commonUtils->ajaxAutocomplete("$gLexiconUrl/suggest_definitions.php", "name", "explanation");
  $panelUtils->addContent($strJsSuggest);
}
$panelUtils->addLine($panelUtils->addCell($mlText[10], "nbr"), $panelUtils->addCell($mlText[8], ""));
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField("dummy", '');
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('lexiconEntryId', $lexiconEntryId);
$panelUtils->addHiddenField('image', $image);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
