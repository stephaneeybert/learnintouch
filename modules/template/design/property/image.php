<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$fileUploadUtils->loadLanguageTexts();
$imagePath = $templatePropertyUtils->imagePath;
$imageUrl = $templatePropertyUtils->imageUrl;
$imageSize = $templatePropertyUtils->imageSize;

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $name = LibEnv::getEnvHttpPOST("name");
  $templatePropertySetId = LibEnv::getEnvHttpPOST("templatePropertySetId");
  $deleteImage = LibEnv::getEnvHttpPOST("deleteImage");

  if ($deleteImage == 1) {
    $image = '';
    } else {
    // Get the file characteristics
    // Note how the form parameter "userfile" creates several variables
    $uploaded_file = LibEnv::getEnvHttpFILE("userfile");
    $userfile = $uploaded_file['tmp_name'];
    $userfile_name = $uploaded_file['name'];
    $userfile_type = $uploaded_file['type'];
    $userfile_size = $uploaded_file['size'];

    // Clean up the filename
    $userfile_name = LibString::stripNonFilenameChar($userfile_name);

    // Check if a file has been specified...
    if ($str = $fileUploadUtils->checkFileName($userfile_name)) {
      array_push($warnings, $str);
      } else if ($str = $fileUploadUtils->checkImageFileType($userfile_name)) {
      // Check if the image file name has a correct file type
      array_push($warnings, $str);
      } else if ($str = $fileUploadUtils->checkFileSize($userfile_size, $imageSize)) {
      array_push($warnings, $str);
      } else if ($str = $fileUploadUtils->uploadFile($userfile, $userfile_name, $imagePath)) {
      // Check if the file has been copied to the directory
      array_push($warnings, $str);
      }

    // Update the image
    $image = $userfile_name;
    }

  if (count($warnings) == 0) {

    if ($templateProperty = $templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, $name)) {
      $templateProperty->setValue($image);
      $templatePropertyUtils->update($templateProperty);
      } else {
      $templateProperty = new TemplateProperty();
      $templateProperty->setName($name);
      $templateProperty->setValue($image);
      $templateProperty->setTemplatePropertySetId($templatePropertySetId);
      $templatePropertyUtils->insert($templateProperty);
      }

    $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
    printContent($str);
    return;
    }

  }

$name = LibEnv::getEnvHttpGET("name");
$templatePropertySetId = LibEnv::getEnvHttpGET("templatePropertySetId");
if (!$name) {
  $name = LibEnv::getEnvHttpPOST("name");
  }
if (!$templatePropertySetId) {
  $templatePropertySetId = LibEnv::getEnvHttpPOST("templatePropertySetId");
  }

$image = '';
if ($templateProperty = $templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, $name)) {
  $image = $templateProperty->getValue();
  }

$panelUtils->setHeader($mlText[0]);

if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $panelUtils->addLine($panelUtils->addCell($warning, "w"));
    }
  }

$help = $popupUtils->getHelpPopup($mlText[1], 300, 300);
$panelUtils->setHelp($help);
$panelUtils->openMultipartForm($PHP_SELF);

if ($image) {
  $panelUtils->addLine($panelUtils->addCell($mlText[6], "br"), "<img src='$imageUrl/$image' border='0' title='" . $mlText[3] . " " . $image . "' href=''>");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[7], "br"), "<input type='checkbox' name='deleteImage' value='1'>");
  }

$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), "<input type=file name='userfile' size='15' maxlength='50'>");
$panelUtils->addLine('', $fileUploadUtils->getFileSizeMessage($imageSize));
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());

$panelUtils->addHiddenField('max_file_size', $imageSize);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('name', $name);
$panelUtils->addHiddenField('templatePropertySetId', $templatePropertySetId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
