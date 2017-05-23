<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $newsStoryId = LibEnv::getEnvHttpPOST("newsStoryId");
  $headline = LibEnv::getEnvHttpPOST("headline");
  $excerpt = LibEnv::getEnvHttpPOST("excerpt");

  $headline = LibString::cleanString($headline);

  // The headline is required
  if (!$headline) {
    array_push($warnings, $mlText[4]);
  }

  $excerpt = LibString::cleanHtmlString($excerpt);

  if (count($warnings) == 0) {

    if ($newsStory = $newsStoryUtils->selectById($newsStoryId)) {
      $newsStory->setHeadline($headline);
      $newsStory->setExcerpt($excerpt);
      $newsStoryUtils->update($newsStory);

      $newsStoryParagraphs = $newsStoryParagraphUtils->selectByNewsStoryId($newsStoryId);
      foreach ($newsStoryParagraphs as $newsStoryParagraph) {
        $newsStoryParagraphId = $newsStoryParagraph->getId();
        $header = LibEnv::getEnvHttpPOST("header$newsStoryParagraphId");
        $body = LibEnv::getEnvHttpPOST("body$newsStoryParagraphId");
        $footer = LibEnv::getEnvHttpPOST("footer$newsStoryParagraphId");

        $header = LibString::cleanHtmlString($header);
        $body = LibString::cleanHtmlString($body);
        $footer = LibString::cleanHtmlString($footer);

        if ($newsStoryParagraph = $newsStoryParagraphUtils->selectById($newsStoryParagraphId)) {
          $newsStoryParagraph->setHeader($header);
          $newsStoryParagraph->setBody($body);
          $newsStoryParagraph->setFooter($footer);
          $newsStoryParagraphUtils->update($newsStoryParagraph);
        }
      }
    }

    $str = LibHtml::urlRedirect("$gNewsUrl/newsStory/admin.php");
    printContent($str);
    return;

  }

} else {

  $newsStoryId = LibEnv::getEnvHttpGET("newsStoryId");

  // Add or delete a paragraph
  $newsStoryParagraphId = LibEnv::getEnvHttpGET("newsStoryParagraphId");
  $addParagraph = LibEnv::getEnvHttpGET("addParagraph");
  $deleteParagraph = LibEnv::getEnvHttpGET("deleteParagraph");

  if ($addParagraph && $newsStoryId) {
    $newsStoryParagraphUtils->add($newsStoryId);

    $str = LibHtml::urlRedirect("$PHP_SELF?newsStoryId=$newsStoryId");
    printContent($str);
    exit;
  } else if ($deleteParagraph && $newsStoryParagraphId) {
    $newsStoryParagraphUtils->deleteParagraph($newsStoryParagraphId);
  }

  $headline = '';
  $excerpt = '';
  if ($newsStoryId) {
    if ($newsStory = $newsStoryUtils->selectById($newsStoryId)) {
      $headline = $newsStory->getHeadline();
      $excerpt = $newsStory->getExcerpt();

      // If by accident the news story had no paragraph then just create one
      // This should not happen though...
      if (!$newsStoryParagraph = $newsStoryParagraphUtils->selectByNewsStoryId($newsStoryId)) {
        $newsStoryParagraphUtils->add($newsStoryId);
      }
    }
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gNewsUrl/newsStory/admin.php?newsStoryId=$newsStoryId");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nb"));
$panelUtils->addLine("<input type='text' name='headline' value='$headline' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[1], $mlText[23], 300, 200);
include($gHtmlEditorPath . "CKEditorUtils.php");
$contentEditor = new CKEditorUtils();
$contentEditor->languageUtils = $languageUtils;
$contentEditor->commonUtils = $commonUtils;
$contentEditor->load();
$contentEditor->setImagePath($newsStoryImageUtils->imageFilePath);
$contentEditor->setImageUrl($newsStoryImageUtils->imageFileUrl);
$contentEditor->setImageBrowserUploadUrl($gSystemUrl . '/editor/ckeditor/connector/image_news_story.php');
$contentEditor->withReducedToolbar();
$contentEditor->withImageButton();
$strEditor = $contentEditor->render();
$strEditor .= $contentEditor->renderInstance("excerpt", $excerpt);
$panelUtils->addLine($panelUtils->addCell($label, "nb"));
$panelUtils->addLine($strEditor);
$panelUtils->addLine();

$strConfirmDelete = <<<HEREDOC
<script type='text/javascript'>
function confirmDelete() {
  confirmation = confirm('$mlText[30]');
  if (confirmation) {
    return(true);
  }

  return(false);
}
</script>
HEREDOC;
$panelUtils->addContent($strConfirmDelete);

// Display the paragraphs
$newsStoryParagraphs = $newsStoryParagraphUtils->selectByNewsStoryId($newsStoryId);
for ($i = 0; $i < count($newsStoryParagraphs); $i++) {
  $newsStoryParagraph = $newsStoryParagraphs[$i];
  $newsStoryParagraphId = $newsStoryParagraph->getId();
  $header = $newsStoryParagraph->getHeader();
  $body = $newsStoryParagraph->getBody();
  $footer = $newsStoryParagraph->getFooter();

  $contentEditor = new CKEditorUtils();
  $contentEditor->languageUtils = $languageUtils;
  $contentEditor->commonUtils = $commonUtils;
  $contentEditor->load();
  $contentEditor->setImagePath($newsStoryImageUtils->imageFilePath);
  $contentEditor->setImageUrl($newsStoryImageUtils->imageFileUrl);
  $contentEditor->setImageBrowserUploadUrl($gSystemUrl . '/editor/ckeditor/connector/image_news_story.php');
  $contentEditor->withStandardToolbar();
  $contentEditor->withImageButton();
  $contentEditor->setHeight(300);
  $strEditor = $contentEditor->renderInstance("header$newsStoryParagraphId", $header);

  $label = $popupUtils->getTipPopup($mlText[3], $mlText[24], 300, 300);
  $panelUtils->addLine($panelUtils->addCell($label, "nb"));
  $panelUtils->addLine($strEditor);

  $strEditor = $contentEditor->renderInstance("body$newsStoryParagraphId", $body);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "nb"));
  $panelUtils->addLine($strEditor);

  $strEditor = $contentEditor->renderInstance("footer$newsStoryParagraphId", $footer);

  // The one and only paragraph cannot be deleted
  $strLine = '';
  if (count($newsStoryParagraphs) > 1) {
    $strDeleteParagraph = "<a href='$PHP_SELF?newsStoryId=$newsStoryId&deleteParagraph=1&newsStoryParagraphId=$newsStoryParagraphId' onclick='javascript:return(confirmDelete(this))' $gJSNoStatus>"
      . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[29]' /> $mlText[29]</a>";
    $strLine .= ' ' . $strDeleteParagraph;
  }

  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nb"));
  $panelUtils->addLine($strEditor . $strLine);
}

$strAddParagraph = "<a href='$PHP_SELF?newsStoryId=$newsStoryId&addParagraph=1' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[28]' /> $mlText[28]</a>";
$panelUtils->addLine($strAddParagraph);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($panelUtils->getOk(), "c"));
$panelUtils->addHiddenField('newsStoryId', $newsStoryId);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
