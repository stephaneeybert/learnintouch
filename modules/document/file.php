<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DOCUMENT);

$mlText = $languageUtils->getMlText(__FILE__);

$fileUploadUtils->loadLanguageTexts();
$filePath = $documentUtils->filePath;
$fileUrl = $documentUtils->fileUrl;
$fileSize = $documentUtils->fileSize;

$warnings = array();

// Check if there are document categories
if ($documentCategoryUtils->countAll() == 0) {
  array_push($warnings, $mlText[8]);
}

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $documentId = LibEnv::getEnvHttpPOST("documentId");
  $categoryId = LibEnv::getEnvHttpPOST("categoryId");

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
  } else if ($str = $fileUploadUtils->checkFileSize($userfile_size, $fileSize)) {
    array_push($warnings, $str);
  } else if ($str = $fileUploadUtils->uploadFile($userfile, $userfile_name, $filePath)) {
    // Check if the file has been copied to the directory
    array_push($warnings, $str);
  }

  // Update the file
  $file = $userfile_name;

  if (count($warnings) == 0) {

    if ($document = $documentUtils->selectById($documentId)) {
      $document->setFile($file);
      $document->setCategoryId($categoryId);
      $documentUtils->update($document);
    } else {
      $document = new Document();
      $document->setFile($file);
      $document->setCategoryId($categoryId);

      // Get the next list order
      $listOrder = $documentUtils->getNextListOrder($categoryId);
      $document->setListOrder($listOrder);

      $documentUtils->insert($document);
    }

    $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
    printContent($str);
    return;
  }

}

$documentId = LibEnv::getEnvHttpGET("documentId");
if (!$documentId) {
  $documentId = LibEnv::getEnvHttpPOST("documentId");
}

// If an id is passed get the current properties
$file = '';
if ($documentId) {
  if ($document = $documentUtils->selectById($documentId)) {
    $categoryId = $document->getCategoryId();
    $file = $document->getFile();
  }
} else {
  // Get the chosen category from the session
  $categoryId = LibSession::getSessionValue(DOCUMENT_SESSION_CATEGORY);
}

$panelUtils->setHeader($mlText[0]);

if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $panelUtils->addLine($panelUtils->addCell($warning, "w"));
  }
}

$panelUtils->openMultipartForm($PHP_SELF);

if (@file_exists($filePath . $file)) {
  $strFile = "<a href='$fileUrl/$file' $gJSNoStatus title=''>$file</a>";
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), $strFile);
}

$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type=file name='userfile' size='15' maxlength='50'>");
$panelUtils->addLine('', $fileUploadUtils->getFileSizeMessage($fileSize));
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());

$panelUtils->addHiddenField('max_file_size', $fileSize);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('documentId', $documentId);
$panelUtils->addHiddenField('categoryId', $categoryId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
