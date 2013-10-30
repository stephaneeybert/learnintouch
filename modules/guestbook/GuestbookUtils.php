<?

class GuestbookUtils extends GuestbookDB {

  var $mlText;
  var $websiteText;

  var $preferences;

  var $languageUtils;
  var $userUtils;

  function GuestbookUtils() {
    $this->GuestbookDB();
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  function loadPreferences() {
    $this->loadLanguageTexts();

    $this->preferences = array(
      "GUESTBOOK_SECURED" =>
      array($this->mlText[10], $this->mlText[31], PREFERENCE_TYPE_BOOLEAN, ''),
        "GUESTBOOK_MAIL_ON_POST" =>
        array($this->mlText[1], $this->mlText[3], PREFERENCE_TYPE_BOOLEAN, ''),
          "GUESTBOOK_INCLUDE_MESSAGE" =>
          array($this->mlText[4], $this->mlText[5], PREFERENCE_TYPE_BOOLEAN, ''),
            "GUESTBOOK_SECURITY_CODE" =>
            array($this->mlText[6], $this->mlText[7], PREFERENCE_TYPE_BOOLEAN, ''),
            );
  }

  // Render the guestbook
  function render() {
    global $gGuestbookUrl;

    $this->loadLanguageTexts();

    $guestbooks = $this->selectAll();

    $str = '';

    $str .= "\n<div class='guestbook_list'>";

    $str .= "\n<div class='guestbook_list_post_link'>"
      . "\n<a href='$gGuestbookUrl/post.php'>" . $this->websiteText[0] . "</a>"
      . "\n</div>";

    foreach ($guestbooks as $guestbook) {

      $body = $guestbook->getBody();
      $releaseDate = $guestbook->getReleaseDate();
      $userId = $guestbook->getUserId();
      $email = $guestbook->getEmail();
      $firstname = $guestbook->getFirstname();
      $lastname = $guestbook->getLastname();

      $body = nl2br($body);

      // Get the user details if a user is specified
      if ($user = $this->userUtils->selectById($userId)) {
        $firstname = $user->getFirstname();
        $lastname = $user->getLastname();
        $email = $user->getEmail();
      }

      if ($firstname || $lastname) {
        $strName = "$firstname $lastname";
      } else {
        $strName = $email;
      }

      if ($email) {
        $strName = "<a href='mailto:$email'>$strName</a>";
      }

      $str .= "\n<div class='guestbook_list_name'>$strName</div>";
      $str .= "\n<div class='guestbook_list_release'>$releaseDate</div>";
      $str .= "\n<div class='guestbook_list_body'>\"$body\"</div>";
    }

    $str .= "\n</div>";

    return($str);
  }

  // Render the styling elements for the editing of the css style properties
  function renderStylingElements() {
    $str = "<div class='guestbook_list'>The guestbook"
      . "<div class='guestbook_list_post_link'>"
      . "<a href='#'>The post link</a>"
      . "</div>"
      . "<div class='guestbook_list_name'>The name</div>"
      . "<div class='guestbook_list_release'>The release date</div>"
      . "<div class='guestbook_list_body'>The message body</div>"
      . "</div>";

    return($str);
  }

}

?>
