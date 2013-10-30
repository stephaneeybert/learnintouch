<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DYNPAGE);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $dynpageId = LibEnv::getEnvHttpPOST("dynpageId");

  $content = LibEnv::getEnvHttpPOST("content");

  $content = LibString::cleanHtmlString($content);

  // Fix the double escaping of & if any
  if (strstr($content, "&amp;amp;")) {
    $content = str_replace("&amp;amp;", "&amp;", $content);
  }

  if ($dynpage = $dynpageUtils->selectById($dynpageId)) {
    $dynpage->setContent($content);
    $dynpageUtils->update($dynpage);
  }

  $str = LibHtml::urlRedirect("$gDynpageUrl/admin.php");
  printContent($str);
  exit;

} else {

  $dynpageId = LibEnv::getEnvHttpGET("dynpageId");

  $name = '';
  $content = '';
  if ($dynpage = $dynpageUtils->selectById($dynpageId)) {
    $name = $dynpage->getName();
    $content = $dynpage->getContent();
  }

  $panelUtils->setHeader($mlText[0], "$gDynpageUrl/admin.php");
  $help = $popupUtils->getHelpPopup($mlText[1], 300, 300);
  $panelUtils->setHelp($help);
  $panelUtils->addLine("<b>$mlText[6]</b> $name");
  $panelUtils->openForm($PHP_SELF);

  if ($dynpageUtils->useHtmlEditorInnova()) {
    $oInnovaContentName = "content";
    include($gInnovaHtmlEditorPath . "setupDynpage.php");
    $panelUtils->addContent($gInnovaHead);
    $panelUtils->addLine("<textarea id='$oInnovaContentName' name='$oInnovaContentName' cols='30' rows='80'>\n$content\n</textarea> $gInnovaBodyOpen $gInnovaBodyClose");
  } else {
    include($gHtmlEditorPath . "CKEditorUtils.php");
    $contentEditor = new CKEditorUtils();
    $contentEditor->languageUtils = $languageUtils;
    $contentEditor->commonUtils = $commonUtils;
    $contentEditor->load();
    $contentEditor->setImagePath($dynpageUtils->imagePath);
    $contentEditor->setImageUrl($dynpageUtils->imageUrl);
    $contentEditor->setImageBrowserUploadUrl($gSystemUrl . '/editor/ckeditor/connector/image_dynpage.php');
    $contentEditor->withStandardToolbar();
    $contentEditor->withImageButton();
    $contentEditor->withFlashButton();
    $contentEditor->withLexicon();
    $contentEditor->setHeight(500);
    $str = $contentEditor->render();
    $str .= $contentEditor->renderInstance("content", $content);
    $panelUtils->addLine($str);
  }

  $panelUtils->addHiddenField('dynpageId', $dynpageId);
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->closeForm();

  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
