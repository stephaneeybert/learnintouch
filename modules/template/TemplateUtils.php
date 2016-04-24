<?php

class TemplateUtils {

  var $mlText;
  var $websiteText;

  var $modelPath;
  var $modelUrl;

  var $imageSize;
  var $imagePath;
  var $imageUrl;

  // The name of the favicon file
  var $faviconFilename;

  // The system pages are all the pages hard coded into the application
  var $systemPages;

  // The browsers running on mobile phone agents
  var $userAgents;

  // The current model
  var $currentModelId;

  // Name of the cookie holding the phone agent
  var $cookieClientIsMobilePhone;

  // Name of the cookie holding the time for which to keep the model id for the mobile phone
  var $cookieMobilePhoneDuration;

  // The instance system pages are the pages that require an object id passed to them
  var $instanceSystemPages;

  var $propertyRefreshCache;
  var $propertyComputerEntryModel;
  var $propertyPhoneEntryModel;
  var $propertyComputerDefaultModel;
  var $propertyPhoneDefaultModel;
  var $propertyComputerEntryPage;
  var $propertyPhoneEntryPage;

  var $preferences;

  var $languageUtils;
  var $preferenceUtils;
  var $commonUtils;
  var $templateModelUtils;
  var $websiteUtils;
  var $dynpageUtils;
  var $userUtils;
  var $documentUtils;
  var $newsPublicationUtils;
  var $newsPaperUtils;
  var $formUtils;
  var $elearningExerciseUtils;
  var $elearningLessonUtils;
  var $documentCategoryUtils;
  var $shopCategoryUtils;
  var $photoAlbumUtils;
  var $linkCategoryUtils;
  var $peopleCategoryUtils;
  var $propertyUtils;
  var $templatePageUtils;
  var $profileUtils;
  var $clockUtils;
  var $navmenuUtils;
  var $adminUtils;
  var $mailAddressUtils;
  var $clientUtils;
  var $smsNumberUtils;
  var $templateElementLanguageUtils;
  var $rssFeedUtils;

