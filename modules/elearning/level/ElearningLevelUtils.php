<?

class ElearningLevelUtils extends ElearningLevelDB {

  var $websiteText;

  var $languageUtils;

  function __construct() {
    parent::__construct();
  }

  function loadLanguageTexts() {
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  function render($elearningLevelId) {
    $this->loadLanguageTexts();

    $str = '';

    if ($level = $this->selectById($elearningLevelId)) {
      $name = $level->getName();
      $description = $level->getDescription();
      $label = $this->websiteText[0];
      $str = "<div class='elearning_level' title='$description'><span class='elearning_level_labelled_name'>$label <span class='elearning_level_name'>$name</span></span></div>";
    }

    return($str);
  }

}

?>
