<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningLessonModelId = LibEnv::getEnvHttpPOST("elearningLessonModelId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");
  $currentLanguageCode = LibEnv::getEnvHttpPOST("currentLanguageCode");
  $instructions = LibEnv::getEnvHttpPOST("instructions");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);
  $currentLanguageCode = LibString::cleanString($currentLanguageCode);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[6]);
  }

  $instructions = LibString::cleanHtmlString($instructions);

  if (count($warnings) == 0) {

    if ($elearningLessonModel = $elearningLessonModelUtils->selectById($elearningLessonModelId)) {
      $elearningLessonModel->setName($name);
      $elearningLessonModel->setDescription($description);
      $elearningLessonModel->setInstructions($languageUtils->setTextForLanguage($elearningLessonModel->getInstructions(), $currentLanguageCode, $instructions));
      $elearningLessonModelUtils->update($elearningLessonModel);

      $elearningLessonHeadings = $elearningLessonHeadingUtils->selectByElearningLessonModelId($elearningLessonModelId);
      foreach ($elearningLessonHeadings as $elearningLessonHeading) {
        $elearningLessonHeadingId = $elearningLessonHeading->getId();
        $name = LibEnv::getEnvHttpPOST("headingName$elearningLessonHeadingId");
        $content = LibEnv::getEnvHttpPOST("content$elearningLessonHeadingId");

        $name = LibString::cleanString($name);

        $content = LibString::cleanHtmlString($content);

        if ($elearningLessonHeading = $elearningLessonHeadingUtils->selectById($elearningLessonHeadingId)) {
          $elearningLessonHeading->setName($name);
          $elearningLessonHeading->setContent($content);
          $elearningLessonHeadingUtils->update($elearningLessonHeading);
        }
      }
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/lesson/model/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningLessonModelId = LibEnv::getEnvHttpGET("elearningLessonModelId");

  $currentLanguageCode = $languageUtils->getCurrentLanguageCode();

  // Add or delete a lesson heading
  $elearningLessonHeadingId = LibEnv::getEnvHttpGET("elearningLessonHeadingId");
  $addLessonHeading = LibEnv::getEnvHttpGET("addLessonHeading");
  $deleteLessonHeading = LibEnv::getEnvHttpGET("deleteLessonHeading");

  if ($addLessonHeading && $elearningLessonModelId) {
    $elearningLessonHeadingUtils->add($elearningLessonModelId);

    $str = LibHtml::urlRedirect("$PHP_SELF?elearningLessonModelId=$elearningLessonModelId");
    printContent($str);
    return;
  } else if ($deleteLessonHeading && $elearningLessonHeadingId) {
    $elearningLessonHeadingUtils->deleteHeading($elearningLessonHeadingId);
  }

  // Swap lesson headings
  $swapWithPrevious = LibEnv::getEnvHttpGET("swapWithPrevious");
  $swapWithNext = LibEnv::getEnvHttpGET("swapWithNext");

  if ($swapWithPrevious && $elearningLessonModelId) {
    $elearningLessonHeadingUtils->swapWithPrevious($elearningLessonHeadingId);
  } else if ($swapWithNext && $elearningLessonModelId) {
    $elearningLessonHeadingUtils->swapWithNext($elearningLessonHeadingId);
  }

  $name = '';
  $description = '';
  $instructions = '';
  if ($elearningLessonModelId) {
    if ($elearningLessonModel = $elearningLessonModelUtils->selectById($elearningLessonModelId)) {
      $name = $elearningLessonModel->getName();
      $description = $elearningLessonModel->getDescription();
      $instructions = $languageUtils->getTextForLanguage($elearningLessonModel->getInstructions(), $currentLanguageCode);
    }
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/lesson/model/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[14], $mlText[15], 300, 300);
if ($elearningExerciseUtils->useHtmlEditorInnova()) {
  $oInnovaContentName = "instructions";
  include($gInnovaHtmlEditorPath . "setupElearningInstructions.php");
  $panelUtils->addContent($gInnovaHead);
  $strEditor = "<textarea id='$oInnovaContentName' name='$oInnovaContentName' cols='30' rows='5'>\n$instructions\n</textarea> $gInnovaBodyOpen $gInnovaBodyClose";
  $strJsEditor = <<<HEREDOC
<script type='text/javascript'>
function getContent() {
  var content = $oInnovaName.getHTMLBody();
  return(content);
}
function setContent(content) {
  $oInnovaName.putHTML(content);
}
$oInnovaName.onSave=new Function("saveInnovaEditorContent()");
function saveInnovaEditorContent() {
  var body = getContent();
  saveEditorContent("$oInnovaContentName", body);
}
</script>
HEREDOC;
} else {
  include($gHtmlEditorPath . "CKEditorUtils.php");
  $editorName = "instructions";
  $contentEditor = new CKEditorUtils();
  $contentEditor->languageUtils = $languageUtils;
  $contentEditor->commonUtils = $commonUtils;
  $contentEditor->load();
  $contentEditor->withReducedToolbar();
  $contentEditor->withAjaxSave();
  $contentEditor->setHeight(300);
  $strEditor = $contentEditor->render();
  $strEditor .= $contentEditor->renderInstance($editorName, $instructions);
  $strJsEditor = <<<HEREDOC
<script type='text/javascript'>
function getContent() {
  var editor = CKEDITOR.instances.$editorName;
  var content = editor.getData();
  return(content);
}
function setContent(content) {
  var editor = CKEDITOR.instances.$editorName;
  editor.setData(content);
}
</script>
HEREDOC;
}
$panelUtils->addHiddenField('currentLanguageCode', $currentLanguageCode);
$strLanguageFlag = $languageUtils->renderChangeWebsiteLanguageBar($currentLanguageCode);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strEditor . ' ' . $strLanguageFlag);
$strJsEditor .= <<<HEREDOC
<script type='text/javascript'>
function changeWebsiteLanguage(languageCode) {
  var url = '$gElearningUrl/lesson/model/get_instructions.php?elearningLessonModelId=$elearningLessonModelId&languageCode='+languageCode;
  document.getElementById('currentLanguageCode').value = languageCode;
  ajaxAsynchronousRequest(url, updateInstructions);
}
function updateInstructions(responseText) {
  var response = eval('(' + responseText + ')');
  var instructions = response.instructions;
  setContent(instructions);
}
function saveEditorContent(editorName, content) {
  content = encodeURIComponent(content);
  var languageCode = document.getElementById('currentLanguageCode').value;
  var params = []; params["elearningLessonModelId"] = "$elearningLessonModelId"; params["languageCode"] = languageCode; params[editorName] = content;
  ajaxAsynchronousPOSTRequest("$gElearningUrl/lesson/model/update.php", params);
}
</script>
HEREDOC;
$panelUtils->addContent($strJsEditor);

$strConfirmDelete = <<<HEREDOC
<script type='text/javascript'>
function confirmDelete() {
  confirmation = confirm('$mlText[3]');
  if (confirmation) {
    return(true);
  }

  return(false);
}
</script>
HEREDOC;
$panelUtils->addContent($strConfirmDelete);

$gInnovaHead = '';

if ($elearningLessonHeadings = $elearningLessonHeadingUtils->selectByElearningLessonModelId($elearningLessonModelId)) {
  foreach ($elearningLessonHeadings as $elearningLessonHeading) {
    $elearningLessonHeadingId = $elearningLessonHeading->getId();
    $headingName = $elearningLessonHeading->getName();
    $content = $elearningLessonHeading->getContent();
    $image = $elearningLessonHeading->getImage();

    $panelUtils->addLine();
    $label = $popupUtils->getTipPopup($mlText[9], $mlText[8], 300, 300);
    $panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='headingName$elearningLessonHeadingId' value='$headingName' size='30' maxlength='50'>");
    $panelUtils->addLine();

    if ($image) {
      if (LibImage::isImage($image) && !LibImage::isGif($image)) {
        $filename = urlencode($elearningLessonHeadingUtils->imageFilePath . $image);
        $url = $gUtilsUrl . "/printImage.php?filename=" . $filename . "&width=120&height=";
        $strImage = "<img src='$url' border='0' title='' href=''>";
      } else {
        $fileUrl = "$elearningLessonHeadingUtils->imageFileUrl/$image";
        $strImage = "<a href='$fileUrl' $gJSNoStatus title=''>$image</a>";
      }
    } else {
      $strImage = '';
    }
    $panelUtils->addLine($panelUtils->addCell($mlText[10], "nbr"), $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[13]'>", "$gElearningUrl/lesson/heading/image.php?elearningLessonHeadingId=$elearningLessonHeadingId", 600, 600) . ' ' . $strImage);
    $panelUtils->addLine();

    if ($elearningExerciseUtils->useHtmlEditorInnova()) {
      $oInnovaContentName = "content$elearningLessonHeadingId";
      include($gInnovaHtmlEditorPath . "setupElearningLessonHeading.php");
      $strEditor = "<textarea id='$oInnovaContentName' name='$oInnovaContentName'>$content</textarea> $gInnovaBodyOpen $gInnovaBodyClose";
    } else {
      $editorName = "content$elearningLessonHeadingId";
      $contentEditor = new CKEditorUtils();
      $contentEditor->languageUtils = $languageUtils;
      $contentEditor->commonUtils = $commonUtils;
      $contentEditor->load();
      $contentEditor->setImagePath($elearningLessonHeadingUtils->imageFilePath);
      $contentEditor->setImageUrl($elearningLessonHeadingUtils->imageFileUrl);
      $contentEditor->setImageBrowserUploadUrl($gSystemUrl . '/editor/ckeditor/connector/image_elearning_lesson_heading.php');
      $contentEditor->withReducedToolbar();
      $contentEditor->withImageButton();
      $contentEditor->setHeight(300);
      $strEditor = $contentEditor->renderInstance($editorName, $content);
    }

    // The one and only lesson heading cannot be swaped
    $strLine = '';
    if (count($elearningLessonHeadings) > 1) {
      $strLine .= " <a href='$PHP_SELF?elearningLessonModelId=$elearningLessonModelId&elearningLessonHeadingId=$elearningLessonHeadingId&swapWithPrevious=1' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImageUp' title='$mlText[12]'></a>"
        . " <a href='$PHP_SELF?elearningLessonModelId=$elearningLessonModelId&elearningLessonHeadingId=$elearningLessonHeadingId&swapWithNext=1' $gJSNoStatus>"
        . "<img border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[11]'></a>";
    }

    // The one and only lesson heading cannot be deleted
    if (count($elearningLessonHeadings) > 1) {
      $strLine .= " <a href='$PHP_SELF?elearningLessonModelId=$elearningLessonModelId&deleteLessonHeading=1&elearningLessonHeadingId=$elearningLessonHeadingId' onclick='javascript:return(confirmDelete(this))' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[2]' /> $mlText[2]</a>";
    }

    $label = $popupUtils->getTipPopup($mlText[7], $mlText[8], 300, 300);
    $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strEditor. $strLine);
  }
}

$strAddLessonHeading = "<img id='addButton' border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'> $mlText[1]";
$panelUtils->addLine('', $panelUtils->addCell($strAddLessonHeading, "nb"));
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningLessonModelId', $elearningLessonModelId);
$panelUtils->closeForm();

$TODO_strWarnEdit = <<<HEREDOC
$(document).ready(function() {
});
</script>
HEREDOC;

$strRememberScroll = LibJavaScript::rememberScroll("elearning_lesson_model_compose_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

$strSkipFormFocus = <<<HEREDOC
<script type='text/javascript'>
var skipFormFocus = true;
</script>
HEREDOC;

printAdminPage($str, $strSkipFormFocus);

?>
