<?php

class ContentImportUtils extends ContentImportDB {

  var $mlText;

  var $languageUtils;
  var $clockUtils;
  var $adminUtils;
  var $uniqueTokenUtils;
  var $profileUtils;

  function __construct() {
    parent::__construct();
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  // Log an import
  function logImport($domainName, $course, $lesson, $exercise) {
    $importDateTime = $this->clockUtils->getSystemDateTime();

    $contentImportHistory = new ContentImportHistory();
    $contentImportHistory->setDomainName($domainName);
    $contentImportHistory->setCourse($course);
    $contentImportHistory->setLesson($lesson);
    $contentImportHistory->setExercise($exercise);
    $contentImportHistory->setImportDateTime($importDateTime);
    $this->contentImportHistoryUtils->insert($contentImportHistory);
  }

  // Get the import permission and return the reason for refusal if any
  function getImportPermission($domainName, $importerPermissionKey) {
    $permission = CONTENT_IMPORT_ERROR_IMPORTER_UNKNOWN_WEBSITE;

    if ($contentImport = $this->selectByDomainNameAndIsImporting($domainName)) {
      $permissionKey = $contentImport->getPermissionKey();
      if ($importerPermissionKey && $importerPermissionKey == $permissionKey) {
        $permissionStatus = $contentImport->getPermissionStatus();
        if (!$permissionStatus) {
          $permission = CONTENT_IMPORT_ERROR_IMPORTER_GRANTED;
        } else if ($permissionStatus == CONTENT_IMPORT_PERMISSION_PENDING) {
          $permission = CONTENT_IMPORT_ERROR_IMPORTER_PENDING;
        } else if ($permissionStatus == CONTENT_IMPORT_PERMISSION_DENIED) {
          $permission = CONTENT_IMPORT_ERROR_IMPORTER_DENIED;
        }
      } else {
        $permission = CONTENT_IMPORT_ERROR_IMPORTER_INVALID_KEY;
      }
    }

    return($permission);
  }

  // Check if an import is granted
  function importIsGranted($permission) {
    $hasPermission = false;

    if ($permission == CONTENT_IMPORT_ERROR_IMPORTER_GRANTED) {
      $hasPermission = true;
    }

    return($hasPermission);
  }

  // Warn about an illegal import attempt
  function illegalImportAttemptAlert($domainName) {
    $this->loadLanguageTexts();

    $websiteName = $this->profileUtils->getProfileValue("website.name");
    $websiteEmail = $this->profileUtils->getProfileValue("website.email");

    $emailSubject = $this->mlText[0] . ' ' . $domainName;
    $emailBody = '';
    if ($domainName) {
      $emailSubject .=  ' ' . $this->mlText[7] . ' ' . $domainName;
      $emailBody = $this->mlText[1];
      $emailBody .= $this->mlText[2] . ' ' . $domainName;
      $emailBody .= $this->mlText[3];
    } else {
      $emailBody .= $this->mlText[10];
    }

    $emailBody .= "\n\n" . $websiteName;
    $emailBody = nl2br($emailBody);

    if (LibEmail::validate($websiteEmail)) {
      LibEmail::sendMail($websiteEmail, $websiteName, $emailSubject, $emailBody, $websiteEmail, $websiteName);
    }
  }

  // Check if the importing website is known to the exporting website
  // The importing website is unknown if it has not yet sent a request
  // to become an importer to the exporting website
  function importerIsUnknown($xmlResponse) {
    $isUnknown = false;

    if (strstr($xmlResponse, CONTENT_IMPORT_ERROR_IMPORTER_UNKNOWN_WEBSITE)) {
      $isUnknown = true;
    }

    return($isUnknown);
  }

  // Check if the request to be an importing website is pending
  // The importer is pending if it has sent a request to become an importer
  // to the exporting website but has not yet received a grant nor a denial
  // That is, if the importing website has registered an exporting website
  // but the exporting website has not yet registered the importing website
  // and its permission key
  function importerIsPending($xmlResponse, $contentImportId) {
    $isPending = false;

    if (strstr($xmlResponse, CONTENT_IMPORT_ERROR_IMPORTER_PENDING)) {
      $isPending = true;
    }

    return($isPending);
  }

  // Check if the request to be an importing website has been denied
  // The importer is denied if it has sent a request to become an importer
  // to the exporting website and the request has been denied
  function importerIsDenied($xmlResponse) {
    $isDenied = false;

    if (strstr($xmlResponse, CONTENT_IMPORT_ERROR_IMPORTER_DENIED)) {
      $isDenied = true;
    }

    return($isDenied);
  }

  // Check if the importer has an invalid permission key
  function importerHasAnUnknownPermissionKey($xmlResponse) {
    $hasInvalidKey = false;

    if (strstr($xmlResponse, CONTENT_IMPORT_ERROR_IMPORTER_UNKNOWN_KEY)) {
      $hasInvalidKey = true;
    }

    return($hasInvalidKey);
  }

  // Render a warning message about a refused import
  // to an unknown importing website
  function renderImportUnknownWarning($contentImportId) {
    $this->loadLanguageTexts();

    if ($contentImport = $this->selectById($contentImportId)) {
      $domainName = $contentImport->getDomainName();
    } else {
      $domainName = '';
    }

    $str = $this->mlText[4] . ' ' . $domainName;

    return($str);
  }

  // Render a warning message about an import attempt
  // with an invalid permission key
  function renderImportUnknownPermissionKey($contentImportId) {
    $this->loadLanguageTexts();

    if ($contentImport = $this->selectById($contentImportId)) {
      $domainName = $contentImport->getDomainName();
    } else {
      $domainName = '';
    }

    $str = $this->mlText[22] . ' ' . $domainName;

    return($str);
  }

  // Render a message offering to register a website to import from
  function renderImportOffer($contentImportId) {
    global $gContentImportUrl;
    global $gJSNoStatus;

    $this->loadLanguageTexts();

    if ($contentImport = $this->selectById($contentImportId)) {
      $domainName = $contentImport->getDomainName();
    } else {
      $domainName = '';
    }

    $str = $this->mlText[5] . " <a  href='$gContentImportUrl/exporters/requestPermission.php?contentImportId=$contentImportId' $gJSNoStatus>" . $this->mlText[6] . '</a>';

    return($str);
  }

  // Render a warning message about a refused import
  // to a website whose request to import had been denied
  function renderImportDeniedWarning($contentImportId) {
    $this->loadLanguageTexts();

    if ($contentImport = $this->selectById($contentImportId)) {
      $domainName = $contentImport->getDomainName();
    } else {
      $domainName = '';
    }

    $str = $this->mlText[8] . ' ' . $domainName;

    return($str);
  }

  // Render a warning message about a refused import
  // to a website whose request to import is pending
  function renderImportPendingWarning($contentImportId) {
    $this->loadLanguageTexts();

    if ($contentImport = $this->selectById($contentImportId)) {
      $domainName = $contentImport->getDomainName();
    } else {
      $domainName = '';
    }

    $str = $this->mlText[9] . ' ' . $domainName;

    return($str);
  }

  // Render import certificate
  // The import certificate is simply a unique token changing periodically
  // and common to all websites
  function renderImportCertificate() {
    $releaseMonth = $this->clockUtils->getSystemMonth();
    $releaseYear = $this->clockUtils->getSystemYear();

    $certificate = md5('content_import_' . $releaseMonth . $releaseYear);

    return($certificate);
  }
  function renderPermissionKey() {
    $permissionKey = LibUtils::generateUniqueId(CONTENT_IMPORT_KEY_LENGTH);

    return($permissionKey);
  }

  // Send a request to a website for the permission to import content
  function getPermissionRequestXML($contentImportId) {
    global $gHomeUrl;

    $xmlResponse = '';

    if ($contentImport = $this->selectById($contentImportId)) {
      $domainName = $contentImport->getDomainName();

      $importCertificate = $this->renderImportCertificate();
      $permissionKey = $this->renderPermissionKey();

      $url = $domainName . "/engine/system/contentImport/exporters/exposeRequestPermission.php?domainName=$gHomeUrl&importCertificate=$importCertificate&permissionKey=$permissionKey";

      $xmlResponse = LibFile::curlGetFileContent($url);
    }

    return($xmlResponse);
  }

  // Register at the importing website that the permission request is pending
  function registerPendingPermissionRequest($contentImportId, $permissionKey) {
    if ($contentImport = $this->selectById($contentImportId)) {
      // Set the permission key created by the exporting website
      $contentImport->setPermissionKey($permissionKey);
      $contentImport->setPermissionStatus(CONTENT_IMPORT_PERMISSION_PENDING);
      $this->update($contentImport);
    }
  }

  // Check if the permission key is valid
  function permissionKeyIsValid($permissionKey) {
    $isValid = false;

    if (strlen($permissionKey) == 10) {
      $isValid = true;
    }

    return($isValid);
  }

  // Render a warning message following a permission request
  function renderPermissionRequestMessage($xmlResponse) {
    $this->loadLanguageTexts();

    $str = '';

    if ($this->permissionKeyIsValid($xmlResponse)) {
      $str = $this->mlText[11];
    } else {
      $str = $this->mlText[13];
    }

    return($str);
  }

  // Register at the exporting website that a permission request has been received
  function registerPermissionRequestWasReceived($domainName, $permissionKey) {
    if ($domainName && $permissionKey) {
      // Generate a new permission key by the exporting website
      $permissionKey = $this->renderPermissionKey();

      if ($contentImport = $this->selectByDomainNameAndIsImporting($domainName)) {
        $contentImportId = $contentImport->getId();
        $contentImport->setPermissionKey($permissionKey);
        $contentImport->setPermissionStatus(CONTENT_IMPORT_PERMISSION_PENDING);
        $this->update($contentImport);
      } else {
        $contentImport = new ContentImport();
        $contentImport->setDomainName($domainName);
        $contentImport->setIsImporting(true);
        $contentImport->setPermissionKey($permissionKey);
        $contentImport->setPermissionStatus(CONTENT_IMPORT_PERMISSION_PENDING);
        $this->insert($contentImport);
        $contentImportId = $this->getLastInsertId();
      }

      $this->sendPermissionRequestNotification($contentImportId);

      return($permissionKey);
    }
  }

  // Grant a permission request
  function grantPermissionRequest($contentImportId) {
    if ($contentImport = $this->selectById($contentImportId)) {
      $contentImport->setPermissionStatus('');
      $this->update($contentImport);
      $this->notifyOfPermissionRequestHandling($contentImportId);
    }
  }

  // Deny a permission request
  function denyPermissionRequest($contentImportId) {
    if ($contentImport = $this->selectById($contentImportId)) {
      $contentImport->setPermissionStatus(CONTENT_IMPORT_PERMISSION_DENIED);
      $this->update($contentImport);
      $this->notifyOfPermissionRequestHandling($contentImportId);
    }
  }

  // Send a request to a website for the permission to import content
  function notifyOfPermissionRequestHandling($contentImportId) {
    global $gHomeUrl;

    $xmlResponse = '';

    if ($contentImport = $this->selectById($contentImportId)) {
      $domainName = $contentImport->getDomainName();
      $permissionKey = $contentImport->getPermissionKey();
      $permissionStatus = $contentImport->getPermissionStatus();

      $importCertificate = $this->renderImportCertificate();

      $url = $domainName . "/engine/system/contentImport/importers/notifyOfPermissionRequestHandling.php?domainName=$gHomeUrl&importCertificate=$importCertificate&permissionKey=$permissionKey&permissionStatus=$permissionStatus";

      LibFile::curlGetFileContent($url);
    }
  }

  // Register at the importing website the handling of the permission request
  // that is, if the request has been granted or denied
  function registerPermissionHandling($domainName, $exporterPermissionKey, $permissionStatus) {
    if ($domainName) {
      if ($contentImport = $this->selectByDomainNameAndIsExporting($domainName)) {
        $contentImportId = $contentImport->getId();
        $permissionKey = $contentImport->getPermissionKey();
        if ($permissionKey == $exporterPermissionKey) {
          $contentImport->setPermissionStatus($permissionStatus);
          $this->update($contentImport);

          $this->sendPermissionHandlingNotification($contentImportId);
        }
      }
    }
  }

  // Send an email to notify the importing website about the handling of its permission request
  function sendPermissionHandlingNotification($contentImportId) {
    global $gContentImportUrl;
    global $gJSNoStatus;

    $this->loadLanguageTexts();

    if ($contentImport = $this->selectById($contentImportId)) {
      $domainName = $contentImport->getDomainName();
      $permissionStatus = $contentImport->getPermissionStatus();

      $websiteName = $this->profileUtils->getProfileValue("website.name");
      $websiteEmail = $this->profileUtils->getProfileValue("website.email");

      // Generate a unique token and keep it for later use
      $tokenName = CONTENT_IMPORT_TOKEN_NAME;
      $tokenDuration = $this->adminUtils->getLoginTokenDuration();
      $tokenValue = $this->uniqueTokenUtils->create($tokenName, $tokenDuration);

      $emailSubject = $this->mlText[23];
      $emailBody = $this->mlText[24]
        . " <a href='$domainName'>$domainName</a> "
        . $this->mlText[25] . ' ';
      if ($this->permissionRequestWasDenied($contentImportId)) {
        $emailBody .= $this->mlText[26] . ' ' . $this->mlText[28];
        $emailBody .= "<br><br>" . $this->mlText[29] . " <a href='$domainName'>$domainName</a> ";
      } else {
        $emailBody .= $this->mlText[27] . ' ' . $this->mlText[28];
        $emailBody .= "<br><br>" . $this->mlText[30] . " <a href='$domainName'>$domainName</a> ";
      }

      $emailBody .= "<br><br>" . $websiteName;

      if ($websiteEmail) {
        LibEmail::sendMail($websiteEmail, $websiteName, $emailSubject, $emailBody, $websiteEmail, $websiteName);
      }
    }
  }

  // Check if a permission request is pending
  function permissionRequestIsPending($contentImportId) {
    $isPending = false;

    if ($contentImport = $this->selectById($contentImportId)) {
      $permissionStatus = $contentImport->getPermissionStatus();
      if ($permissionStatus == CONTENT_IMPORT_PERMISSION_PENDING) {
        $isPending = true;
      }
    }

    return($isPending);
  }

  // Check if a permission request was denied
  function permissionRequestWasDenied($contentImportId) {
    $wasDenied = false;

    if ($contentImport = $this->selectById($contentImportId)) {
      $permissionStatus = $contentImport->getPermissionStatus();
      if ($permissionStatus == CONTENT_IMPORT_PERMISSION_DENIED) {
        $wasDenied = true;
      }
    }

    return($wasDenied);
  }

  // Send an email to notify the exporting website about the pending permission request
  function sendPermissionRequestNotification($contentImportId) {
    global $gContentImportUrl;
    global $gJSNoStatus;

    $this->loadLanguageTexts();

    if ($contentImport = $this->selectById($contentImportId)) {
      $domainName = $contentImport->getDomainName();

      $websiteName = $this->profileUtils->getProfileValue("website.name");
      $websiteEmail = $this->profileUtils->getProfileValue("website.email");

      // Generate a unique token and keep it for later use
      $tokenName = CONTENT_IMPORT_TOKEN_NAME;
      $tokenDuration = $this->adminUtils->getLoginTokenDuration();
      $tokenValue = $this->uniqueTokenUtils->create($tokenName, $tokenDuration);

      $emailSubject = $this->mlText[21];
      $emailBody = $this->mlText[14]
        . " <a href='$domainName'>$domainName</a> "
        . $this->mlText[15]
        . "<br><br>" . $this->mlText[16]
        . " <a href='$gContentImportUrl/importers/handlePermissionRequest.php?contentImportId=$contentImportId&grant=1&tokenName=$tokenName&tokenValue=$tokenValue' $gJSNoStatus>" . $this->mlText[17] . "</a> "
        . $this->mlText[18]
        . " <a href='$gContentImportUrl/importers/handlePermissionRequest.php?contentImportId=$contentImportId&grant=0&tokenName=$tokenName&tokenValue=$tokenValue' $gJSNoStatus>" . $this->mlText[19] . "</a> "
        . $this->mlText[20]
        . "<br><br>" . $websiteName;

      if ($websiteEmail) {
        LibEmail::sendMail($websiteEmail, $websiteName, $emailSubject, $emailBody, $websiteEmail, $websiteName);
      }
    }
  }

  // Check if a certificate is a valid one
  function isValidCertificate($importCertificate) {
    $isValid = false;

    $validCertificate = $this->renderImportCertificate();

    if ($validCertificate == $importCertificate) {
      $isValid = true;
    }

    return($isValid);
  }

}

?>
