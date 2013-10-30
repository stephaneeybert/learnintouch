<?

class DocumentCategoryUtils extends DocumentCategoryDB {

  var $mlText;
  var $websiteText;

  var $nbPerRow;

  var $languageUtils;
  var $preferenceUtils;
  var $documentUtils;

  function DocumentCategoryUtils() {
    $this->DocumentCategoryDB();

    $this->init();
  }

  function init() {
    $this->nbPerRow = 3;
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  // Get the next available list order
  function getNextListOrder() {
    $listOrder = 1;
    if ($categorys = $this->selectAll()) {
      $total = count($categorys);
      if ($total > 0) {
        $category = $categorys[$total - 1];
        $listOrder = $category->getListOrder() + 1;
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
    if ($documentCategory = $this->selectById($id)) {
      $listOrder = $documentCategory->getListOrder();
      if ($documentCategories = $this->selectByListOrder($listOrder)) {
        if (($listOrder == 0) || (count($documentCategories)) > 1) {
          $this->resetListOrder();
        }
      }
    }
  }

  // Get the next object
  function selectNext($id) {
    if ($category = $this->selectById($id)) {
      $listOrder = $category->getListOrder();
      if ($category = $this->selectByNextListOrder($listOrder)) {
        return($category);
      }
    }
  }

  // Get the previous object
  function selectPrevious($id) {
    if ($category = $this->selectById($id)) {
      $listOrder = $category->getListOrder();
      if ($category = $this->selectByPreviousListOrder($listOrder)) {
        return($category);
      }
    }
  }

  // Render a document category
  function render($documentCategoryId) {
    global $gDocumentUrl;
    global $gIsPhoneClient;

    $this->loadLanguageTexts();

    $str = '';

    $str .= "\n<div class='document'>";

    $displayAll = $this->preferenceUtils->getValue("DOCUMENT_DISPLAY_ALL");

    $documents = array();
    if ($documentCategoryId > 0 && !$displayAll) {
      $documents = $this->documentUtils->selectByCategoryId($documentCategoryId);
    } else {
      $documents = $this->documentUtils->selectAll();
    }

    $hideSelector = $this->preferenceUtils->getValue("DOCUMENT_HIDE_SELECTOR");

    if ($this->countAll() > 1 && !$hideSelector) {
      $categoryList = array('-1' => '');
      if ($categorys = $this->selectAll()) {
        foreach ($categorys as $wDocumentCategory) {
          $wDocumentCategoryId = $wDocumentCategory->getId();
          $wName = $wDocumentCategory->getName();
          $categoryList[$wDocumentCategoryId] = $wName;
        }
      }
      $strSelect = LibHtml::getSelectList("documentCategoryId", $categoryList, $documentCategoryId, true);

      $str .= "\n<form action='$gDocumentUrl/display.php' method='post'>";
      $str .= "\n<div class='document_category'>";
      $str .= "\n" . $this->websiteText[0] . " $strSelect";
      $str .= "\n</div>";
      $str .= "\n</form>";
    }

    $documentList = array();
    for ($i = 0; $i < count($documents); $i++) {
      $document = $documents[$i];
      $documentList[$i] = $this->documentUtils->render($document);
    }

    if (!$gIsPhoneClient) {
      $nbPerRow = $this->preferenceUtils->getValue("DOCUMENT_NB_PER_ROW");

      // Make sure there is an upper limit set for the loop
      // Otherwise this can crash the web server services
      if (!$nbPerRow) {
        $nbPerRow = $this->nbPerRow;
      }
    } else {
      $nbPerRow = 1;
    }

    $str .= "\n<table border='0' width='100%' cellpadding='0' cellspacing='0'>";

    for ($i = 0; $i < count($documentList); $i = $i + $nbPerRow) {
      $str .= "\n<tr>";
      for ($j = 0; $j < $nbPerRow; $j++) {
        $str .= "\n<td>"
          . LibUtils::getArrayValue($i+$j, $documentList)
          . "</td>";
      }
      $str .= "\n</tr>";
    }

    $str .= "\n</table>";

    $str .= "\n</div>";

    return($str);
  }

  // Get the list of categories
  function getAll() {
    $this->loadLanguageTexts();

    $list = array();

    if ($documentCategories = $this->selectAll()) {
      foreach ($documentCategories as $documentCategory) {
        $documentCategoryId = $documentCategory->getId();
        $name = $documentCategory->getName();
        $list['SYSTEM_PAGE_DOCUMENT_LIST' . $documentCategoryId] = $this->mlText[1]
          . " " . $name;
      }
    }

    return($list);
  }

}

?>
