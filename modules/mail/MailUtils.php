<?

class MailUtils extends MailDB {

  var $mlText;

  var $imageSize;
  var $imagePath;
  var $imageUrl;

  var $fileSize;
  var $filePath;
  var $fileUrl;

  // The separator between the filenames of the files attached to a mail
  var $attachmentsSeparator;

  var $preferences;

  var $dynpageUtils;
  var $languageUtils;
  var $preferenceUtils;
  var $adminModuleUtils;
  var $adminUtils;
  var $clockUtils;
  var $profileUtils;
  var $fileUploadUtils;

  function __construct() {
    parent::__construct();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imageSize = 200000;
    $this->imagePath = $gDataPath . 'mail/image/';
    $this->imageUrl = $gDataUrl . '/mail/image';

    $this->fileSize = 256000;
    $this->filePath = $gDataPath . 'mail/file/';
    $this->fileUrl = $gDataUrl . '/mail/file';

    $this->attachmentsSeparator = ':';
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->imagePath)) {
      if (!is_dir($gDataPath . 'mail')) {
        mkdir($gDataPath . 'mail');
      }
      mkdir($this->imagePath);
      chmod($this->imagePath, 0755);
    }

    if (!is_dir($this->filePath)) {
      mkdir($this->filePath);
      chmod($this->filePath, 0755);
    }
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  function loadPreferences() {
    $this->loadLanguageTexts();

    $this->preferences = array(
      "MAIL_ONLY_ADMIN" =>
      array($this->mlText[7], $this->mlText[16], PREFERENCE_TYPE_BOOLEAN, ''),
        "MAIL_USER_AUTOLOGIN" =>
        array($this->mlText[18], $this->mlText[19], PREFERENCE_TYPE_BOOLEAN, ''),
          "MAIL_DISPLAY_LOGO" =>
          array($this->mlText[12], $this->mlText[52], PREFERENCE_TYPE_BOOLEAN, ''),
            "MAIL_DISPLAY_SIGNATURE" =>
            array($this->mlText[11], $this->mlText[51], PREFERENCE_TYPE_BOOLEAN, ''),
              "MAIL_COLLECT_ALL" =>
              array($this->mlText[14], $this->mlText[15], PREFERENCE_TYPE_BOOLEAN, ''),
                "MAIL_SUBSCRIPTION_ACKNOWLEDGE" =>
                array($this->mlText[9], $this->mlText[13], PREFERENCE_TYPE_MLTEXT, $this->mlText[0]),
                  "MAIL_SIGNATURE" =>
                  array($this->mlText[10], $this->mlText[50], PREFERENCE_TYPE_MLTEXT, ''),
                    "MAIL_HTML_EDITOR" =>
                    array($this->mlText[30], $this->mlText[31], PREFERENCE_TYPE_SELECT,
                      array(
                        'HTML_EDITOR_CKEDITOR' => $this->mlText[33],
                      )),
                    "MAIL_LIST_STEP" =>
                    array($this->mlText[8], $this->mlText[4], PREFERENCE_TYPE_SELECT, array(10 => "10", 20 => "20", 50 => "50", 100 => "100")),
                      "MAIL_AUTO_DELETE" =>
                      array($this->mlText[5], $this->mlText[6], PREFERENCE_TYPE_SELECT, array(6 => "6", 12 => "12", 24 => "24", 36 => "36", 48 => "48")),
                        "MAIL_IMAGE_WIDTH" =>
                        array($this->mlText[22], $this->mlText[23], PREFERENCE_TYPE_TEXT, 300),
                          "MAIL_PHONE_IMAGE_WIDTH" =>
                          array($this->mlText[24], $this->mlText[25], PREFERENCE_TYPE_TEXT, 140),
                          );

    $this->preferenceUtils->init($this->preferences);
  }

  // Get the width of the image
  function getImageWidth() {
    global $gIsPhoneClient;

    if ($gIsPhoneClient) {
      $width = $this->preferenceUtils->getValue("MAIL_PHONE_IMAGE_WIDTH");
    } else {
      $width = $this->preferenceUtils->getValue("MAIL_IMAGE_WIDTH");
    }

    return($width);
  }

  // Remove the non referenced images
  function deleteUnusedImages() {
    $handle = opendir($this->imagePath);
    while ($imageFile = readdir($handle)) {
      if ($imageFile != "." && $imageFile != ".." && !strstr($imageFile, '*')) {
        if (!$this->imageIsUsed($imageFile)) {
          $imageFile = str_replace(" ", "\\ ", $imageFile);
          if (file_exists($this->imagePath . $imageFile)) {
            unlink($this->imagePath . $imageFile);
          }
        }
      }
    }
    closedir($handle);
  }

  // Remove the non referenced attached files
  function deleteUnusedAttachedFiles() {
    $handle = opendir($this->filePath);
    while ($attachedFile = readdir($handle)) {
      if ($attachedFile != "." && $attachedFile != ".." && !strstr($attachedFile, '*')) {
        if (!$this->attachedFileIsUsed($attachedFile)) {
          $attachedFile = str_replace(" ", "\\ ", $attachedFile);
          if (file_exists($this->filePath . $attachedFile)) {
            unlink($this->filePath . $attachedFile);
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

    if ($result = $this->dao->selectBodyLikeImage($image)) {
      if ($result->getRowCount() < 1) {
        $isUsed = false;
      }
    }

    return($isUsed);
  }

  // Check if an attached file is being used
  function attachedFileIsUsed($file) {
    $isUsed = true;

    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectAttachmentsLikeFile($file)) {
      if ($result->getRowCount() < 1) {
        $isUsed = false;
      }
    }

    return($isUsed);
  }

  // Get the attached files
  function getAllAttachedFiles() {
    $attachedFiles = array();

    $handle = opendir($this->filePath);
    while ($attachedFile = readdir($handle)) {
      if ($attachedFile != "." && $attachedFile != ".." && !strstr($attachedFile, '*')) {
        if (file_exists($this->filePath . $attachedFile)) {
          array_push($attachedFiles, $attachedFile);
        }
      }
    }
    closedir($handle);

    return($attachedFiles);
  }

  // Delete an attached file
  function deleteAttachedFile($filename) {
    $filename = str_replace(" ", "\\ ", $filename);
    if (file_exists($this->filePath . $filename)) {
      unlink($this->filePath . $filename);
    }
  }

  // Get the list of meta names
  function getMetaNames() {
    $this->loadLanguageTexts();

    $metaNames = array(
      array(MAIL_META_USER_FIRSTNAME, $this->mlText[1]),
      array(MAIL_META_USER_LASTNAME, $this->mlText[2]),
      array(MAIL_META_USER_EMAIL, $this->mlText[3]),
      array(MAIL_META_USER_PASSWORD, $this->mlText[17]),
    );

    $metaElearningNames = array(
      array(MAIL_META_ELEARNING_NEXT_EXERCISE_NAME, $this->mlText[21]),
    );

    if ($this->adminModuleUtils->moduleGrantedToAdmin(MODULE_ELEARNING)) {
      foreach ($metaElearningNames as $metaElearningName) {
        array_push($metaNames, $metaElearningName);
      }
    }

    return($metaNames);
  }

  // Render the meta names as a javascript array
  function renderMetaNamesJs() {
    $strMetaNames = "[";

    $metaNames = $this->getMetaNames();
    foreach ($metaNames as $metaName) {
      list($name, $description) = $metaName;
      $strMetaNames .= "\n[\"$description\", \"$name\"],";
    }
    $strMetaNames .= "]";

    $strMetaNames = str_replace("],]", "]]", $strMetaNames);

    return($strMetaNames);
  }

  // Check if the mail is locked for the logged in admin
  function isLockedForLoggedInAdmin($mailId) {
    $locked = false;

    $adminLogin = $this->adminUtils->checkAdminLogin();
    if (!$this->adminUtils->isSuperAdmin($adminLogin)) {
      if ($mail = $this->selectById($mailId)) {
        $locked = $mail->getLocked();
      }
    }

    return($locked);
  }

  // Check if the mail is locked
  function isLocked($mailId) {
    $locked = false;

    if ($mail = $this->selectById($mailId)) {
      $locked = $mail->getLocked();
    }

    return($locked);
  }

  // Check if an image is being used
  function inTextFormat($mailId) {
    $textFormat = false;

    if ($mail = $this->selectById($mailId)) {
      $textFormat = $mail->getTextFormat();
    }

    return($textFormat);
  }

  // Transform the image urls into email image elements
  // for multiple modules
  function urlToEmailImageCID($body) {
    $body = $this->urlToEmailImageCIDReplace($this->imageUrl, $body);
    $body = $this->urlToEmailImageCIDReplace($this->dynpageUtils->imageUrl, $body);
    return($body);
  }

  // Transform the image urls into email image elements
  function urlToEmailImageCIDReplace($urlPrefix, $body) {
    global $gHomeUrl;

    $relativeUrl = substr($urlPrefix, strlen($gHomeUrl), strlen($urlPrefix) - strlen($gHomeUrl)) . '/';
    $body = str_replace($relativeUrl, "cid:", $body);

    return($body);
  }

  // Get the image names from the email image elements
  function getImagesFromCID($body) {
    $attachedImages = array();

    if (strstr($body, 'cid:') && preg_match_all("(\"cid:[\S]*?\")", $body, $matches)) {
      $matches = $matches[0];
      for ($i = 0; $i < count($matches); $i++) {
        $matches[$i] = str_replace("\"", '', $matches[$i]);
        $matches[$i] = str_replace("'", '', $matches[$i]);
        $matches[$i] = str_replace("cid:", '', $matches[$i]);
        if (!$this->fileUploadUtils->isImageType($matches[$i])) {
          unset($matches[$i]);
        } else {
          $matches[$i] = $this->imagePath . $matches[$i];
        }
      }

      $temp = array_unique($matches);
      $matches = array_values($temp);
      $attachedImages = $matches;
    }

    return($attachedImages);
  }

  // Add the user email address to a unsubscribe page url if any
  function addEmailAddressToUnsubscribeUrl($body) {
    $pattern = "SYSTEM_PAGE_USER_UNSUBSCRIBE";
    $replace = "$pattern&amp;email=[USER_EMAIL]";
    if (strstr($body, $pattern)) {
      $body = str_replace($pattern, $replace, $body);
    }

    return($body);
  }

  // Add the user login name and password to a login page link if any
  function addLoginPasswordToLoginUrl($body) {
    $userAutologin = $this->preferenceUtils->getValue("MAIL_USER_AUTOLOGIN");

    if ($userAutologin) {
      $pattern = "SYSTEM_PAGE_USER_LOGIN";
      $replace = "$pattern&amp;login=[USER_EMAIL]&amp;password=[USER_PASSWORD]&amp;autologin=1";
      if (strstr($body, $pattern)) {
        $body = str_replace($pattern, $replace, $body);
      }
    }

    return($body);
  }

  // Get the attached files of a mail
  // Check that the files really exist
  function getExistingAttachedFiles($mailId, $withPath = false) {
    if ($mail = $this->selectById($mailId)) {
      $attachments = $mail->getAttachments();
      if ($attachments) {
        $filenames = explode($this->attachmentsSeparator, $attachments);
      } else {
        $filenames = array();
      }

      // If a file is listed in a mail but does not exist then remove it from the list
      foreach ($filenames as $key => $filename) {
        if (!file_exists($this->filePath . $filename)) {
          unset($filenames[$key]);
          // If a file is missing then remove it from the mail attachments list
          $this->removeAttachment($mailId, $filename);
        }

        if ($withPath) {
          $filenames[$key] = $this->filePath . $filenames[$key];
        }
      }

      return($filenames);
    }
  }

  // Add an attached file to a mail
  function addAttachment($mailId, $attachFilename) {
    if ($mail = $this->selectById($mailId)) {
      $filenames = $this->getExistingAttachedFiles($mailId);

      if (!in_array($attachFilename, $filenames)) {
        array_push($filenames, $attachFilename);
        $attachments = join($this->attachmentsSeparator, $filenames);
        $mail->setAttachments($attachments);
        $this->update($mail);
      }
    }
  }

  // Remove an attached file from a mail
  function removeAttachment($mailId, $attachedFilename) {
    if ($mail = $this->selectById($mailId)) {
      $attachments = $mail->getAttachments();
      $filenames = explode($this->attachmentsSeparator, $attachments);
      foreach ($filenames as $key => $filename) {
        if ($filename == $attachedFilename) {
          unset($filenames[$key]);
        }
      }
      $attachments = join($this->attachmentsSeparator, $filenames);
      $mail->setAttachments($attachments);
      $this->update($mail);
    }
  }

  // Check if the selected html editor is the CKEditor
  function useHtmlEditorCKEditor() {
    $result = false;

    $htmlEditor = $this->preferenceUtils->getValue("MAIL_HTML_EDITOR");

    if ($htmlEditor == 'HTML_EDITOR_CKEDITOR') {
      $result = true;
    }

    return($result);
  }

  // Render the subject
  function renderSubject($mail) {
    $str = $mail->subject;

    return($str);
  }

  // Update the url of the images
  // Add the domain name before the image url
  function updateImageUrl($content) {
    global $gHomeUrl;

    if (strstr($content, '<img') &&  preg_match_all("/<img.*?src *?= *?['|\"]([\S][^>][^'][^\"]*?)['|\"]/i", $content, $matches)) {
      $matches = $matches[1];
      if (count($matches) > 0) {
        for ($i = 0; $i < count($matches); $i++) {
          $strRelUrl = $matches[$i];
          $strAbsUrl = $gHomeUrl . $strRelUrl;
          $content = str_replace($strRelUrl, $strAbsUrl, $content);
        }
      }
    }

    return($content);
  }

  // Fix incorrect email address mailto urls
  // Remove any prefix from the url to avoid mailto urls prefixed by a domain name
  // like: http://www.europasprak.com/christophe.luciani@europasprak.com
  function fixMailtoUrls($content) {
    if (strstr($content, '<a')) {
      $pattern = "/(<a[^>]+href=\"mailto:).*\/([^\"]+)/i";
      $replacement = '$1$2';
      $content = preg_replace($pattern, $replacement, $content);
    }

    return($content);
  }

  // Update the url of the links
  // Add the domain name before the link url
  function relativeToAbsoluteUrls($content) {
    global $gHomeUrl;
    global $gEngineUrl;

    if (strstr($content, '<a')) {
      $prefix = substr($gEngineUrl, strlen($gHomeUrl) + 1, strlen($gEngineUrl) - strlen($gHomeUrl) - 1);

      $pattern = "/(<a[^>]+href=\")\/?(" . $prefix . "[^\"]+)/i";
      $replacement = '$1' . $gHomeUrl . '/' . '$2';
      $content = preg_replace($pattern, $replacement, $content);
    }

    return($content);
  }

  // Delete an email
  function deleteMail($mailId) {
    $this->delete($mailId);
  }

  // Delete the old mails
  function deleteOldMails() {
    $autoDelete = $this->preferenceUtils->getValue("MAIL_AUTO_DELETE");

    if ($autoDelete && is_numeric($autoDelete)) {
      $systemDate = $this->clockUtils->getSystemDate();

      // Get the date since which to delete the mails
      $sinceDate = $this->clockUtils->incrementMonths($systemDate, -1 * $autoDelete);

      $this->deleteByDate($sinceDate);
    }
  }

  // Render the body
  function renderBody($mail) {
    global $gHomeUrl;

    $mailId = $mail->getId();
    $body = $mail->getBody();
    $textFormat = $mail->getTextFormat();

    $inTextFormat = $this->inTextFormat($mailId);

    // Render the signature
    $signature = $this->preferenceUtils->getValue("MAIL_SIGNATURE");
    $displaySignature = $this->preferenceUtils->getValue("MAIL_DISPLAY_SIGNATURE");
    $signature = nl2br($signature);
    $displayLogo = $this->preferenceUtils->getValue("MAIL_DISPLAY_LOGO");

    // Check if the the email needs to have its line breaks translated into html tags
    if (!LibString::containsHtmlLineBreak($body)) {
      $body = nl2br($body);
    }

    $str = $body;

    if ($signature && $displaySignature) {
      $str .= "<p>$signature</p>";
    }

    if ($displayLogo) {
      $imageSrc = $this->profileUtils->fileUrl . "/" . $this->profileUtils->getLogoFilename();
      $str .= "<p><img src='$imageSrc' title='' alt='' /></p>";
    }

    if ($inTextFormat) {
      $str = LibString::br2nl($str);
      $str = LibString::p2nl($str);
      $str = LibString::stripTags($str);
      $str = LibString::normalizeLinebreaks($str);
      $str = str_replace("&nbsp;", '', $str);
      $str = preg_replace("/\t+/", '', $str);
      $str = preg_replace("/\n +/", "\n", $str);
      $str = preg_replace("/\n{2,}/", "\n\n", $str);
      $str = nl2br($str);
    }

    return($str);
  }

}

?>
