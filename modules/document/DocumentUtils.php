<?

class DocumentUtils extends DocumentDB {

  var $mlText;
  var $websiteText;

  var $filePath;
  var $fileUrl;
  var $fileSize;

  var $maxWidth;
  var $maxHeight;

  var $preferences;

  var $preferenceUtils;
  var $languageUtils;
  var $userUtils;

  function __construct() {
    parent::__construct();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->filePath = $gDataPath . 'document/file/';
    $this->fileUrl = $gDataUrl . '/document/file';
    $this->fileSize = 20000000;
  }

  function loadLanguageTexts() {
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->filePath)) {
      if (!is_dir($gDataPath . 'document')) {
        mkdir($gDataPath . 'document');
      }
      mkdir($this->filePath);
      chmod($this->filePath, 0755);
    }
  }

  function loadPreferences() {
    $this->loadLanguageTexts();

    $this->preferences = array(
      "DOCUMENT_NB_PER_ROW" =>
      array($this->mlText[32], $this->mlText[33], PREFERENCE_TYPE_RANGE, array(1, 10, 3)),
        "DOCUMENT_DISPLAY_ALL" =>
        array($this->mlText[4], $this->mlText[5], PREFERENCE_TYPE_BOOLEAN, ''),
          "DOCUMENT_HIDE_SELECTOR" =>
          array($this->mlText[6], $this->mlText[7], PREFERENCE_TYPE_BOOLEAN, ''),
          "DOCUMENT_ISSUU_SMARTLOOK" =>
          array($this->mlText[9], $this->mlText[10], PREFERENCE_TYPE_RAW_CONTENT, ''),
          "DOCUMENT_ISSUU_DISABLE" =>
          array($this->mlText[11], $this->mlText[12], PREFERENCE_TYPE_BOOLEAN, ''),
          );

    $this->preferenceUtils->init($this->preferences);
  }

  // Remove the non referenced files from the directory
  function deleteUnusedFiles() {
    $handle = opendir($this->filePath);
    while ($oneFile = readdir($handle)) {
      if ($oneFile != "." && $oneFile != ".." && !strstr($oneFile, '*')) {
        if (!$this->fileIsUsed($oneFile)) {
          $oneFile = str_replace(" ", "\\ ", $oneFile);
          if (file_exists($this->filePath . $oneFile)) {
            unlink($this->filePath . $oneFile);
          }
        }
      }
    }
    closedir($handle);
  }

  // Check if a file is being used
  function fileIsUsed($file) {
    $isUsed = true;

    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByFile($file)) {
      if ($result->getRowCount() < 1) {
        $isUsed = false;
      }
    }

    return($isUsed);
  }

  // Delete a document
  function deleteDocument($documentId) {
    $this->delete($documentId);
  }

  // Get the url to view or download the document
  function getDocumentUrl($documentId) {
    $url = '';

    if ($this->isEnabled()) {
      if ($document = $this->selectById($documentId)) {
        $file = $document->getFile();
        $url = $this->fileUrl . '/' . $file;
      }
    } else {
      $url = $this->getDownloadUrl($documentId);
    }

    return($url);
  }

  // Get the url to download the document
  function getDownloadUrl($documentId) {
    global $gUtilsUrl;
    global $gRootPath;

    $url = '';

    if ($document = $this->selectById($documentId)) {
      $file = $document->getFile();
      $filename = $this->filePath . $file;
      $url = "$gUtilsUrl/download.php?filename=$filename";
    }

    return($url);
  }

  // Check if a document is hidden
  function isHidden($documentId) {
    $hidden = false;

    if ($document = $this->selectById($documentId)) {
      $hidden = $document->getHide();
    }

    return($hidden);
  }

  // Check if a document is secured
  function isSecured($documentId) {
    $secured = false;

    if ($document = $this->selectById($documentId)) {
      $secured = $document->getSecured();
    }

    return($secured);
  }

  // Get the list of published documents
  function getPublishedDocumentList() {
    $this->loadLanguageTexts();

    global $gDocumentUrl;

    $list = array();

    if ($documents = $this->selectPublished()) {
      foreach ($documents as $document) {
        $documentId = $document->getId();
        $file = $document->getFile();
        $description = $document->getDescription();
        $reference = $document->getReference();

        $list['SYSTEM_PAGE_DOCUMENT' . $documentId] = $this->mlText[0] . " " . $file;
      }
    }

    return($list);
  }

  // Get the next available list order
  function getNextListOrder($categoryId) {
    $listOrder = 1;
    if ($documents = $this->selectByCategoryId($categoryId)) {
      $total = count($documents);
      if ($total > 0) {
        $document = $documents[$total - 1];
        $listOrder = $document->getListOrder() + 1;
      }
    }

    return($listOrder);
  }

  // Swap the curent object with the next one
  function swapWithNext($id) {
    $this->repairListOrder($id);

    $currentObject = $this->selectById($id);
    $currentListOrder = $currentObject->getListOrder();

    // Get the next object and its list order
    if (!$nextObject = $this->selectNext($id)) {
      return;
    }
    $nextListOrder = $nextObject->getListOrder();

    // Update the list orders
    $currentObject->setListOrder($nextListOrder);
    $this->update($currentObject);
    $nextObject->setListOrder($currentListOrder);
    $this->update($nextObject);
  }

  // Swap the curent object with the previous one
  function swapWithPrevious($id) {
    $this->repairListOrder($id);

    $currentObject = $this->selectById($id);
    $currentListOrder = $currentObject->getListOrder();

    // Get the previous object and its list order
    if (!$previousObject = $this->selectPrevious($id)) {
      return;
    }
    $previousListOrder = $previousObject->getListOrder();

    // Update the list orders
    $currentObject->setListOrder($previousListOrder);
    $this->update($currentObject);
    $previousObject->setListOrder($currentListOrder);
    $this->update($previousObject);
  }

  // Repair the order if some order numbers are identical
  // If, by accident, some objects have the same list order
  // (it shouldn't happen) then assign a new list order to each of them
  function repairListOrder($id) {
    if ($document = $this->selectById($id)) {
      $listOrder = $document->getListOrder();
      $categoryId = $document->getCategoryId();
      if ($documents = $this->selectByListOrder($categoryId, $listOrder)) {
        if (($listOrder == 0) || (count($documents)) > 1) {
          $this->resetListOrder($categoryId);
        }
      }
    }
  }

  // Get the next object
  function selectNext($id) {
    if ($document = $this->selectById($id)) {
      $listOrder = $document->getListOrder();
      $categoryId = $document->getCategoryId();
      if ($document = $this->selectByNextListOrder($categoryId, $listOrder)) {
        return($document);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($document = $this->selectById($id)) {
      $listOrder = $document->getListOrder();
      $categoryId = $document->getCategoryId();
      if ($document = $this->selectByPreviousListOrder($categoryId, $listOrder)) {
        return($document);
      }
    }
  }

  // Render a document
  function render($document) {
    global $gJSNoStatus;
    global $gImagesUserUrl;

    if (!$document) {
      return;
    }

    $this->loadLanguageTexts();

    $documentId = $document->getId();

    if ($this->isHidden($documentId)) {
      return;
    }

    if ($this->isSecured($documentId) && !$this->userUtils->getLoggedUserId()) {
      return;
    }

    $file = $document->getFile();
    $reference = $document->getReference();
    $description = $document->getDescription();

    $downloadUrl = $this->getDownloadUrl($documentId);
    $title = $this->websiteText[1] . ' ' . $file;

    $issuu = false;
    if ($this->isEnabled()) {
      $issuu = true;
    }

    $str = "\n<div class='document_item'>";

    if ($issuu) {
      $str .= "<a href='" . $this->fileUrl . '/' . $file . "' title='$title'>";
    } else {
      $str .= "<a href='$downloadUrl' $gJSNoStatus title='$title'>";
    }
    if ($reference) {
      $str .= "<div class='document_reference'>" . $this->websiteText[2] . ' ' . $reference . '</div>';
    }
    if ($description) {
      $str .= "<div class='document_description'>" . $description . '</div>';
    }
    if (!$reference && !$description) {
      $str .= "<div class='document_filename'>" . $file . '</div>';
    }
    $str .= "</a>";

    $str .= "<div class='document_buttons'>";
    if ($issuu) {
      $str .= "<span class='document_view_button'>"
        . "<a href='" . $this->fileUrl . '/' . $file . "'>"
        . '[' . $this->websiteText[8] . ']'
        . "</a>"
        . "</span>";
    }
    $str .= " <span class='document_download_button'>"
      . "<a href='$downloadUrl' $gJSNoStatus>"
      . '[' . $this->websiteText[3] . ']'
      . "</a>"
      . "</span>";
    $str .= "</div>";

    $str .= '</div>';

    return($str);
  }

  function isEnabled() {
    if ($this->getIssuuSmartlook() && !$this->preferenceUtils->getValue("DOCUMENT_ISSUU_DISABLE")) {
      return(true);
    } else {
      return(false);
    }
  }

  // Get the Issuu.com/smartlook/ viewer
  function getIssuuSmartlook() {
    return($this->preferenceUtils->getValue("DOCUMENT_ISSUU_SMARTLOOK"));
  }

  // Render the styling elements for the editing of the css style properties
  function renderStylingElements() {
    global $gStylingImage;
    global $gImagesUserUrl;

    $str = "<div class='document'>The documents"
      . "<div class='document_category'>"
      . "Document category: A category"
      . "</div>"
      . "<div class='document_item'>A document"
      . "<div class='document_buttons'>"
      . "<span class='document_view_button'>View</span>"
      . "<span class='document_download_button'>Download</span>"
      . "</div>"
      . "<div class='document_reference'>The reference</div>"
      . "<div class='document_description'>The description</div>"
      . "<div class='document_filename'>The filename</div>"
      . "</div>"
      . "</div>";

    return($str);
  }

}

?>
