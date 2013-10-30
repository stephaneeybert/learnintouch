<?

class LanguageUtils extends LanguageDB {

  var $imageFilePath;
  var $imageFileUrl;
  var $imageFileSize;

  // Default language of the system
  var $defaultLanguage;

  // Property names
  var $propertyDefault;
  var $propertyActiveLanguages;
  var $propertyActiveAdminLanguages;

  // Name of the cookie variables
  var $cookieLanguageCode;
  var $cookieAdminLanguageCode;

  // Name of the session variables
  var $sessionLanguageCode;
  var $sessionAdminLanguageCode;

  var $commonUtils;
  var $propertyUtils;
  var $adminUtils;

  function LanguageUtils() {
    $this->LanguageDB();

    $this->init();
  }

  function init() {
    global $gApiPath;
    global $gApiUrl;

    $this->imageFileSize = 20000;
    $this->imageFilePath = $gApiPath . 'image/language/';
    $this->imageFileUrl = $gApiUrl . '/image/language';

    $this->defaultLanguage = 'fr';
    $this->propertyDefault = "LANGUAGE_DEFAULT";
    $this->propertyActiveLanguages = "LANGUAGE_ACTIVE";
    $this->propertyActiveAdminLanguages = "LANGUAGE_ADMIN_ACTIVE";
    $this->cookieLanguageCode = "languageCode";
    $this->cookieAdminLanguageCode = "languageCodeAdmin";
    $this->sessionLanguageCode = "languageCode";
    $this->sessionAdminLanguageCode = "languageCodeAdmin";
  }

  function createDirectories() {
    global $gLanguagePath;
    global $gLanguageUrl;

    if (!is_dir($this->imageFilePath)) {
      if (!is_dir($gDataPath . 'language')) {
        mkdir($gDataPath . 'language');
      }
      mkdir($this->imageFilePath);
      chmod($this->imageFilePath, 0755);
    }
  }

  function loadLanguageTexts() {
    $this->mlText = $this->getMlText(__FILE__);
  }

  // Remove the non referenced files from the directory
  function deleteUnusedImageFiles() {
    $handle = opendir($this->imageFilePath);
    while ($oneFile = readdir($handle)) {
      if ($oneFile != "." && $oneFile != ".." && !strstr($oneFile, '*')) {
        if (!$this->imageIsUsed($oneFile)) {
          $oneFile = str_replace(" ", "\\ ", $oneFile);
          if (@file_exists($filePath . $oneFile)) {
            @unlink($filePath . $oneFile);
          }
        }
      }
    }
    closedir($handle);
  }

  // Check if an image is being used
  function imageIsUsed($image) {
    $isUsed = true;

    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByImage($image)) {
      if ($result->getRowCount() < 1) {
        $isUsed = false;
      }
    }

