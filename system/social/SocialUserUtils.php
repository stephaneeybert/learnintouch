<?

class SocialUserUtils {

  var $commonUtils;
  var $profileUtils;
  var $facebookUtils;
  var $linkedinUtils;

  function publishNotification($message, $url, $caption, $actionLinks) {
    $description = $this->profileUtils->getWebsiteSocialDescription();

    $logo = $this->profileUtils->getLogoFilename();

    if ($logo && is_file($this->profileUtils->filePath . $logo)) {
      $logoUrl = $this->profileUtils->fileUrl . '/' . $logo;
    } else {
      $logoUrl = '';
    }

    $message = LibString::decodeHtmlspecialchars($message);
    $caption = LibString::decodeHtmlspecialchars($caption);
    $description = LibString::decodeHtmlspecialchars($description);

    $message = LibString::escapeDoubleQuotes($message);
    $caption = LibString::escapeDoubleQuotes($caption);
    $description = LibString::escapeDoubleQuotes($description);

    $this->commonUtils->preventPageCaching();

    $str = $this->facebookUtils->publishNotification($message, $url, $caption, $actionLinks, $description, $logoUrl);

    $linkedInCaption = str_replace('{*actor*}', '', $caption);
    $str .= ' ' . $this->linkedinUtils->publishNotification($message, $url, $linkedInCaption, $actionLinks, $description, $logoUrl);

    return($str);
  }

}

?>