  function TemplateUtils() {
    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;
    global $gTemplatePath;

    $this->modelPath = $gDataPath . 'template/model/html/';
    $this->modelUrl = $gDataUrl . '/template/model/html';

    $this->imageSize = 200000;
    $this->imagePath = $this->modelPath . 'image/';
    $this->imageUrl = $this->modelUrl . '/image';

    $this->cookieClientIsMobilePhone = "isPhoneMobileClient";
    $this->cookieMobilePhoneDuration = 60 * 60 * 24 * 365;
    $this->cookieDuration = 60 * 60 * 24 * 365;
    $this->faviconFilename = 'favicon.ico';

    $this->instanceSystemPages = array(
      'SYSTEM_PAGE_DYNPAGE',
      'SYSTEM_PAGE_NEWSPUBLICATION',
      'SYSTEM_PAGE_NEWSPAPER_LIST',
      'SYSTEM_PAGE_NEWSPAPER',
      'SYSTEM_PAGE_NEWSSTORY',
      'SYSTEM_PAGE_NEWSSTORY_IMAGES',
      'SYSTEM_PAGE_FORM',
      'SYSTEM_PAGE_PHOTO_ITEM',
      'SYSTEM_PAGE_SHOP_ITEM',
      'SYSTEM_PAGE_PHOTO_CYCLE',
      'SYSTEM_PAGE_PEOPLE_ITEM',
      'SYSTEM_PAGE_ELEARNING_EXERCISE',
      'SYSTEM_PAGE_ELEARNING_LESSON',
      'SYSTEM_PAGE_ELEARNING_RESULT',
    );

    $this->propertyRefreshCache = "TEMPLATE_REFRESH_CACHE";
    $this->propertyComputerEntryModel = "TEMPLATE_COMPUTER_ENTRY_MODEL";
    $this->propertyPhoneEntryModel = "TEMPLATE_PHONE_ENTRY_MODEL";
    $this->propertyComputerDefaultModel = "TEMPLATE_COMPUTER_DEFAULT_MODEL";
    $this->propertyPhoneDefaultModel = "TEMPLATE_PHONE_DEFAULT_MODEL";
    $this->propertyComputerEntryPage = "TEMPLATE_COMPUTER_ENTRY_PAGE_";
    $this->propertyPhoneEntryPage = "TEMPLATE_PHONE_ENTRY_PAGE_";
    $this->propertyEntryPage = "TEMPLATE_ENTRY_PAGE_";

    $this->userAgents = array();
    $userAgentFile = $gTemplatePath . "mobileUserAgents.txt";
    if (is_file($userAgentFile)) {
      $lines = file($userAgentFile);
      if (count($lines) > 0) {
        foreach ($lines as $line) {
          $line = LibString::stripLineBreaks($line);
          if (isset($line)) {
            array_push($this->userAgents, $line);
          }
        }
      }
    }
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;
    global $gTemplatePath;

    if (!is_dir($this->modelPath)) {
      if (!is_dir($gDataPath . 'template')) {
        mkdir($gDataPath . 'template');
      }
      if (!is_dir($gDataPath . 'template/model')) {
        mkdir($gDataPath . 'template/model');
      }
      mkdir($this->modelPath);
      chmod($this->modelPath, 0755);
      if (!is_dir($this->imagePath)) {
        mkdir($this->imagePath);
        chmod($this->imagePath, 0755);
      }
    }
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  function loadSystemPages() {
    $this->loadLanguageTexts();

    $this->systemPages = array(
      'SYSTEM_PAGE_CLIENT_LIST' =>
      array('MODULE_CLIENT', $this->mlText[11]),
      'SYSTEM_PAGE_CLIENT_CYCLE' =>
      array('MODULE_CLIENT', $this->mlText[6]),
      'SYSTEM_PAGE_LINK_LIST' =>
      array('MODULE_LINK', $this->mlText[14]),
            'SYSTEM_PAGE_LINK_CYCLE' =>
            array('MODULE_LINK', $this->mlText[8]),
              'SYSTEM_PAGE_LANGUAGE' =>
              array('MODULE_LANGUAGE', $this->mlText[31]),
                'SYSTEM_PAGE_GUESTBOOK_LIST' =>
                array('MODULE_GUESTBOOK', $this->mlText[12]),
                  'SYSTEM_PAGE_GUESTBOOK_POST' =>
                  array('MODULE_GUESTBOOK', $this->mlText[25]),
                    'SYSTEM_PAGE_PHOTO_ALBUM_LIST' =>
                    array('MODULE_PHOTO', $this->mlText[42]),
                      'SYSTEM_PAGE_PHOTO_LIST' =>
                      array('MODULE_PHOTO', $this->mlText[17]),
                        'SYSTEM_PAGE_PHOTO_CYCLE' =>
                        array('MODULE_PHOTO', $this->mlText[9]),
                          'SYSTEM_PAGE_PHOTO_ITEM' =>
                          array('MODULE_PHOTO', $this->mlText[7]),
                            'SYSTEM_PAGE_PHOTO_SEARCH' =>
                            array('MODULE_PHOTO', $this->mlText[16]),
                              'SYSTEM_PAGE_PEOPLE_LIST' =>
                              array('MODULE_PEOPLE', $this->mlText[13]),
                                'SYSTEM_PAGE_PEOPLE_ITEM' =>
                                array('MODULE_PEOPLE', $this->mlText[3]),
                                  'SYSTEM_PAGE_DOCUMENT' =>
                                  array('MODULE_DOCUMENT', $this->mlText[18]),
                                  'SYSTEM_PAGE_DOCUMENT_LIST' =>
                                  array('MODULE_DOCUMENT', $this->mlText[2]),
                                    'SYSTEM_PAGE_NEWSPUBLICATION_LIST' =>
                                    array('MODULE_NEWS', $this->mlText[29]),
                                      'SYSTEM_PAGE_NEWSPUBLICATION' =>
                                      array('MODULE_NEWS', $this->mlText[32]),
                                        'SYSTEM_PAGE_NEWSPAPER_LIST' =>
                                        array('MODULE_NEWS', $this->mlText[30]),
                                          'SYSTEM_PAGE_NEWSPAPER' =>
                                          array('MODULE_NEWS', $this->mlText[1]),
                                            'SYSTEM_PAGE_NEWSSTORY' =>
                                            array('MODULE_NEWS', $this->mlText[4]),
                                              'SYSTEM_PAGE_FORM' =>
                                              array('MODULE_FORM', $this->mlText[43]),
                                                'SYSTEM_PAGE_ELEARNING_ASSIGNMENTS' =>
                                                array('OPTION_ELEARNING', $this->mlText[56]),
                                                'SYSTEM_PAGE_ELEARNING_SUBSCRIPTIONS' =>
                                                array('OPTION_ELEARNING', $this->mlText[57]),
                                                  'SYSTEM_PAGE_ELEARNING_EXERCISE' =>
                                                  array('OPTION_ELEARNING', $this->mlText[22]),
                                                    'SYSTEM_PAGE_ELEARNING_LESSON' =>
                                                    array('OPTION_ELEARNING', $this->mlText[55]),
                                                      'SYSTEM_PAGE_ELEARNING_RESULT' =>
                                                      array('OPTION_ELEARNING', $this->mlText[44]),
                                                        'SYSTEM_PAGE_ELEARNING_LIST_TEACHERS' =>
                                                        array('OPTION_ELEARNING', $this->mlText[38]),
                                                        'SYSTEM_PAGE_ELEARNING_PARTICIPANTS' =>
                                                        array('OPTION_ELEARNING_STORE', $this->mlText[54]),
                                                          'SYSTEM_PAGE_ELEARNING_LIST_EXERCISES' =>
                                                          array('OPTION_ELEARNING_STORE', $this->mlText[66]),
                                                            'SYSTEM_PAGE_ELEARNING_LIST_LESSONS' =>
                                                            array('OPTION_ELEARNING_STORE', $this->mlText[67]),
                                                              'SYSTEM_PAGE_ELEARNING_TEACHER_CORNER' =>
                                                              array('OPTION_ELEARNING_STORE', $this->mlText[20]),
                                                                'SYSTEM_PAGE_SEARCH' =>
                                                                array('OPTION_SHOP', $this->mlText[33]),
                                                                  'SYSTEM_PAGE_SHOP_CATEGORY_LIST' =>
                                                                  array('OPTION_SHOP', $this->mlText[34]),
                                                                    'SYSTEM_PAGE_SHOP_ITEM' =>
                                                                    array('OPTION_SHOP', $this->mlText[35]),
                                                                      'SYSTEM_PAGE_SHOP_ORDER_LIST' =>
                                                                      array('OPTION_SHOP', $this->mlText[36]),
                                                                        'SYSTEM_PAGE_SHOP_CART' =>
                                                                        array('OPTION_SHOP', $this->mlText[39]),
                                                                          'SYSTEM_PAGE_SHOP_SEARCH' =>
                                                                          array('OPTION_SHOP', $this->mlText[40]),
                                                                            'SYSTEM_PAGE_SHOP_SELECTION' =>
                                                                            array('OPTION_SHOP', $this->mlText[41]),
                                                                              'SYSTEM_PAGE_CONTACT_POST' =>
                                                                              array('MODULE_CONTACT', $this->mlText[19]),
                                                                                'SYSTEM_PAGE_USER_PROFILE' =>
                                                                                array('MODULE_USER', $this->mlText[21]),
                                                                                  'SYSTEM_PAGE_USER_LOGIN' =>
                                                                                  array('MODULE_USER', $this->mlText[23]),
                                                                                    'SYSTEM_PAGE_USER_LOGOUT' =>
                                                                                    array('MODULE_USER', $this->mlText[28]),
                                                                                      'SYSTEM_PAGE_USER_GET_PASSWORD' =>
                                                                                      array('MODULE_USER', $this->mlText[37]),
                                                                                        'SYSTEM_PAGE_USER_CHANGE_PASSWORD' =>
                                                                                        array('MODULE_USER', $this->mlText[24]),
                                                                                          'SYSTEM_PAGE_USER_REGISTER' =>
                                                                                          array('MODULE_USER', $this->mlText[27]),
                                                                                            'SYSTEM_PAGE_USER_UNSUBSCRIBE' =>
                                                                                            array('MODULE_USER', $this->mlText[26]),
                                                                                              'SYSTEM_PAGE_POST_LOGIN_PAGE' =>
                                                                                              array('MODULE_USER', $this->mlText[46]),
                                                                                                'SYSTEM_PAGE_INVITER' =>
                                                                                                array('MODULE_SMS', $this->mlText[64]),
                                                                                                  'SYSTEM_PAGE_TERMS_OF_SERVICE' =>
                                                                                                  array('MODULE_USER', $this->mlText[58]),
                                                                                                    'SYSTEM_PAGE_MAIL_REGISTER' =>
                                                                                                    array('MODULE_MAIL', $this->mlText[10]),
                                                                                                      'SYSTEM_PAGE_SMS_REGISTER' =>
                                                                                                      array('MODULE_SMS', $this->mlText[15]),
                                                                                                        'SYSTEM_PAGE_DYNPAGE' =>
                                                                                                        array('MODULE_DYNPAGE', $this->mlText[5]),
                                                                                                          'SYSTEM_PAGE_ENTRY_PAGE' =>
                                                                                                          array('MODULE_DYNPAGE', $this->mlText[45]),
                                                                                                          );

  }

  function loadPreferences() {
    $this->loadLanguageTexts();

    $templateModels = $this->templateModelUtils->getAllModels();

    $this->preferences = array(
      "TEMPLATE_POPUP_MODEL" =>
      array($this->mlText[47], $this->mlText[48], PREFERENCE_TYPE_SELECT, $templateModels),
      "TEMPLATE_POPUP_MODEL_ON_PHONE" =>
      array($this->mlText[65], $this->mlText[48], PREFERENCE_TYPE_SELECT, $templateModels),
        "TEMPLATE_PREVIEW_DUMMY_CONTENT" =>
        array($this->mlText[49], $this->mlText[51], PREFERENCE_TYPE_MLTEXT, ''),
        );

    $this->preferenceUtils->init($this->preferences);
  }

  function getPopupTemplateModel() {
    global $gIsPhoneClient;

    if ($gIsPhoneClient) {
      $templateModelId = $this->preferenceUtils->getValue("TEMPLATE_POPUP_MODEL_ON_PHONE");
    } else {
      $templateModelId = $this->preferenceUtils->getValue("TEMPLATE_POPUP_MODEL");
    }

    return($templateModelId);
  }

  // Validate the requested url
  function validateRequestedUrl() {
    global $gTemplateUrl;

    if (isset($_GET['PHPSESSID'])) {
      if (preg_match('/[0-f]/i', $_GET['PHPSESSID'])) {
        $str = LibHtml::urlRedirect("$gTemplateUrl/invalid.php");
        printContent($str);
        exit;
      }
    }
  }

  // Check if the 
  function isBaseUrl($url) {
    global $gHomeUrl;

    if (LibString::stripTraillingSlash($url) == $gHomeUrl) {
      $isBaseUrl = true;
    } else {
      $isBaseUrl = false;
    }

    return($isBaseUrl);
  }

  // Store the requested url
  function storeRequestedUrl($url = '') {
    global $REQUEST_URI;

    if (!$url) {
      $url = $REQUEST_URI;
    }

    LibCookie::putCookie(TEMPLATE_SESSION_REQUESTED_URL, $url, 60);
  }

  // Retrieve the requested url
  function retrieveRequestedUrl() {
    return(LibCookie::getCookie(TEMPLATE_SESSION_REQUESTED_URL));
  }

  // Get the module of a system page
  function getSystemPageModule($systemPage) {
    $module = $systemPage[0];

    return($module);
  }

  // The system pages are all the pages, with or without the ones that need an object id
  function getSystemPages($withInstancePages = false) {
    $this->loadSystemPages();

    $listSystemPages = array();

    foreach ($this->systemPages as $systemPageId => $systemPage) {
      if ($withInstancePages || !in_array($systemPageId, $this->instanceSystemPages)) {
        $pageDescription = $systemPage[1];
        if ($this->websiteUtils->isCurrentWebsiteModule($systemPage[0])) {
          $listSystemPages[$systemPageId] = $pageDescription;
        } else if ($this->websiteUtils->isCurrentWebsiteOption($systemPage[0])) {
          $listSystemPages[$systemPageId] = $pageDescription;
        }
      }
    }

    return($listSystemPages);
  }

  // Check if a page id is valid
  function isValidPageId($pageId) {
    if (is_numeric($pageId) || substr($pageId, 0, 12) == "SYSTEM_PAGE_") {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if a page is secured
  function isSecuredPage($webpageId) {
    $isSecured = false;

    if ($this->preferenceUtils->getValue("DYNPAGE_SECURED_ACCESS")) {
      // Check if the page is a web page
      if (is_numeric($webpageId)) {
        if ($this->dynpageUtils->isSecured($webpageId)) {
          $isSecured = true;
          return;
        }
      } else {
        $isSecured = $this->userUtils->isSecuredPage($webpageId);
      }
    }

    return($isSecured);
  }

  // Get the template model from a url if any
  function getTemplateModelFromUrl($url) {
    if (strstr($url, 'templateModelId')) {
      $parameters = LibUtils::getUrlParameters($url);
      if (isset($parameters['templateModelId'])) {
        return($parameters['templateModelId']);
      }
    }
  }

  // Render the url of a page
  function renderPageUrl($pageId, $templateModelId = '') {
    global $gTemplateUrl;
    global $gHomeUrl;

    $strUrl = '';

    // The url can take different forms, a system page from the website, a web page from the website
    // an external url pointing to another website or an email address

    // A page can be a dynamic page and its id is a numeric id
    if (is_numeric($pageId)) {
      if ($dynpage = $this->dynpageUtils->selectById($pageId)) {
        $strUrl = "$gTemplateUrl/display.php?pageId=$pageId";
      }
      // Or it can be a system page and its id is constant name plus a numerical id
    } else if (substr($pageId, 0, 11) == 'SYSTEM_PAGE' && preg_match('/[0-9]/', $pageId)) {
      if (substr($pageId, 0, 27) == "SYSTEM_PAGE_NEWSPUBLICATION") {
        $newsPublicationId = substr($pageId, 27, strlen($pageId) - 27);
        $strUrl = "$gTemplateUrl/display.php?pageId=SYSTEM_PAGE_NEWSPUBLICATION&newsPublicationId=$newsPublicationId";
      } else if (substr($pageId, 0, 21) == "SYSTEM_PAGE_NEWSPAPER") {
        $newsPaperId = substr($pageId, 21, strlen($pageId) - 21);
        $strUrl = "$gTemplateUrl/display.php?pageId=SYSTEM_PAGE_NEWSPAPER&newsPaperId=$newsPaperId";
      } else if (substr($pageId, 0, 16) == "SYSTEM_PAGE_FORM") {
        $formId = substr($pageId, 16, strlen($pageId) - 16);
        $strUrl = "$gTemplateUrl/display.php?pageId=SYSTEM_PAGE_FORM&formId=$formId";
      } else if (substr($pageId, 0, 30) == "SYSTEM_PAGE_ELEARNING_EXERCISE") {
        $urlSuffix = substr($pageId, 30, strlen($pageId) - 30);
        if (strstr($urlSuffix, 'ELEARNING_SUBSCRIPTION_ID')) {
          list($elearningExerciseId, $elearningSubscriptionId) = explode('ELEARNING_SUBSCRIPTION_ID', $urlSuffix);
          $strUrl = "$gTemplateUrl/display.php?pageId=SYSTEM_PAGE_ELEARNING_EXERCISE&elearningExerciseId=$elearningExerciseId&elearningSubscriptionId=$elearningSubscriptionId";
        } else {
          $elearningExerciseId = $urlSuffix;
          $strUrl = "$gTemplateUrl/display.php?pageId=SYSTEM_PAGE_ELEARNING_EXERCISE&elearningExerciseId=$elearningExerciseId";
        }
      } else if (substr($pageId, 0, 28) == "SYSTEM_PAGE_ELEARNING_LESSON") {
        $urlSuffix = substr($pageId, 28, strlen($pageId) - 28);
        if (strstr($urlSuffix, 'ELEARNING_SUBSCRIPTION_ID')) {
          list($elearningLessonId, $elearningSubscriptionId) = explode('ELEARNING_SUBSCRIPTION_ID', $urlSuffix);
          $strUrl = "$gTemplateUrl/display.php?pageId=SYSTEM_PAGE_ELEARNING_LESSON&elearningLessonId=$elearningLessonId&elearningSubscriptionId=$elearningSubscriptionId";
        } else {
          $elearningLessonId = $urlSuffix;
          $strUrl = "$gTemplateUrl/display.php?pageId=SYSTEM_PAGE_ELEARNING_LESSON&elearningLessonId=$elearningLessonId";
        }
      } else if (substr($pageId, 0, 28) == "SYSTEM_PAGE_ELEARNING_RESULT") {
        $elearningResultId = substr($pageId, 28, strlen($pageId) - 28);
        $strUrl = "$gTemplateUrl/display.php?pageId=SYSTEM_PAGE_ELEARNING_RESULT&elearningResultId=$elearningResultId";
      } else if (substr($pageId, 0, 25) == "SYSTEM_PAGE_DOCUMENT_LIST") {
        $documentCategoryId = substr($pageId, 25, strlen($pageId) - 25);
        $strUrl = "$gTemplateUrl/display.php?pageId=SYSTEM_PAGE_DOCUMENT_LIST&documentCategoryId=$documentCategoryId";
      } else if (substr($pageId, 0, 20) == "SYSTEM_PAGE_DOCUMENT") {
        $documentId = substr($pageId, 20, strlen($pageId) - 20);
        $strUrl = $this->documentUtils->getDocumentUrl($documentId);
      } else if (substr($pageId, 0, 30) == "SYSTEM_PAGE_SHOP_CATEGORY_LIST") {
        $shopCategoryId = substr($pageId, 30, strlen($pageId) - 30);
        $strUrl = "$gTemplateUrl/display.php?pageId=SYSTEM_PAGE_SHOP_CATEGORY_LIST&shopCategoryId=$shopCategoryId";
      } else if (substr($pageId, 0, 27) == "SYSTEM_PAGE_SHOP_ORDER_LIST") {
        $strUrl = "$gTemplateUrl/display.php?pageId=SYSTEM_PAGE_SHOP_ORDER_LIST";
      } else if (substr($pageId, 0, 28) == "SYSTEM_PAGE_PHOTO_ALBUM_LIST") {
        $strUrl = "$gTemplateUrl/display.php?pageId=SYSTEM_PAGE_PHOTO_ALBUM_LIST";
      } else if (substr($pageId, 0, 22) == "SYSTEM_PAGE_PHOTO_LIST") {
        $photoAlbumId = substr($pageId, 22, strlen($pageId) - 22);
        $strUrl = "$gTemplateUrl/display.php?pageId=SYSTEM_PAGE_PHOTO_LIST&photoAlbumId=$photoAlbumId";
      } else if (substr($pageId, 0, 23) == "SYSTEM_PAGE_PHOTO_CYCLE") {
        $photoAlbumId = substr($pageId, 23, strlen($pageId) - 23);
        $strUrl = "$gTemplateUrl/display.php?pageId=SYSTEM_PAGE_PHOTO_CYCLE&photoAlbumId=$photoAlbumId";
      } else if (substr($pageId, 0, 21) == "SYSTEM_PAGE_LINK_LIST") {
        $linkCategoryId = substr($pageId, 21, strlen($pageId) - 21);
        $strUrl = "$gTemplateUrl/display.php?pageId=SYSTEM_PAGE_LINK_LIST&linkCategoryId=$linkCategoryId";
      } else if (substr($pageId, 0, 23) == "SYSTEM_PAGE_PEOPLE_LIST") {
        $peopleCategoryId = substr($pageId, 23, strlen($pageId) - 23);
        $strUrl = "$gTemplateUrl/display.php?pageId=SYSTEM_PAGE_PEOPLE_LIST&peopleCategoryId=$peopleCategoryId";
      } else if (substr($pageId, 0, 20) == "SYSTEM_PAGE_LANGUAGE") {
        $languageId = substr($pageId, 20, strlen($pageId) - 20);
        $strUrl = $this->languageUtils->renderLanguageUrl($languageId);
      }
      // Or it can be a system page and its id is constant name only
    } else if (substr($pageId, 0, 11) == "SYSTEM_PAGE") {
      $strUrl = "$gTemplateUrl/display.php?pageId=$pageId";
    } else {
      // An email address url
      if ($pageId && LibEmail::validate($pageId)) {
        $strUrl = "mailto:" . $url;
      } else if ($pageId) {
        // An external website url
        $strUrl = urldecode($pageId);
      } else {
        $strUrl = '';
      }
    }

    if (!$strUrl) {
      $strUrl = $gHomeUrl;
    }

    // If a model id is passed then the model becomes the active model
    if ($strUrl && $templateModelId) {
      // Note that a simple & ampersand is used instead of the xml standard &amp; sequence
      // The use of the xml standard &amp; sequence here bugs the redirection
      $strUrl = LibUtils::addUrlParameter($strUrl, 'templateModelId', $templateModelId);
    }

    return($strUrl);
  }

  // Render the name of a page
  function getPageName($pageId, $templateModelId = '') {
    $name = '';

    // A page can be a dynamic page and its id is a numeric id
    if (is_numeric($pageId)) {
      $name = $this->dynpageUtils->getFolderPath($pageId);
      // Or it can be a system page and its id is constant name plus a numerical id
    } else if (substr($pageId, 0, 11) == 'SYSTEM_PAGE' && preg_match('/[0-9]/', $pageId)) {
      if (substr($pageId, 0, 27) == "SYSTEM_PAGE_NEWSPUBLICATION") {
        $newsPublicationId = substr($pageId, 27, strlen($pageId) - 27);
        if ($newsPublication = $this->newsPublicationUtils->selectById($newsPublicationId)) {
          $name = $this->getSystemPageName(substr($pageId, 0, 27)) . ' : ' . $newsPublication->getName();
        }
      } else if (substr($pageId, 0, 21) == "SYSTEM_PAGE_NEWSPAPER") {
        $newsPaperId = substr($pageId, 21, strlen($pageId) - 21);
        if ($newsPaper = $this->newsPaperUtils->selectById($newsPaperId)) {
          $name = $this->getSystemPageName(substr($pageId, 0, 21)) . ' : ' . $newsPaper->getTitle();
        }
      } else if (substr($pageId, 0, 16) == "SYSTEM_PAGE_FORM") {
        $formId = substr($pageId, 16, strlen($pageId) - 16);
        if ($form = $this->formUtils->selectById($formId)) {
          $name = $this->getSystemPageName(substr($pageId, 0, 16)) . ' : ' . $form->getName();
        }
      } else if (substr($pageId, 0, 30) == "SYSTEM_PAGE_ELEARNING_EXERCISE") {
        $urlSuffix = substr($pageId, 30, strlen($pageId) - 30);
        if (strstr($urlSuffix, 'ELEARNING_SUBSCRIPTION_ID')) {
          list($elearningExerciseId, $elearningSubscriptionId) = explode('ELEARNING_SUBSCRIPTION_ID', $urlSuffix);
        } else {
          $elearningExerciseId = $urlSuffix;
        }
        if ($elearningExercise = $this->elearningExerciseUtils->selectById($elearningExerciseId)) {
          $name = $this->getSystemPageName(substr($pageId, 0, 30)) . ' : ' . $elearningExercise->getName();
        }
      } else if (substr($pageId, 0, 28) == "SYSTEM_PAGE_ELEARNING_LESSON") {
        $urlSuffix = substr($pageId, 28, strlen($pageId) - 28);
        if (strstr($urlSuffix, 'ELEARNING_SUBSCRIPTION_ID')) {
          list($elearningLessonId, $elearningSubscriptionId) = explode('ELEARNING_SUBSCRIPTION_ID', $urlSuffix);
        } else {
          $elearningLessonId = $urlSuffix;
        }
        if ($elearningLesson = $this->elearningLessonUtils->selectById($elearningLessonId)) {
          $name = $this->getSystemPageName(substr($pageId, 0, 28)) . ' : ' . $elearningLesson->getName();
        }
      } else if (substr($pageId, 0, 25) == "SYSTEM_PAGE_DOCUMENT_LIST") {
        $documentCategoryId = substr($pageId, 25, strlen($pageId) - 25);
        if ($documentCategory = $this->documentCategoryUtils->selectById($documentCategoryId)) {
          $name = $this->getSystemPageName(substr($pageId, 0, 25)) . ' : ' . $documentCategory->getName();
        }
      } else if (substr($pageId, 0, 20) == "SYSTEM_PAGE_DOCUMENT") {
        $documentId = substr($pageId, 20, strlen($pageId) - 20);
        if ($document = $this->documentUtils->selectById($documentId)) {
          $name = $this->getSystemPageName(substr($pageId, 0, 20)) . ' : ' . $document->getFile();
        }
      } else if (substr($pageId, 0, 30) == "SYSTEM_PAGE_SHOP_CATEGORY_LIST") {
        $shopCategoryId = substr($pageId, 30, strlen($pageId) - 30);
        if ($shopCategory = $this->shopCategoryUtils->selectById($shopCategoryId)) {
          $name = $this->getSystemPageName(substr($pageId, 0, 30)) . ' : ' . $shopCategory->getName();
        }
      } else if ($pageId == "SYSTEM_PAGE_SHOP_ORDER_LIST") {
        $name = $this->getSystemPageName($pageId);
      } else if ($pageId == "SYSTEM_PAGE_PHOTO_ALBUM_LIST") {
        $name = $this->getSystemPageName($pageId);
      } else if (substr($pageId, 0, 22) == "SYSTEM_PAGE_PHOTO_LIST") {
        $photoAlbumId = substr($pageId, 22, strlen($pageId) - 22);
        if ($photoAlbum = $this->photoAlbumUtils->selectById($photoAlbumId)) {
          $name = $this->getSystemPageName(substr($pageId, 0, 22)) . ' : ' . $photoAlbum->getName();
        }
      } else if (substr($pageId, 0, 23) == "SYSTEM_PAGE_PHOTO_CYCLE") {
        $photoAlbumId = substr($pageId, 23, strlen($pageId) - 23);
        if ($photoAlbum = $this->photoAlbumUtils->selectById($photoAlbumId)) {
          $name = $this->getSystemPageName(substr($pageId, 0, 23)) . ' : ' . $photoAlbum->getName();
        }
      } else if (substr($pageId, 0, 21) == "SYSTEM_PAGE_LINK_LIST") {
        $linkCategoryId = substr($pageId, 21, strlen($pageId) - 21);
        if ($linkCategory = $this->linkCategoryUtils->selectById($linkCategoryId)) {
          $name = $this->getSystemPageName(substr($pageId, 0, 21)) . ' : ' . $linkCategory->getName();
        }
      } else if (substr($pageId, 0, 23) == "SYSTEM_PAGE_PEOPLE_LIST") {
        $peopleCategoryId = substr($pageId, 23, strlen($pageId) - 23);
        if ($peopleCategory = $this->peopleCategoryUtils->selectById($peopleCategoryId)) {
          $name = $this->getSystemPageName(substr($pageId, 0, 23)) . ' : ' . $peopleCategory->getName();
        }
      } else if (substr($pageId, 0, 20) == "SYSTEM_PAGE_LANGUAGE") {
        $languageId = substr($pageId, 20, strlen($pageId) - 20);
        if ($language = $this->languageUtils->selectById($languageId)) {
          $name = $this->getSystemPageName(substr($pageId, 0, 20)) . ' : ' . $language->getName();
        }
      }
      // Or it can be a system page and its id is constant name only
    } else if (substr($pageId, 0, 11) == "SYSTEM_PAGE") {
      $name = $this->getSystemPageName($pageId);
    }

    return($name);
  }

  function getSystemPageName($systemPageId) {
    $pages = $this->getSystemPages(true);

    if (isset($pages[$systemPageId])) {
      $name = $pages[$systemPageId];
    } else {
      $name = '';
    }

    return($name);
  }

  // Set a flag to request the refreshing of the cache file
  function setRefreshCache() {
    $this->propertyUtils->store($this->propertyRefreshCache, time());
  }

  // Render the javascript code used to update the internal link value
  function renderJsUpdate($pageId) {
    $webpageName = $this->getPageName($pageId);
    $webpageName = LibString::decodeHtmlspecialchars($webpageName);
    $webpageName = LibString::escapeQuotes($webpageName);

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

// Set the webpageId field in the model navigation elements
if (form.elements['webpageId']) {
  form.elements['webpageId'].value = '$pageId';
}

// Set the webpageName field in the model navigation elements
if (form.elements['webpageName']) {
  form.elements['webpageName'].value = '$webpageName';
}
</script>
HEREDOC;

    return($str);
  }

  // Render the javascript to refresh the model cache
  function renderCacheRequestJs() {
    global $gTemplateDesignUrl;

    $this->loadLanguageTexts();

    $text52 = $this->mlText[52];
    $text53 = $this->mlText[53];

    $strCacheJs = <<<HEREDOC
<script type='text/javascript'>

// Color the cache message
function updateCacheMessage(templateModelId) {
  templateModelId = parseInt(templateModelId);
  var element = "cacheMessage" + templateModelId;
  document.getElementById(element).innerHTML = "<span id='cacheMessage"+templateModelId+"' style='color:green'>$text52</span>";
  }

// Refresh the css cache file
function cacheCssFile(templateModelId) {
  var element = "cacheMessage" + templateModelId;
  document.getElementById(element).innerHTML = "<span id='cacheMessage"+templateModelId+"' style='color:grey'>$text53</span>";
  ajaxAsynchronousRequest('$gTemplateDesignUrl/model/cache_css.php?templateModelId='+templateModelId, updateCacheMessage);
  }

</script>
HEREDOC;

    return($strCacheJs);
  }

  // Render the message requesting a refresh of the model cache
  function renderCacheRequestMessage($templateModelId) {
    global $gJSNoStatus;
    global $gCommonImagesUrl;
    global $gImageReset;

    $this->loadLanguageTexts();

    $strCacheRequested = "<a href='javascript:cacheCssFile($templateModelId);' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageReset' title='" . $this->mlText[50] . "'> <span id='cacheMessage$templateModelId'>" . $this->mlText[50] . "</span></a>";

    return($strCacheRequested);
  }

  // Check for the need to refresh the cache file
  function mustRefreshCache($templateModelId) {
    $time = $this->propertyUtils->retrieve($this->propertyRefreshCache);
    $modelTime = $this->getModelFileTime($templateModelId);

    // If no cache file exists then refresh the cache
    if (!$modelTime) {
      return(true);
    }

    // If the cache file is too old then refresh it
    if ($time && $time > $modelTime) {
      return(true);
    }

    return(false);
  }

  // Get the dummy content for the model preview
  function getModelPreviewDummyContent() {
    $str = $this->preferenceUtils->getValue("TEMPLATE_PREVIEW_DUMMY_CONTENT");

    return($str);
  }

  // Refresh the cache if needed
  function checkRefreshCache($templateModelId) {
    if ($this->mustRefreshCache($templateModelId)) {
      $this->cacheModelFile($templateModelId);
    }
  }

  // Refresh the cache file for the model
  function cacheModelFile($templateModelId) {
    $currentLanguageCode = $this->languageUtils->getCurrentLanguageCode();

    $activeLanguageCodes = $this->languageUtils->getActiveLanguageCodes();

    foreach ($activeLanguageCodes as $languageCode) {
      $this->languageUtils->setCurrentLanguageCode($languageCode);

      if ($this->templateModelUtils->selectById($templateModelId)) {
        $filename = $this->getModelFilename($templateModelId);
        $str = $this->templateModelUtils->renderWebsiteModel($templateModelId);
        LibFile::writeString($filename, $str);

        $popupTemplateModelId = $this->getPopupTemplateModel();
        $filename = $this->getPopupModelFilename($popupTemplateModelId);
        $str = $this->templateModelUtils->renderWebsiteModel($popupTemplateModelId);
        LibFile::writeString($filename, $str);
      }
    }

    $this->languageUtils->setCurrentLanguageCode($currentLanguageCode);
  }

  // Delete the unused cache files
  function deleteUnusedCacheFiles() {
    $filePath = $this->modelPath;
    $handle = opendir($filePath);
    while ($oneFile = readdir($handle)) {
      if ($oneFile != "." && $oneFile != ".." && !strstr($oneFile, '*') && !is_dir($filePath . $oneFile)) {
        if (!$this->cacheFileIsUsed($oneFile)) {
          $oneFile = str_replace(" ", "\\ ", $oneFile);
          if (file_exists($filePath . $oneFile)) {
            unlink($filePath . $oneFile);
          }
        }
      }
    }
    closedir($handle);
  }

  // Check if a cache file is being used
  function cacheFileIsUsed($file) {
    // Some cache files must not be deleted
    if ($file == 'default.css') {
      return(true);
    }

    // Get the name of the model from the file name
    $bits = explode(".", $file);
    if (count($bits) > 1) {
      $templateModelId = strtolower($bits[0]);

      // Check if there is a model for the file
      if ($templateModel = $this->templateModelUtils->selectById($templateModelId)) {
        $wTemplateModelId = $templateModel->getId();
        if ($wTemplateModelId == $templateModelId) {
          return(true);
        }
      }
    }

    return(false);
  }

  // Get the filename of a cache file for a model
  function getModelFilename($templateModelId) {
    // There is one cached file for each language of each model
    $languageCode = $this->languageUtils->getCurrentLanguageCode();

    $filename = $this->modelPath . $templateModelId . '.' . $languageCode . '.html';

    return($filename);
  }

  // Get the filename of a cache file of all popups for a model
  function getPopupModelFilename($templateModelId) {
    $filename = $this->modelPath . $templateModelId . '.popup.html';

    return($filename);
  }

  // Get the last modification time of a cache file for a model
  function getModelFileTime($templateModelId) {
    $time = '';

    $filename = $this->getModelFilename($templateModelId);

    if (is_file($filename)) {
      $time = filemtime($filename);
    }

    return($time);
  }

  // Get the filename of the css file for the model
  function getModelCssFilename($templateModelId = '') {
    if ($templateModelId) {
      $filename = $templateModelId . '.css';
    }

    return($filename);
  }

  // Get the path to the css file of the model
  function getModelCssPath($templateModelId) {
    $filename = $this->getModelCssFilename($templateModelId);

    return($this->modelPath . $filename);
  }

  // Get the url to the css file of a model
  function getModelCssUrl($templateModelId = '') {
    $filename = $this->getModelCssFilename($templateModelId);

    return($this->modelUrl . '/' . $filename);
  }

  // Set the default model for a rendering on a computer
  function setComputerDefault($templateModelId) {
    // Set the model as default one
    $this->propertyUtils->store($this->propertyComputerDefaultModel, $templateModelId);
  }

  // Get the default model for a rendering on a computer
  function getComputerDefault() {
    $templateModelId = $this->propertyUtils->retrieve($this->propertyComputerDefaultModel);

    return($templateModelId);
  }

  // Set the default model for a rendering on a phone
  function setPhoneDefault($templateModelId) {
    // Set the model as default one
    $this->propertyUtils->store($this->propertyPhoneDefaultModel, $templateModelId);
  }

  // Get the default model for a rendering on a phone
  function getPhoneDefault() {
    $templateModelId = $this->propertyUtils->retrieve($this->propertyPhoneDefaultModel);

    return($templateModelId);
  }

  // Set the entry model for a rendering on a computer
  function setComputerEntry($templateModelId) {
    // Set the model as default one
    $this->propertyUtils->store($this->propertyComputerEntryModel, $templateModelId);
  }

  // Get the entry model for a rendering on a computer
  function getComputerEntry() {
    $templateModelId = $this->propertyUtils->retrieve($this->propertyComputerEntryModel);

    return($templateModelId);
  }

  // Set the entry model for a rendering on a phone
  function setPhoneEntry($templateModelId) {
    // Set the model as default one
    $this->propertyUtils->store($this->propertyPhoneEntryModel, $templateModelId);
  }

  // Get the entry model for a rendering on a phone
  function getPhoneEntry() {
    $templateModelId = $this->propertyUtils->retrieve($this->propertyPhoneEntryModel);

    return($templateModelId);
  }

  // Detect the kind of user agent
  function detectUserAgent() {
    global $HTTP_USER_AGENT;

    $isPhone = false;
    $isTouch = false;

    foreach ($this->userAgents as $userAgent) {
      list($name, $phone, $touch) = explode(':', $userAgent);

      if ($phone && strstr(strtolower($HTTP_USER_AGENT), strtolower($name))) {
        $isPhone = true;
        $isTouch = true;
        break;
      } else if ($touch && strstr(strtolower($HTTP_USER_AGENT), strtolower($name))) {
        $isTouch = true;
        break;
      }
    }

    if ($isPhone) {
      $this->setPhoneClient();
    } else {
      $this->unsetPhoneClient();
    }

    if ($isTouch) {
      $this->setTouchClient();
    } else {
      $this->unsetTouchClient();
    }

    return($isPhone);
  }

  // Get the entry page
  function getEntryPage() {
    $languageCode = $this->languageUtils->getCurrentLanguageCode();

    if ($this->isPhoneClient()) {
      $pageId = $this->getPhoneEntryPage($languageCode);
    } else {
      $pageId = $this->getComputerEntryPage($languageCode);
    }

    if ($pageId) {
      $url = $this->renderPageUrl($pageId);
    } else {
      $url = '';
    }

    return($url);
  }

  // Get the computer entry page for a language
  function getComputerEntryPage($languageCode = '') {
    $entryPage = '';

    if (!$languageCode) {
      $languageCode = $this->languageUtils->getCurrentLanguageCode();
    }

    if ($languageCode) {
      $entryPage = $this->propertyUtils->retrieve($this->propertyComputerEntryPage . $languageCode);
    }

    return($entryPage);
  }

  // Set the computer entry page for a language
  function setComputerEntryPage($languageCode, $entryPage) {
    if ($languageCode) {
      $this->propertyUtils->store($this->propertyComputerEntryPage . $languageCode, $entryPage);
    }
  }

  // Get the phone entry page for a language
  function getPhoneEntryPage($languageCode = '') {
    $entryPage = '';

    if (!$languageCode) {
      $languageCode = $this->languageUtils->getCurrentLanguageCode();
    }

    if ($languageCode) {
      $entryPage = $this->propertyUtils->retrieve($this->propertyPhoneEntryPage . $languageCode);
    }

    return($entryPage);
  }

  // Set the phone entry page for a language
  function setPhoneEntryPage($languageCode, $entryPage) {
    if ($languageCode) {
      $this->propertyUtils->store($this->propertyPhoneEntryPage . $languageCode, $entryPage);
    }
  }

  // Delete the entry pages properties for the non active languages
  function deleteEntryPages() {
    $languages = $this->languageUtils->selectAll();
    foreach ($languages as $language) {
      $languageCode = $language->getCode();
      if (!$this->languageUtils->isActiveLanguage($languageCode)) {
        $this->propertyUtils->delete($this->propertyComputerEntryPage . $languageCode);
        $this->propertyUtils->delete($this->propertyPhoneEntryPage . $languageCode);
      }
    }
  }

  // Retrieve the current model
  function getCurrentModel() {
    $templateModelId = LibSession::getSessionValue(TEMPLATE_SESSION_MODEL);

    if (!$templateModelId) {
      $templateModelId = LibCookie::getCookie(TEMPLATE_SESSION_MODEL);
    }

    return($templateModelId);
  }

  // Store the current model
  function setCurrentModel($templateModelId) {
    LibSession::putSessionValue(TEMPLATE_SESSION_MODEL, $templateModelId);

    LibCookie::putCookie(TEMPLATE_SESSION_MODEL, $templateModelId, $this->cookieDuration);
  }

  // Render a popup
  function renderPopup($content) {
    $templateModelId = $this->getPopupTemplateModel();

    if (!$templateModelId) {
      $templateModelId = $this->getComputerDefault();
    }

    $filename = $this->getPopupModelFilename($templateModelId);

    $str = LibFile::readIntoString($filename);

    $str = str_replace('TEMPLATE_CONTENT_PAGE', $content, $str);

    return($str);
  }

  // Render the common javascripts
  function renderCommonJavascripts() {
    global $gJsUrl;
    global $gTemplateDesignUrl;
    global $gSwfPlayerUrl;
    global $gApiUrl;

    $str = <<<HEREDOC
<script src='$gJsUrl/popup.js' type='text/javascript'></script>
<script src='$gJsUrl/utilities.js' type='text/javascript'></script>
<script src='$gJsUrl/cookies.js' type='text/javascript'></script>
<script src='$gJsUrl/ajax.js' type='text/javascript'></script>
<script src='$gJsUrl/jquery/jquery-1.7.1.min.js' type='text/javascript'></script>
<script type='text/javascript' src='$gJsUrl/jquery/datepicker/language/jquery.ui.datepicker-da.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/datepicker/language/jquery.ui.datepicker-de.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/datepicker/language/jquery.ui.datepicker-en-GB.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/datepicker/language/jquery.ui.datepicker-es.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/datepicker/language/jquery.ui.datepicker-fi.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/datepicker/language/jquery.ui.datepicker-fr.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/datepicker/language/jquery.ui.datepicker-it.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/datepicker/language/jquery.ui.datepicker-nl.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/datepicker/language/jquery.ui.datepicker-no.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/datepicker/language/jquery.ui.datepicker-ru.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/datepicker/language/jquery.ui.datepicker-sv.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/datepicker/language/jquery.ui.datepicker-da.js'></script>
<link rel='stylesheet' type='text/css' href='$gJsUrl/jquery/ui/css/smoothness/jquery-ui-1.8.17.custom.css' />
<script type="text/javascript" src="$gJsUrl/jquery/ui/jquery-ui-1.8.17.custom.min.js"></script>
<script type="text/javascript" src="$gJsUrl/jquery/jquery-ui-autocomplete-extension/scottgonzalez-jquery-ui-extensions-e34c945/autocomplete/jquery.ui.autocomplete.html.js"></script>
<script type='text/javascript' src='$gJsUrl/jquery/wtooltip.min.js'></script>
<link rel='stylesheet' type='text/css' href='$gJsUrl/jquery/tipsy-0.1.7/src/stylesheets/tipsy.css' />
<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
<script type="text/javascript" src="$gJsUrl/socket/socket.io.min.js"></script>
<script type='text/javascript' src='$gJsUrl/jquery/tipsy-0.1.7/src/javascripts/jquery.tipsy.js'></script>
<script type="text/javascript" src="$gJsUrl/jquery/cycle/jquery.cycle.all.min.2.99.js"></script>
<script type="text/javascript" src="$gJsUrl/jquery/jquery.progressbar.2.0/js/jquery.progressbar.min.js"></script>
<script type='text/javascript' src='$gJsUrl/jquery/jquery.corner.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/jquery.corner.config.js'></script>
<script type="text/javascript" src="$gJsUrl/jquery/ui/furf-jquery-ui-touch-punch-766dcf9/jquery.ui.touch-punch.min.js"></script>
<script type="text/javascript" src="$gJsUrl/jquery/jquery.caret.js"></script>
<script type="text/javascript" src="$gJsUrl/jquery/print.js"></script>
<script type='text/javascript' src='$gJsUrl/jquery/jqzoom_ev-2.3/js/jquery.jqzoom-core-pack.js'></script>  
<link rel="stylesheet" type="text/css" href="$gJsUrl/jquery/jqzoom_ev-2.3/css/jquery.jqzoom.css"> 
<script type='text/javascript'>
$(document).ready(function() {
  $(document).ready(function(){
    $('.zoomable').jqzoom({
      zoomType: 'innerzoom'
    });
  });
});
</script>
<script type="text/javascript" src="$gJsUrl/soundmanagerv297a-20150601/script/soundmanager2-jsmin.js"></script>
<script type='text/javascript'>
$(document).ready(function() {
  soundManager.onready(function() {
    soundManager.setup({
    waitForWindowLoad: true,
    debugMode: false,
    debugFlash: false,
    useConsole: true,
    url: '$gSwfPlayerUrl'
    });
  });
});
</script>
<script type='text/javascript' src='$gJsUrl/jquery/jquery.autoresize.js'></script>
<script type='text/javascript'>
$(document).ready(function() {
  $('input.auto_grow').autoResize({
    comfortZone: 30
  });
});
</script>
<style type="text/css">
// Fix the IE transparency for .png images
<!--[if lt IE 7]>
img, div, span, input { behavior: url('$gApiUrl/image/js/iepngfix.htc') }
<![endif]-->
</style>
HEREDOC;

    return($str);
  }

  // Render the css properties for a preview page
  function renderPreviewCssProperties() {
    global $gTemplateDesignUrl;

    $str = "<link rel='stylesheet' type='text/css' href='$gTemplateDesignUrl/data/css/default.css' />";

    $str .= $this->renderDefaultModelCssPageProperties();

    return($str);
  }

  // Render the css properties for the preformatted pages of the default computer model
  function renderDefaultModelCssPageProperties() {
    $templateModelId = $this->getComputerDefault();

    $filename = $this->getModelCssPath($templateModelId);
    $properties = LibFile::readIntoString($filename);

    // Remove some identifiers so as to have the css properties accessible outside of a model
    // Otherwise it would not be possible to reach them as they would be behind their model
    $properties = preg_replace("/.ID[^ ]* +/i", '', $properties);

    $str = "<style type='text/css'><!-- $properties --></style>";

    return($str);
  }

  // Get the css properties for a preformatted page of the default computer model
  function getDefaultModelCssPageProperties($systemPage) {
    $str = '';

    $templateModelId = $this->getComputerDefault();
    if ($templatePage = $this->templatePageUtils->selectByTemplateModelIdAndSystemPage($templateModelId, $systemPage)) {
      $templatePageId = $templatePage->getId();
      $str .= $this->templatePageUtils->renderHtmlProperties($templatePageId);
    }

    return($str);
  }

  // Render the template content
  function renderContent($templateModelId) {
    // Check if the cache needs to be updated
    $this->checkRefreshCache($templateModelId);

    $filename = $this->getModelFilename($templateModelId);

    $str = LibFile::readIntoString($filename);

    // Update the non cached content
    $str = $this->updateContent($str);

    return($str);
  }

  // Render the name of the current page
  function renderCurrentPageName() {
    global $gTemplate;

    $str = "\n<div class='system_current_page'>"
      . $gTemplate->getPageTitle()
      . "</div>";

    return($str);
  }

  // Render the Facebook XMLNS
  function renderFacebookXMLNS() {
    global $gTemplate;

    $str = $gTemplate->getFacebookXMLNS();

    return($str);
  }

  // Render the current page content
  // The page element is not cached because must be update at each request
  function renderCurrentPageContent() {
    global $gTemplate;

    $str = "\n<div class='template_current_page'>"
      . $gTemplate->getPageContent()
      . "</div>";

    return($str);
  }

  // Update the non cached content of the template
  function updateContent($content) {
    global $gTemplate;
    global $PHP_SELF;
    global $gSetupWebsiteName;
    global $gSetupWebsiteUrl;

    $this->loadLanguageTexts();

    // The page content has to be replace first as it may contain some template elements
    // that in turn, are to be replaced as well, so as to offer different contents
    // simultaneously on one web page only, and not permanently in some model elements
    if (strstr($content, 'TEMPLATE_CONTENT_PAGE')) {
      $replace = $this->renderCurrentPageContent();
      $content = str_replace('TEMPLATE_CONTENT_PAGE', $replace, $content);
    }

    if (strstr($content, 'TEMPLATE_CONTENT_TITLE')) {
      $replace = $gTemplate->getPageTitle();
      if (!$replace) {
        $replace = $this->profileUtils->getWebsiteTitle();
      }
      $content = str_replace('TEMPLATE_CONTENT_TITLE', $replace, $content);
    }

    if (strstr($content, 'TEMPLATE_CONTENT_URL')) {
      $replace = $PHP_SELF;
      $content = str_replace('TEMPLATE_CONTENT_URL', $replace, $content);
    }

    if (strstr($content, 'TEMPLATE_CONTENT_WEBSITE_NAME')) {
      $replace = $gSetupWebsiteName;
      $content = str_replace('TEMPLATE_CONTENT_WEBSITE_NAME', $replace, $content);
    }

    if (strstr($content, 'TEMPLATE_CONTENT_CURRENT_PAGE_NAME')) {
      $replace = $this->renderCurrentPageName();
      $content = str_replace('TEMPLATE_CONTENT_CURRENT_PAGE_NAME', $replace, $content);
    }

    if (strstr($content, 'TEMPLATE_FACEBOOK_XMLNS')) {
      $replace = $this->renderFacebookXMLNS();
      $content = str_replace('TEMPLATE_FACEBOOK_XMLNS', $replace, $content);
    }

    if (strstr($content, 'TEMPLATE_SOCIAL_BUTTONS')) {
      $replace = $this->commonUtils->renderSocialNetworksButtons($gSetupWebsiteName, $gSetupWebsiteUrl);
      $content = str_replace('TEMPLATE_SOCIAL_BUTTONS', $replace, $content);
    }

    if (strstr($content, 'TEMPLATE_CONTENT_CLOCK_DATE')) {
      $replace = $this->clockUtils->renderDate();
      $content = str_replace('TEMPLATE_CONTENT_CLOCK_DATE', $replace, $content);
    }

    if (strstr($content, 'TEMPLATE_CONTENT_CLOCK_TIME')) {
      $replace = $this->clockUtils->renderTime();
      $content = str_replace('TEMPLATE_CONTENT_CLOCK_TIME', $replace, $content);
    }

    if (strstr($content, 'TEMPLATE_CONTENT_USER_MINI_LOGIN')) {
      $replace = $this->userUtils->renderUserMiniLogin();
      $content = str_replace('TEMPLATE_CONTENT_USER_MINI_LOGIN', $replace, $content);
    }

    if (strstr($content, 'TEMPLATE_CONTENT_DYNPAGE_BREADCRUMBS')) {
      $replace = $this->dynpageUtils->renderBreadCrumbs();
      $content = str_replace('TEMPLATE_CONTENT_DYNPAGE_BREADCRUMBS', $replace, $content);
    }

    if (strstr($content, 'TEMPLATE_CONTENT_SHOP_CATEGORY_MENU')) {
      $replace = $this->shopCategoryUtils->renderMenu();
      if (!$replace) {
        $replace = $this->navmenuUtils->renderTags();
      }
      $content = str_replace('TEMPLATE_CONTENT_SHOP_CATEGORY_MENU', $replace, $content);
    }

    if (strstr($content, 'TEMPLATE_CONTENT_SHOP_CATEGORY_ACCORDION_MENU')) {
      $replace = $this->shopCategoryUtils->renderAccordionMenu();
      if (!$replace) {
        $replace = $this->navmenuUtils->renderTags();
      }
      $content = str_replace('TEMPLATE_CONTENT_SHOP_CATEGORY_ACCORDION_MENU', $replace, $content);
    }

    if (strstr($content, 'TEMPLATE_CONTENT_SEARCH')) {
      $replace = $this->commonUtils->renderMiniSearch($this->websiteText[0]);
      $content = str_replace('TEMPLATE_CONTENT_SEARCH', $replace, $content);
    }

    if (strstr($content, 'TEMPLATE_CONTENT_LAST_UPDATE')) {
      $replace = $this->adminUtils->renderLastUpdate();
      $content = str_replace('TEMPLATE_CONTENT_LAST_UPDATE', $replace, $content);
    }

    if (strstr($content, 'TEMPLATE_CONTENT_MAIL_REGISTRATION')) {
      $replace = $this->mailAddressUtils->renderMiniRegister();
      $content = str_replace('TEMPLATE_CONTENT_MAIL_REGISTRATION', $replace, $content);
    }

    if (strstr($content, 'TEMPLATE_CONTENT_CLIENT_IMAGE_CYCLE')) {
      $replace = $this->clientUtils->renderImageCycleInTemplateElement();
      $content = str_replace('TEMPLATE_CONTENT_CLIENT_IMAGE_CYCLE', $replace, $content);
    }

    if (strstr($content, 'TEMPLATE_CONTENT_SMS_REGISTRATION')) {
      $replace = $this->smsNumberUtils->renderMiniNumberRegister();
      $content = str_replace('TEMPLATE_CONTENT_SMS_REGISTRATION', $replace, $content);
    }

    if (strstr($content, 'TEMPLATE_CONTENT_LANGUAGE_BAR')) {
      $replace = $this->languageUtils->renderWebsiteLanguageBar();
      if (!$replace) {
        $replace = $this->languageUtils->renderTags();
      }
      $content = str_replace('TEMPLATE_CONTENT_LANGUAGE_BAR', $replace, $content);
    }

    if (strstr($content, 'TEMPLATE_CONTENT_WEBSITE_ADDRESS')) {
      $replace = $this->profileUtils->renderWebSiteAddress();
      $content = str_replace('TEMPLATE_CONTENT_WEBSITE_ADDRESS', $replace, $content);
    }

    if (strstr($content, 'TEMPLATE_CONTENT_WEBSITE_TELEPHONE')) {
      $replace = $this->profileUtils->renderWebSiteTelephone();
      $content = str_replace('TEMPLATE_CONTENT_WEBSITE_TELEPHONE', $replace, $content);
    }

    if (strstr($content, 'TEMPLATE_CONTENT_WEBSITE_FAX')) {
      $replace = $this->profileUtils->renderWebSiteFax();
      $content = str_replace('TEMPLATE_CONTENT_WEBSITE_FAX', $replace, $content);
    }

    if (strstr($content, 'TEMPLATE_CONTENT_WEBSITE_COPYRIGHT')) {
      $replace = $this->profileUtils->renderWebSiteCopyright();
      $content = str_replace('TEMPLATE_CONTENT_WEBSITE_COPYRIGHT', $replace, $content);
    }

    if (strstr($content, 'TEMPLATE_CONTENT_LANGUAGE_')) {
      if (strstr($content, 'TEMPLATE_CONTENT_LANGUAGE_') && preg_match_all("/(TEMPLATE_CONTENT_LANGUAGE_)([0-9]*)/", $content, $matches)) {
        $patterns = $matches[0];
        $templateElementIds = $matches[2];
        for ($i = 0; $i < count($templateElementIds); $i++) {
          $pattern = $patterns[$i];
          $templateElementId = $templateElementIds[$i];
          $replace = $this->templateElementLanguageUtils->render($templateElementId);
          $content = str_replace($pattern, $replace, $content);
        }
      }
    }

    if (strstr($content, 'TEMPLATE_CONTENT_RSS_FEED_')) {
      if (strstr($content, 'TEMPLATE_CONTENT_RSS_FEED_') && preg_match_all("/(TEMPLATE_CONTENT_RSS_FEED_)([0-9]*)/", $content, $matches)) {
        $patterns = $matches[0];
        $rssFeedIds = $matches[2];
        for ($i = 0; $i < count($rssFeedIds); $i++) {
          $pattern = $patterns[$i];
          $rssFeedId = $rssFeedIds[$i];
          $replace = $this->rssFeedUtils->render($rssFeedId);
          $content = str_replace($pattern, $replace, $content);
        }
      }
    }

    return($content);
  }

  // Set the client as being a phone or a small screen device
  function setPhoneClient() {
    LibSession::putSessionValue(TEMPLATE_SESSION_PHONE_CLIENT, true);
    LibCookie::putCookie(TEMPLATE_SESSION_PHONE_CLIENT, 1, $this->cookieMobilePhoneDuration);
  }

  // Set the client as not being a phone or a small screen device
  function unsetPhoneClient() {
    LibSession::putSessionValue(TEMPLATE_SESSION_PHONE_CLIENT, false);
    LibCookie::putCookie(TEMPLATE_SESSION_PHONE_CLIENT, 0, $this->cookieMobilePhoneDuration);
  }

  // Check if the client is a phone or a small screen device
  function isPhoneClient() {
    $isPhone = LibSession::getSessionValue(TEMPLATE_SESSION_PHONE_CLIENT);

    // Check the cookie if the session expired
    if (!$isPhone) {
      $isPhone = LibCookie::getCookie(TEMPLATE_SESSION_PHONE_CLIENT);
    }

    return($isPhone);
  }

  // Set the client as being a touch screen device
  function setTouchClient() {
    LibSession::putSessionValue(TEMPLATE_SESSION_TOUCH_CLIENT, true);
    LibCookie::putCookie(TEMPLATE_SESSION_TOUCH_CLIENT, 1, $this->cookieMobilePhoneDuration);
  }

  // Set the client as not being a touch screen device
  function unsetTouchClient() {
    LibSession::putSessionValue(TEMPLATE_SESSION_TOUCH_CLIENT, false);
    LibCookie::putCookie(TEMPLATE_SESSION_TOUCH_CLIENT, 0, $this->cookieMobilePhoneDuration);
  }

  // Check if the client is a touch screen device
  // All phone clients are considered touch screen devices
  // The iPad, not considered a phone client, is still considered a touch screen device
  function isTouchClient() {
    $isTouch = LibSession::getSessionValue(TEMPLATE_SESSION_TOUCH_CLIENT);

    // Check the cookie if the session expired
    if (!$isTouch) {
      $isTouch = LibCookie::getCookie(TEMPLATE_SESSION_TOUCH_CLIENT);
    }

    return($isTouch);
  }

  // Get the size of the font used to generate a captcha image
  function getSecurityCodeFontSize($isPhoneClient) {
    if ($isPhoneClient) {
      $fontSize = 20;
    } else {
      $fontSize = 15;
    }

    return($fontSize);
  }

}

?>
