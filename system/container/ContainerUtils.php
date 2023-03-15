<?

class ContainerUtils extends ContainerDB {

  var $mlText;

  var $currentContainerId;

  var $languageUtils;

  function __construct() {
    parent::__construct();

    $this->init();
  }

  function init() {
    $this->currentContainerId = "containerCurrentContainerId";
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  // Add a container
  function add() {
    $container = new Container();
    $this->insert($container);
    $containerId = $this->getLastInsertId();

    return($containerId);
  }

  // Duplicate a container
  function duplicate($containerId) {
    if ($container = $this->selectById($containerId)) {
      $this->insert($container);
      $duplicatedContainerId = $this->getLastInsertId();

      return($duplicatedContainerId);
    }
  }

  // Get the content of a container in a given language
  function getContentForLanguage($containerId, $languageCode) {
    $content = '';

    if ($container = $this->selectById($containerId)) {
      $content = $this->languageUtils->getTextForLanguage($container->getContent(), $languageCode);
      if (!$content) {
        // If none is found then get it for no specific language
        $content = $this->languageUtils->getTextForLanguage($container->getContent(), '');
        if (!$content) {
          // If none is found then try to get it for the default language
          $languageCode = $this->languageUtils->getDefaultLanguageCode();
          $content = $this->languageUtils->getTextForLanguage($container->getContent(), $languageCode);
        }
      }
    }

    return($content);
  }

  // Set the content of a container in a given language
  function setContentForLanguage($containerId, $languageCode, $content) {
    if ($container = $this->selectById($containerId)) {
      $container->setContent($this->languageUtils->setTextForLanguage($container->getContent(), $languageCode, $content));
      $this->update($container);
    }

    return($content);
  }

  // Update the content of a container
  function addContent($containerId, $content) {
    $languageCode = $this->languageUtils->getCurrentLanguageCode();

    $this->setContentForLanguage($containerId, $languageCode, $content);
  }

  // Get the available languages for the container
  function getAvailableLanguages($containerId, $excludeUsedOnes = false) {
    $this->loadLanguageTexts();

    $languageNames = $this->languageUtils->getActiveLanguageNames();
    $languageNames = array_merge(array('' => $this->mlText[0]), $languageNames);
    // Remove the already used languages
    if ($excludeUsedOnes) {
      if ($container = $this->selectById($containerId)) {
        $content = $container->getContent();
        $languageCodes = $this->languageUtils->getTextLanguageCodes($content);
        foreach ($languageCodes as $languageCode) {
          unset($languageNames[$languageCode]);
        }
      }
    }

    return($languageNames);
  }

  // Render
  function render($containerId) {
    if (!$container = $this->selectById($containerId)) {
      return;
    }

    $languageCode = $this->languageUtils->getCurrentLanguageCode();

    $content = $this->getContentForLanguage($containerId, $languageCode);

    $str = "\n<div class='container'>" . $content . "\n</div>";

    return($str);
  }

  // Render the tags
  // When creating an element in the template system, it is necessary to create its tags
  // A dummy rendering is used for this operation
  function renderTags() {
    $str = "\n<div class='container'></div>";

    return($str);
  }

}

?>
