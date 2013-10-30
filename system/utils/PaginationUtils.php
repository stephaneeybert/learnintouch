<?

class PaginationUtils {

  var $mlText;

  // The total number of items
  var $listNbItems;

  // The number of items per page
  var $listStep;

  // The current index
  var $listIndex;

  // The set of pagination links
  var $links;

  // The next link
  var $next;

  // The previous link
  var $previous;

  // Some hidden variables
  var $hiddenVariables;

  var $languageUtils;

  function PaginationUtils($listNbItems, $listStep, $listIndex) {
    // Have a default step if none is specified
    if (!$listStep) {
      $listStep = 20;
    }

    $this->listNbItems = $listNbItems;
    $this->listStep = $listStep;
    $this->listIndex = $listIndex;

    $this->hiddenVariables = array();

    $this->loadLanguageTexts();
  }

  function loadLanguageTexts() {
    $this->languageUtils = new LanguageUtils();
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  // Add a hidden variable
  function addHiddenVariable($name, $value) {
    array_push($this->hiddenVariables, array($name, $value));
  }

  // Render the next pagination link
  function renderNextLink() {
    global $PHP_SELF;
    global $gJSNoStatus;

    $str = '';

    // Get the url for the next subset
    $listIndex = $this->listIndex + $this->listStep;
    $nextSubsetUrl = "$PHP_SELF?listIndex=" . $listIndex;

    if (count($this->hiddenVariables) > 0) {
      foreach ($this->hiddenVariables as $hiddenVariable) {
        list($name, $value) = $hiddenVariable;
        $nextSubsetUrl .= "&$name=$value";
      }
    }

    if ($this->listIndex + $this->listStep < $this->listNbItems) {
      $str = "<a href='$nextSubsetUrl' $gJSNoStatus title=''>" . $this->mlText[2] . "</a>";
    }

    return($str);
  }

  // Render the previous pagination link
  function renderPreviousLink() {
    global $PHP_SELF;
    global $gJSNoStatus;

    $str = '';

    // Get the url for the previous subset
    $listIndex = $this->listIndex - $this->listStep;
    $previousSubsetUrl = "$PHP_SELF?listIndex=" . $listIndex;

    if (count($this->hiddenVariables) > 0) {
      foreach ($this->hiddenVariables as $hiddenVariable) {
        list($name, $value) = $hiddenVariable;
        $previousSubsetUrl .= "&$name=$value";
      }
    }

    if ($this->listIndex > 0) {
      $str = "<a href='$previousSubsetUrl' $gJSNoStatus title=''>" . $this->mlText[1] . "</a>";
    }

    return($str);
  }

  // Render the set of pagination links
  function render() {
    global $PHP_SELF;
    global $gJSNoStatus;

    $str = '';

    $linkList = array();
    if ($this->listNbItems > $this->listStep) {
      $k = 0;
      for ($i = 0; $i < $this->listNbItems; $i = $i + $this->listStep) {
        $j = $i + $this->listStep - 1;
        if ($j >= $this->listNbItems) {
          $j = $this->listNbItems - 1;
        }

        $linkList[$i] = "$i - $j";
        $k++;
      }
    }

    if (count($linkList) > 0) {
      $str .= "<form action='$PHP_SELF' method='post'>";
      $str .= $this->renderPreviousLink() . ' ';
      $str .= LibHtml::getSelectList("listIndex", $linkList, $this->listIndex, true);
      if (count($this->hiddenVariables) > 0) {
        foreach ($this->hiddenVariables as $hiddenVariable) {
          list($name, $value) = $hiddenVariable;
          $str .= "<input type='hidden' name='$name' value='$value' />";
        }
      }
      $str .= ' ' . $this->renderNextLink();
      $str .= "</form>";
    }

    return($str);
  }

}

?>
