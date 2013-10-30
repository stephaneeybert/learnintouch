<?

class GoogleUtils extends SocialUserDB {

  var $clientId;
  var $clientSecret;

  var $commonUtils;
  var $profileUtils;
  var $popupUtils;
  var $userUtils;
  var $preferenceUtils;

  function GoogleUtils() {
    $this->SocialUserDB();
  }

  function loadProperties() {
    $this->clientId = $this->profileUtils->getGoogleClientId();
    $this->clientSecret = $this->profileUtils->getGoogleClientSecret();
  }

  function renderLoginButton() {
    global $gGoogleUrl;
    global $gImagesUserUrl;

    $str = $this->popupUtils->getDialogPopup("<img border='0' src='$gImagesUserUrl/" . IMAGE_COMMON_GOOGLEPLUS . "' title='Log in with Google'>", "$gGoogleUrl/login.php", 600, 600);

    return($str);
  }

  function getAuthenticateCodeUrl() {
    global $gGoogleUrl;

    $this->loadProperties();

    $redirectUrl = urlencode("$gGoogleUrl/login.php");

    $scope = "https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email";
    $scope = urlencode($scope);

    $url = "https://accounts.google.com/o/oauth2/auth"
      . "?client_id=" . $this->clientId
      . "&redirect_uri=$redirectUrl"
      . "&scope=$scope"
      . "&response_type=code";

    return($url);
  }

  function processLoginRequest($code) {
    global $gSocialUrl;

    $accessToken = $this->getAccessToken($code);

    $googleUser = $this->getGoogleUser($accessToken);

    $email = '';
    if (!empty($googleUser) && $googleUser->id) {
      $googleUserId = $googleUser->id;

      if ($socialUser = $this->selectByGoogleUserId($googleUserId)) {
        $userId = $socialUser->getUserId();

        if ($user = $this->userUtils->selectById($userId)) {
          $email = $user->getEmail();
        }
      }
    }

    if ($email) {
      if (!$this->userUtils->noLongerValidUserCannotLogin($email)) {
        $this->userUtils->openUserSession($email);

        $noRedirect = LibSession::getSessionValue(SOCIAL_SESSION_NO_REDIRECT);
        if (!$noRedirect) {
          $postUserLoginUrl = $this->userUtils->getPostUserLoginUrl();
          $str = LibHtml::urlDisplayRedirectParentWindow($postUserLoginUrl);
          printContent($str);
        } else {
          $str = LibJavascript::reloadParentWindow();
          printContent($str);
        }
      }
    } else {
      $this->preferenceUtils->init($this->userUtils->preferences);
      $userAutoRegister = $this->preferenceUtils->getValue("USER_AUTO_REGISTER");
      if ($userAutoRegister) {
        $email = $googleUser->email;
        $firstname = $googleUser->given_name;
        $lastname = $googleUser->family_name;

        $registerUrl = "$gSocialUrl/register.php?googleUserId=$googleUserId&email=$email&firstname=$firstname&lastname=$lastname";
        $str = LibHtml::urlDisplayRedirectParentWindow($registerUrl);
        printContent($str);
      }
    }

    $str = LibJavascript::autoCloseWindow();
    printContent($str);
    return;
  }

  function getAccessToken($code) {
    global $gGoogleUrl;

    $this->loadProperties();

    $clientId = $this->clientId;
    $clientSecret = $this->clientSecret;

    $redirectUrl = "$gGoogleUrl/login.php";

    $tokenPost = array(
      "code" => $code,
      "client_id" => $clientId,
      "client_secret" => $clientSecret,
      "redirect_uri" => "$redirectUrl",
      "grant_type" => "authorization_code"
    );

    $url = "https://accounts.google.com/o/oauth2/token";

    $headers[] = "{'Content-Type'} : {'application/x-www-form-urlencoded'}";

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($tokenPost));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $json_response = curl_exec($curl);
    curl_close($curl);

    $authentication = json_decode($json_response);

    if (isset($authentication->error) && $authentication->error == 'invalid_request') {
      return('');
    }

    $accessToken = $authentication->access_token;

    return($accessToken);
  }

  function getGoogleUser($accessToken) {
    $googleUser = $this->apiRequest($accessToken, "https://www.googleapis.com/oauth2/v1/userinfo");

    return($googleUser);
  }

  function apiRequest($accessToken, $url) {
    $headers[0] = "Authorization: Bearer " . $accessToken;

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $json_response = curl_exec($curl);
    curl_close($curl);

    $response = json_decode($json_response);

    return($response);      
  }

  function setup($noRedirect) {
    $this->loadProperties();

    if ($this->clientId) {
      LibSession::putSessionValue(SOCIAL_SESSION_NO_REDIRECT, $noRedirect);

      return(true);
    }
  }

  function publishNotification($title, $url, $body, $actionLinks, $description, $imageSrc, $autoPublish = false) {
    global $gImagesUserUrl;
    global $gJSNoStatus;

    //TODO Google is not yet offering API stream posting

    $IMAGE_COMMON_GOOGLEPLUS = IMAGE_COMMON_GOOGLEPLUS;
    $str = <<<HEREDOC
<script type="text/javascript">
function googlePlusPublish() {
}
</script>
<a href='javascript:googlePlusPublish();' $gJSNoStatus><img border='0' src='$gImagesUserUrl/$IMAGE_COMMON_GOOGLEPLUS' title='Share on Google+'></a>
HEREDOC;
  }

}

?>
