<?

class PeopleCategoryUtils extends PeopleCategoryDB {

  var $mlText;
  var $websiteText;

  var $nbPerRow;

  var $languageUtils;
  var $preferenceUtils;
  var $peopleUtils;

  function __construct() {
    parent::__construct();

    $this->init();
  }

  function init() {
    $this->nbPerRow = 3;
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  // Get the list of categories
  function getAll() {
    $this->loadLanguageTexts();

    $list = array();

    if ($peopleCategories = $this->selectAll()) {
      foreach ($peopleCategories as $peopleCategory) {
        $peopleCategoryId = $peopleCategory->getId();
        $name = $peopleCategory->getName();
        $list['SYSTEM_PAGE_PEOPLE_LIST' . $peopleCategoryId] = $this->mlText[1]
          . " " . $name;
      }
    }

    return($list);
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
    if ($linkCategory = $this->selectById($id)) {
      $listOrder = $linkCategory->getListOrder();
      if ($linkCategories = $this->selectByListOrder($listOrder)) {
        if (($listOrder == 0) || (count($linkCategories)) > 1) {
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

  // Render a list of people
  function render($peopleCategoryId = '') {
    global $gTemplateUrl;
    global $gPeopleUrl;
    global $gIsPhoneClient;

    $this->loadLanguageTexts();

    $displayAll = $this->preferenceUtils->getValue("PEOPLE_DISPLAY_ALL");

    if (!$peopleCategoryId) {
      if (!$displayAll) {
        // Get the first category
        if ($peopleCategories = $this->selectAll()) {
          if (count($peopleCategories) > 0) {
            $peopleCategory = $peopleCategories[0];
            $peopleCategoryId = $peopleCategory->getId();
          }
        }
      }
    }

    $str = '';

    $str .= "\n<div class='people_list'>";

    if ($gIsPhoneClient) {
      $hideSelector = $this->preferenceUtils->getValue("PEOPLE_PHONE_HIDE_SELECTOR");
    } else {
      $hideSelector = $this->preferenceUtils->getValue("PEOPLE_HIDE_SELECTOR");
    }

    if ($this->countAll() > 1 && !$hideSelector) {
      $categoryList = array('-1' => '');
      if ($categories = $this->selectAll()) {
        foreach ($categories as $wCategory) {
          $wCategoryId = $wCategory->getId();
          $wName = $wCategory->getName();
          $categoryList[$wCategoryId] = $wName;
        }
      }
      $strSelect = LibHtml::getSelectList("peopleCategoryId", $categoryList, $peopleCategoryId, true);

      $str .= "\n<form action='$gPeopleUrl/display.php' method='post'>";
      $str .= "\n<div class='people_list_category_selector'>";
      $str .= "\n" . $this->websiteText[0] . " $strSelect";
      $str .= "\n</div>";
      $str .= "\n</form>";
    }

    $peoples = array();
    if ($peopleCategoryId > 0) {
      // Get the people of the selected category
      $peoples = $this->peopleUtils->selectByCategoryId($peopleCategoryId);
    } else {
      // Get the all the people
      $peoples = $this->peopleUtils->selectAll();
    }

    if ($gIsPhoneClient) {
      $hideProfile = $this->preferenceUtils->getValue("PEOPLE_PHONE_HIDE_PROFILE");
      $hideEmail = $this->preferenceUtils->getValue("PEOPLE_PHONE_HIDE_EMAIL");
    } else {
      $hideProfile = $this->preferenceUtils->getValue("PEOPLE_HIDE_PROFILE");
      $hideEmail = $this->preferenceUtils->getValue("PEOPLE_HIDE_EMAIL");
    }

    $peopleList = array();
    for ($i = 0; $i < count($peoples); $i++) {
      $people = $peoples[$i];

      $peopleList[$i] = "\n<div class='people_list_person'>";

      $peopleList[$i] .= "\n<div class='people_list_image'>"
        . $this->peopleUtils->renderThumbnail($people)
        . "</div>";

      $peopleList[$i] .= "\n<div class='people_list_name'>"
        . $this->peopleUtils->renderName($people)
        . "</div>";

      if (!$hideProfile) {
        $peopleList[$i] .= "\n<div class='people_list_profile'>"
          . $this->peopleUtils->renderProfile($people)
          . "</div>";
      }

      if (!$hideEmail) {
        $peopleList[$i] .= "\n<div class='people_list_email'>"
          . $this->peopleUtils->renderEmail($people)
          . "</div>";
      }

      $peopleList[$i] .= "</div>";
    }

    // Get the number of people per row
    if ($gIsPhoneClient) {
      $nbPerRow = 1;
    } else {
      $nbPerRow = $this->preferenceUtils->getValue("PEOPLE_NB_PER_ROW");
    }

    // Make sure there is an upper limit set for the loop
    // Otherwise this can crash the web server services
    if (!$nbPerRow) {
      $nbPerRow = $this->nbPerRow;
    }

    $str .= "\n<table border='0' width='100%' cellpadding='0' cellspacing='0'>";

    for ($i = 0; $i < count($peopleList); $i = $i + $nbPerRow) {
      $str .= "\n<tr>";
      for ($j = 0; $j < $nbPerRow; $j++) {
        $str .= "\n<td style='vertical-align:top;'>"
          . LibUtils::getArrayValue($i+$j, $peopleList)
          . "</td>";
      }
      $str .= "\n</tr>";
    }

    $str .= "\n</table>";

    $str .= "\n</div>";

    return($str);
  }

  // Render the styling elements for the editing of the css style properties
  function renderStylingElements() {
    global $gStylingImage;

    $str = "<div class='people_list'>The people"
      . "<div class='people_list_category_selector'>"
      . "Staff category: A category"
      . "</div>"
      . "<div class='people_list_person'>"
      . "<div class='people_list_image'>The photo"
      . "<img class='people_list_image_file' src='$gStylingImage' title='The border of the photo' alt='' />"
      . "</div>"
      . "<div class='people_list_name'>The name</div>"
      . "<div class='people_list_profile'>The profile</div>"
      . "<div class='people_list_email'>The email address</div>"
      . "</div>"
      . "</div>";

    return($str);
  }

}

?>
