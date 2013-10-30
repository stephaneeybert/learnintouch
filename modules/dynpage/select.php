<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$dynpageId = LibEnv::getEnvHttpPOST("dynpageId");

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  if ($dynpage = $dynpageUtils->selectById($dynpageId)) {
    $name = $templateUtils->getPageName($dynpageId);

    $str = <<<HEREDOC
<script type='text/javascript'>

// Get the form of the parent window
var form = window.opener.document.forms['edit'];

// Reset the url field in the model navigation elements
if (form.elements['url']) {
  form.elements['url'].value = '';
}
if (form.elements['externalUrl']) {
  form.elements['externalUrl'].value = '';
}

// Set the webpageName field in the model navigation elements
if (form.elements['webpageName']) {
  form.elements['webpageName'].value = '$name';
}

// Set the webpageId field in the model navigation elements
if (form.elements['webpageId']) {
  form.elements['webpageId'].value = $dynpageId;
}

</script>
HEREDOC;

    printMessage($str);
  }

  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
}

$panelUtils->setHeader($mlText[0], "$gTemplateUrl/select.php");
$help = $popupUtils->getHelpPopup($mlText[1], 300, 300);
$panelUtils->setHelp($help);

// Display the okay button only if a page has been selected
if ($dynpage = $dynpageUtils->selectById($dynpageId)) {
  $name = $dynpage->getName();

  $panelUtils->openForm($PHP_SELF);
  $folderPath = $dynpageUtils->getFolderPath($dynpageId);
  $panelUtils->addLine($panelUtils->addCell($folderPath, "c"));
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($panelUtils->getOk(), "c"));
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('dynpageId', $dynpageId);
  $panelUtils->closeForm();
  $panelUtils->addLine();
}

$dynpages = renderChildren(DYNPAGE_ROOT_ID, 0);

$str = $panelUtils->render();

printAdminPage($str);

function renderChildren($parentId, $indentLevel) {
  global $dynpageUtils;
  global $panelUtils;
  global $gImagePage;
  global $gImageFolder;
  global $PHP_SELF;
  global $gCommonImagesUrl;
  global $movedDynpageId;

  $strIndent = str_repeat('&nbsp;', $indentLevel * 8);
  $indentLevel++;

  $dynpages = $dynpageUtils->selectChildren($parentId);

  foreach ($dynpages as $dynpage) {
    $dynpageId = $dynpage->getId();
    $name = $dynpage->getName();

    $hasChild = $dynpageUtils->hasChild($dynpageId);

    if ($hasChild) {
      $imageFolder = $gImageFolder;
    } else {
      $imageFolder = $gImagePage;
    }

    $panelUtils->openForm($PHP_SELF);
    $panelUtils->addLine("$strIndent <input type='image' border='0' src='$gCommonImagesUrl/$imageFolder' title=''> $name");
    $panelUtils->addHiddenField('dynpageId', $dynpageId);
    $panelUtils->closeForm();

    if ($dynpageUtils->hasChild($dynpageId)) {
      renderChildren($dynpageId, $indentLevel);
    }
  }
}

?>