    return($isUsed);
  }

  // Get the list of languages
  function getLanguages() {
    $list = array();

    $this->loadLanguageTexts();

    $languages = $this->getActiveLanguages();
    foreach ($languages as $language) {
      $languageId = $language->getId();
      $name = $language->getName();

      $list['SYSTEM_PAGE_LANGUAGE' .  $languageId] = $this->mlText[0] . ' ' . $name;
    }

    return($list);
  }

  // Render the image
  function renderImage($languageId) {
    $str = '';

    if ($language = $this->selectById($languageId)) {
      $name = $language->getName();
      $image = $language->getImage();

      $name = ucfirst($name);

      $str = "<img src='$this->imageFileUrl/$image' title='$name' alt='' />";
    }

    return($str);
  }

  // Get the name of the language
  function getLanguageName($languageCode) {

    $name = '';

    if ($languageCode) {
      if ($language = $this->selectByCode($languageCode)) {
        $name = $language->getName();
      }
    }

    return($name);
  }

  // Get the locale of the language
  function getLanguageLocale() {
    $locale = '';

    // Get the default language
    $languageCode = $this->getCurrentLanguageCode();

    if ($languageCode) {
      if ($language = $this->selectByCode($languageCode)) {
        $locale = $language->getLocale();
      }
    }

    return($locale);
  }

  // Get the locale of the admin language
  function getAdminLanguageLocale() {
    $locale = '';

    // Get the default language
    $languageCode = $this->getCurrentAdminLanguageCode();

    if ($languageCode) {
      if ($language = $this->selectByCode($languageCode)) {
        $locale = $language->getLocale();
      }
    }

    return($locale);
  }

  // Check if the locale is the english one
  function isLanguageLocaleEnglish($locale) {
    if (strstr($locale, LANGUAGE_ENGLISH_CODE)) {
      return(true);
    } else {
      return(false);
    }
  }

  // Get the names of the active languages
  function getActiveLanguageNames() {

    $languageNames = array();
    $languages = $this->getActiveLanguages();
    foreach ($languages as $language) {
      $code = $language->getCode();
      $name = $language->getName();
      $languageNames[$code] = $name;
    }

    return($languageNames);
  }

  // Get the names of all the languages
  function getLanguageNames() {

    $languageNames = array();
    $languages = $this->selectAll();
    foreach ($languages as $language) {
      $code = $language->getCode();
      $name = $language->getName();
      $name = ucfirst($name);
      $languageNames[$code] = $name;
    }

    return($languageNames);
  }

  // Get the default language
  function getDefaultLanguageCode() {
    $value = $this->propertyUtils->retrieve($this->propertyDefault);

    // Set the system default language if no default language has been specified
    if (!$value) {
      $value = $this->defaultLanguage;

      $this->setDefaultLanguageCode($value);
    }

    return($value);
  }

  function getCurrentLanguageCode() {
    global $gSiteLanguageDefault;

    // Check if a language has been chosen
    $languageCode = LibSession::getSessionValue(LANGUAGE_SESSION_LANGUAGE_CODE);

    // If no language has yet been chosen
    if (!$languageCode) {
      // Check if a language had already been specified in the past
      $languageCode = LibCookie::getCookie($this->cookieLanguageCode);

      // Check that the language stored in the cookie is still active
      if (!$this->isActiveLanguage($languageCode)) {
        $languageCode = '';
      }

      // If no language had been specified in the past
      if (!$languageCode) {
        // Get the default language
        $languageCode = $this->getDefaultLanguageCode();
      }
    }

    return($languageCode);
  }

  // Set the current language code
  function setCurrentLanguageCode($languageCode, $inCookie = true) {
    $languageCode = trim($languageCode);

    // Store the language in the session
    LibSession::putSessionValue(LANGUAGE_SESSION_LANGUAGE_CODE, $languageCode);

    // Store the language in a cookie
    // Except when the language of the website is being activated from the administrator pannel
    // for multi-languages content editing purposes
    if ($inCookie) {
      LibCookie::putCookie($this->cookieLanguageCode, $languageCode, (60 * 60 * 24 * 360));
    }
  }

  function getCurrentAdminLanguageCode() {
    global $gSiteLanguageDefault;

    // Check if a language has been chosen
    $languageCode = LibSession::getSessionValue(LANGUAGE_SESSION_ADMIN_LANGUAGE_CODE);

    // If no language has yet been chosen
    if (!$languageCode) {
      // Check if a language had already been specified in the past
      $languageCode = LibCookie::getCookie($this->cookieAdminLanguageCode);

      // Check that the language stored in the cookie is still active
      if (!$this->isActiveAdminLanguage($languageCode)) {
        $languageCode = '';
      }

      // If no language had been specified in the past
      if (!$languageCode) {
        $activeLanguages = $this->getActiveAdminLanguageCodes();

        if (count($activeLanguages) > 1) {
          $languageCode = trim($activeLanguages[0]);
          if (!$languageCode) {
            $languageCode = LANGUAGE_ENGLISH_CODE;

            LibCookie::putCookie($this->cookieAdminLanguageCode, $languageCode, (60 * 60 * 24 * 360));
          }
        }
      }

      LibSession::putSessionValue(LANGUAGE_SESSION_ADMIN_LANGUAGE_CODE, $languageCode);
    }

    return($languageCode);
  }

  // Set the current admin language code
  function setCurrentAdminLanguageCode($languageCode) {
    $languageCode = trim($languageCode);

    // Store the language in the session
    LibSession::putSessionValue(LANGUAGE_SESSION_ADMIN_LANGUAGE_CODE, $languageCode);

    // Store the language in a cookie
    LibCookie::putCookie($this->cookieAdminLanguageCode, $languageCode, (60 * 60 * 24 * 360));
  }

  function deactivateAdminLanguage($languageCode) {
    $activeLanguages = explode(LANGUAGE_SEPARATOR, trim($this->propertyUtils->retrieve($this->propertyActiveAdminLanguages)));

    foreach ($activeLanguages as $key => $activeLanguage) {
      if ($activeLanguage == $languageCode) {
        unset($activeLanguages[$key]);
      }
    }

    $this->propertyUtils->store($this->propertyActiveAdminLanguages, join($activeLanguages, LANGUAGE_SEPARATOR));
  }

  function activateAdminLanguage($languageCode) {
    $activeLanguages = $this->propertyUtils->retrieve($this->propertyActiveAdminLanguages);

    if (!strstr($activeLanguages, $languageCode)) {
      $activeLanguages .= LANGUAGE_SEPARATOR . $languageCode;
    }

    $this->propertyUtils->store($this->propertyActiveAdminLanguages, $activeLanguages);
  }

  function getActiveAdminLanguageCodes() {
    $activeLanguages = $this->propertyUtils->retrieve($this->propertyActiveAdminLanguages);

    $languageCodes = explode(LANGUAGE_SEPARATOR, trim($activeLanguages));

    return($languageCodes);
  }

  function isActiveAdminLanguage($languageCode) {
    $activeLanguages = $this->getActiveAdminLanguageCodes();

    if (in_array($languageCode, $activeLanguages)) {
      return(true);
    } else {
      return(false);
    }
  }

  // Count the number of active languages
  function deactivateLanguage($languageCode) {
    $activeLanguages = explode(LANGUAGE_SEPARATOR, trim($this->propertyUtils->retrieve($this->propertyActiveLanguages)));

    foreach ($activeLanguages as $key => $activeLanguage) {
      if ($activeLanguage == $languageCode) {
        unset($activeLanguages[$key]);
      }
    }

    $this->propertyUtils->store($this->propertyActiveLanguages, join($activeLanguages, LANGUAGE_SEPARATOR));
  }

  function activateLanguage($languageCode) {
    $activeLanguages = $this->propertyUtils->retrieve($this->propertyActiveLanguages);

    if (!strstr($activeLanguages, $languageCode)) {
      $activeLanguages .= LANGUAGE_SEPARATOR . $languageCode;
    }

    $activeLanguages = trim($activeLanguages);

    $this->propertyUtils->store($this->propertyActiveLanguages, $activeLanguages);
  }

  function getActiveLanguageCodes() {
    $activeLanguages = $this->propertyUtils->retrieve($this->propertyActiveLanguages);

    $languageCodes = explode(LANGUAGE_SEPARATOR, trim($activeLanguages));

    return($languageCodes);
  }

  function isActiveLanguage($languageCode) {
    $activeLanguages = $this->getActiveLanguageCodes();

    if (in_array($languageCode, $activeLanguages)) {
      return(true);
    } else {
      return(false);
    }
  }

  // Count the number of active languages
  function countActiveLanguages() {
    $activeLanguageList = array();

    $nbLanguages = count($this->getActiveLanguageCodes());

    return($nbLanguages);
  }

  // Get the active languages
  function getActiveLanguages() {
    $activeLanguageList = array();

    $activeLanguages = $this->getActiveLanguageCodes();

    $languages = $this->selectAll();
    foreach ($languages as $language) {
      $code = $language->getCode();
      if (in_array($code, $activeLanguages)) {
        array_push($activeLanguageList, $language);
      }
    }

    return($activeLanguageList);
  }

  function setDefaultLanguageCode($languageCode) {
    $languageCode = trim($languageCode);

    // Activate the language if it is not yet activated
    $this->activateLanguage($languageCode);

    // Set the language as default one
    $this->propertyUtils->store($this->propertyDefault, $languageCode);
  }

  // Get the texts in the current website language
  function getWebsiteText($page, $htmlConvert = true) {
    global $gCurrentLanguageCode;

    return($this->getText($page, $gCurrentLanguageCode, $htmlConvert));
  }

  // Get the texts in the current language
  function getMlText($page, $htmlConvert = true) {
    global $gCurrentAdminLanguageCode;

    return($this->getText($page, $gCurrentAdminLanguageCode, $htmlConvert));
  }

  // Get the text
  // The text files are stored in the same directory as their page file
  function getText($page, $languageCode, $htmlConvert = true) {
    // Get the name and path of the current page
    $pageBasename = basename($page);
    $pagePath = dirname($page);

    // Get the name of the current page excluding the path and the .php suffix
    $pageName = '.' . substr($pageBasename, 0, strlen($pageBasename) - 4);

    // Construct the name of the message file
    $pageMessageFile = $pagePath . '/' . $pageName . '.' . $languageCode . '.php';

    // Construct the name of the message file using the english language
    // Used only if the resource file for the chosen language is not found
    $pageMessageFileEnglish = $pagePath . '/' . $pageName . '.en.php';

    if (@file_exists($pageMessageFile)) {
      $mlText = $this->getMlTextStrings($pageMessageFile, $htmlConvert);

      // Add the english version for the missing array values
      // This is needed if new array values are added after the language
      // file has already been translated
      $mlTextEnglish = $this->getMlTextStrings($pageMessageFileEnglish, $htmlConvert);
      foreach ($mlTextEnglish as $key => $value) {
        if (!isset($mlText[$key])) {
          $mlText[$key] = $mlTextEnglish[$key];
        }
      }
    } else if (@file_exists($pageMessageFileEnglish)) {
      $mlText = $this->getMlTextStrings($pageMessageFileEnglish, $htmlConvert);
    } else {
      $mlText = array();
    }

    return($mlText);
  }

  // Get the text message strings from the file
  function getMlTextStrings($languageFile, $htmlConvert = true) {
    $mlText = array();

    if (@is_file($languageFile)) {
      $lines = @file($languageFile);
      foreach ($lines as $line) {
        $key = trim(substr($line, 1, 4));
        $value = substr($line, 6, strlen($line) - 6);

        // Remove the trailing line break
        $value = preg_replace("/(\r\n|\n|\r)$/iU", '', $value);

        if ($htmlConvert) {
          // Transform the line break characters
          if (strstr($value, '\n')) {
            $value = str_replace('\n', '<br />', $value);
          }

          // Transform the single quotes
          if (strstr($value, '\'')) {
            $value = str_replace('\'', '&#039;', $value);
          }
        }

        $mlText[$key] = $value;
      }
    }

    return($mlText);
  }

  // Set the text message strings from the file
  function setMlTextStrings($languageFile, $mlText) {
    if (count($mlText) > 0) {
      $fp = @fopen($languageFile, 'w');
      foreach ($mlText as $key => $value) {
        $key = vsprintf("[%4s]",  $key);
        $value = str_replace("\r\n", "\\n", $value);
        $value = str_replace("\n", "\\n", $value);
        $value = str_replace("\r", "\\n", $value);
        @fwrite($fp, "$key$value\n");
      }
      @fclose($fp);
    }
  }

  // Get the filenames of the language files
  function getLanguageFilenames($languageCode, $currentDirectory = '') {
    global $gEnginePath;

    $languageFiles = array();

    if (!$currentDirectory) {
      $currentDirectory = $gEnginePath;
    }

    // Add a trailing slash to the current directory if none
    $currentDirectory = LibString::addTraillingSlash($currentDirectory);

    $dirs = LibDir::getDirNames($currentDirectory);

    foreach ($dirs as $dir) {
      if ($dir == "." || $dir == "..") {
        continue;
      }

      $nextDirectory = $currentDirectory . $dir;

      $subLanguageFiles = $this->getLanguageFilenames($languageCode, $nextDirectory);
      $languageFiles = array_merge($languageFiles, $subLanguageFiles);
    }

    // Get the files
    $allFiles = LibDir::getFileNames($currentDirectory);
    foreach ($allFiles as $file) {
      if (substr($file, 0, 1) == ".") {
        if (!strstr($file, '..') && strstr($file, '.en.php')) {
          $nameBits = explode(".", $file);
          $languageFile = "." . $nameBits[1] . ".$languageCode.php";
          if (@is_file("$currentDirectory$languageFile")) {
            array_push($languageFiles, $currentDirectory . $languageFile);
          }
        }
      }
    }

    return($languageFiles);
  }

  // Render the flag bar for the website languages
  function renderWebsiteLanguageBar() {
    $languageCodes = array();

    $languages = $this->selectAll();

    foreach ($languages as $language) {
      $code = $language->getCode();

      if ($this->isActiveLanguage($code)) {
        array_push($languageCodes, $code);
      }
    }

    $str = $this->renderBar($languageCodes, false);

    return($str);
  }

  // Render the flag bar for the website languages
  // indicating the currently selected language
  // and offering the change to another language
  function renderChangeWebsiteLanguageBar($currentLanguageCode) {
    global $gLanguageUrl;
    global $gJSNoStatus;

    $str = <<<HEREDOC
<script type='text/javascript'>
function ajaxChangeWebsiteLanguage(languageCode, previousLanguageCode) {
  // The url must be encoded
  languageCode = encodeURIComponent(languageCode);
  previousLanguageCode = encodeURIComponent(previousLanguageCode);
  var url = '$gLanguageUrl/changeWebsiteLanguage.php?languageCode='+languageCode+'&previousLanguageCode='+previousLanguageCode;
  ajaxAsynchronousRequest(url, refreshChangeWebsiteLanguage);
}

function refreshChangeWebsiteLanguage(responseText) {
  var response = eval('(' + responseText + ')');
  var languageCode = response.languageCode;
  if (languageCode) {
    var wLanguageCodes = getElementsByClass('language_current');
    for (i = 0; i < wLanguageCodes.length; i++) {
      var wLanguageCode = wLanguageCodes[i];
      var previousLanguageCode = wLanguageCode.innerHTML;
      wLanguageCode.innerHTML = languageCode;
    }

    var previousLanguageImages = getElementsByClass('language_image_' + previousLanguageCode);
    for (i = 0; i < previousLanguageImages.length; i++) {
      var previousLanguageImage = previousLanguageImages[i];
      previousLanguageImage.style.borderStyle = "none";
      previousLanguageImage.style.paddingBottom = "5px";
    }

    var languageImages = getElementsByClass('language_image_' + languageCode);
    for (i = 0; i < languageImages.length; i++) {
      var languageImage = languageImages[i];
      languageImage.style.borderStyle = "solid";
      languageImage.style.borderBottomWidth = "2px";
      languageImage.style.borderColor = "green";
      languageImage.style.paddingBottom = "3px";
    }

    changeWebsiteLanguage(languageCode);
  }
}

// Implement this callback with the required specific functionality
// The post process function name can also be specified
//  function changeWebsiteLanguage(languageCode) {
//  }
</script>
HEREDOC;

    $languages = $this->selectAll();

    // Hidden element to keep the current language
    $str .= "<span class='language_current' style='display:none;'>$currentLanguageCode</span>";

    foreach ($languages as $language) {
      $code = $language->getCode();
      if ($this->isActiveLanguage($code)) {
        $name = $language->getName();
        $image = $language->getImage();
        $name = ucfirst($name);

        if ($code == $currentLanguageCode) {
          $style = 'border-style:solid; border-bottom-width:2px; border-color:green; padding-bottom:3px;';
        } else {
          $style = 'padding-bottom:5px;';
        }

        $str .= "<a href='javascript:void(0);' onclick=\"ajaxChangeWebsiteLanguage('$code', '$currentLanguageCode');\" $gJSNoStatus>"
          . "<img class='language_image_$code' src='$this->imageFileUrl/$image' title='$name' alt='' style='$style'/>"
          . "</a> ";
      }
    }

    return($str);
  }

  // Render the flag bar for the administration languages
  function renderAdminLanguageBar() {
    $languageCodes = array();

    $languages = $this->selectAll();

    foreach ($languages as $language) {
      $code = $language->getCode();

      if ($this->isActiveAdminLanguage($code)) {
        array_push($languageCodes, $code);
      }
    }

    $str = $this->renderBar($languageCodes, true);

    return($str);
  }

  // Render the flag of a language
  function renderLanguageFlag($languageCode) {
    global $gLanguageUrl;
    global $gJSNoStatus;

    $str = '';

    if ($language = $this->selectByCode($languageCode)) {
      $name = $language->getName();
      $image = $language->getImage();

      $name = ucfirst($name);

      $str = "\n<span class='language_item'>" . "<img src='$this->imageFileUrl/$image' class='language_item_img' title='$name' alt='' />" . "</span>";
    }

    return($str);
  }

  // Render the flag bar
  function renderBar($languageCodes, $isAdmin) {
    global $gLanguageUrl;
    global $gJSNoStatus;

    $str = '';

    $str .= "<div class='language'>";

    $languageNames = array();
    $languages = $this->selectAll();

    foreach ($languages as $language) {
      $code = $language->getCode();

      if (!in_array($code, $languageCodes)) {
        continue;
      }

      $name = $language->getName();
      $image = $language->getImage();

      $name = ucfirst($name);

      $url = "$gLanguageUrl/store.php?currentLanguageCode=" . urlencode($code);
      if ($isAdmin) {
        $url .= "&amp;isAdmin=$isAdmin";
      }

      $str .= "\n<span class='language_item'>"
        . "<a href='$url' $gJSNoStatus>"
        . "<img src='$this->imageFileUrl/$image' class='language_item_img' title='$name' alt='' />"
        . "</a>"
        . "</span>";
    }

    $str .= "</div>";

    return($str);
  }

  // Render the url used to select a language
  function renderLanguageUrl($languageId) {
    global $gLanguageUrl;

    $url = '';

    if ($language = $this->selectById($languageId)) {
      $code = $language->getCode();
      if ($this->isActiveLanguage($code)) {
        $url = "$gLanguageUrl/store.php?currentLanguageCode=" . urlencode($code);
      }
    }

    return($url);
  }

  // Render the tags
  // When creating an element in the template system, it is necessary to create its tags
  // A dummy rendering is used for this operation
  function renderTags() {
    $str = "\n<div class='language'>";
    $str .= "<div class='language_item'></div>";
    $str .= "<div class='language_item_img'></div>";
    $str .= "\n</div>";

    return($str);
  }

  // Get the text for the given language
  function getTextForLanguage($strMlText, $languageCode) {
    if (!strstr($strMlText, LANGUAGE_MLTEXT_SEPARATOR)) {
      return($strMlText);
    }
    $mlTexts = explode(LANGUAGE_MLTEXT_SEPARATOR . LANGUAGE_MLTEXT_SEPARATOR, $strMlText);
    foreach ($mlTexts as $mlText) {
      if (strstr($mlText, LANGUAGE_MLTEXT_SEPARATOR)) {
        list($wLanguageCode, $text) = explode(LANGUAGE_MLTEXT_SEPARATOR, $mlText);
        if ($wLanguageCode == $languageCode) {
          return($text);
        }
      }
    }
  }

  // Set the text for the given language
  function setTextForLanguage($strCurrentMlText, $languageCode, $text) {
    $strMlText = '';
    $updated = false;
    // Remove any unlikely separator in the content to prevent conflict with the content separator
    $text = str_replace(LANGUAGE_MLTEXT_SEPARATOR, '', $text);
    // Make sure the content is not empty as it would lead to a confusing ||| series of separators
    if (!$languageCode) {
      $languageCode = ' ';
    }
    if (!$text) {
      $text = ' ';
    }
    $mlTexts = explode(LANGUAGE_MLTEXT_SEPARATOR . LANGUAGE_MLTEXT_SEPARATOR, $strCurrentMlText);
    foreach ($mlTexts as $mlText) {
      if ($mlText) {
        if (strstr($mlText, LANGUAGE_MLTEXT_SEPARATOR)) {
          list($wLanguageCode, $wText) = explode(LANGUAGE_MLTEXT_SEPARATOR, $mlText);
        } else {
          $wLanguageCode = ' ';
          $wText = $mlText;
        }
        if ($wLanguageCode == $languageCode && $text && $languageCode) {
          $updated = true;
          $wText = $text;
        }
        $mlText = $wLanguageCode . LANGUAGE_MLTEXT_SEPARATOR . $wText;
        if ($strMlText) {
          $strMlText .= LANGUAGE_MLTEXT_SEPARATOR . LANGUAGE_MLTEXT_SEPARATOR;
        }
        $strMlText .= $mlText;
      }
    }
    if ($updated == false && $text && $languageCode) {
      $mlText = $languageCode . LANGUAGE_MLTEXT_SEPARATOR . $text;
      if ($strMlText) {
        $strMlText .= LANGUAGE_MLTEXT_SEPARATOR . LANGUAGE_MLTEXT_SEPARATOR;
      }
      $strMlText .= $mlText;
    }

    return($strMlText);
  }

  // Get the languages of a text
  function getTextLanguageCodes($strMlText) {
    $languages = array();

    $mlTexts = explode(LANGUAGE_MLTEXT_SEPARATOR . LANGUAGE_MLTEXT_SEPARATOR, $strMlText);
    foreach ($mlTexts as $mlText) {
      if (strstr($mlText, LANGUAGE_MLTEXT_SEPARATOR)) {
        list($languageCode, $text) = explode(LANGUAGE_MLTEXT_SEPARATOR, $mlText);
        array_push($languages, $languageCode);
      }
    }

    return($languages);
  }

  // Translate a file into a language
  function translateFile($filePath, $toLanguageCode) {
    $fromLanguageCode = 'en';

    $translationFilePath = '';
    $filename = basename($filePath);
    $nameBits = explode(".", $filename);
    if (is_array($nameBits) && count($nameBits) > 0) {
      $translationFilePath = dirname($filePath) . '/.' . $nameBits[0] . '.' . $toLanguageCode . '.php';
    }

    if (is_file($filePath) && $translationFilePath) {
      $mlText = $this->getText($filePath, $fromLanguageCode);

      $translations = $this->getMlTextStrings($translationFilePath, false);

      foreach ($mlText as $key => $text) {
        // Try to simulate user interaction to prevent Google denial of service
        $seconds = rand(5, 35);
        sleep($seconds);

        $translation = $this->commonUtils->getGoogleTextTranslation($text, $fromLanguageCode, $toLanguageCode);

        if ($translation) {
          $translations[$key] = $translation;
        }
      }

      $this->setMlTextStrings($translationFilePath, $translations);
    }
  }

  function renderTranslateLanguageResource() {
    global $gLanguageUrl;
    global $gJSNoStatus;
    global $SCRIPT_FILENAME;

    $toLanguageCode = $this->adminUtils->getTranslateLanguageCodes();
    if ($toLanguageCode) {
      $languageFlag = $this->renderLanguageFlag($toLanguageCode);  
      $toLanguageCode = urlencode($toLanguageCode);
      $filePath = urlencode($SCRIPT_FILENAME);
      $str = "<a onclick=\"window.open(this.href, '_blank'); return(false);\" href='$gLanguageUrl/translate.php?toLanguageCode=$toLanguageCode&filePath=$filePath' $gJSNoStatus>$languageFlag</a>";
      return($str);
    }
  }

}

?>
