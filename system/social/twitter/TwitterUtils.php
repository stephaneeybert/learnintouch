<?

class TwitterUtils extends SocialUserDB {

  var $consumerKey;
  var $consumerSecret;

  var $commonUtils;
  var $popupUtils;
  var $profileUtils;
  var $userUtils;
  var $preferenceUtils;

  function TwitterUtils() {
    $this->SocialUserDB();
  }

  function loadProperties() {
    $this->consumerKey = $this->profileUtils->getTwitterConsumerKey();
    $this->consumerSecret = $this->profileUtils->getTwitterConsumerSecret();
  }

  function renderLoginButton() {
    global $gTwitterUrl;
    global $gImagesUserUrl;

    $str = $this->popupUtils->getDialogPopup("<img border='0' src='$gImagesUserUrl/" . IMAGE_COMMON_TWITTER . "' title='Log in with Twitter'>", "$gTwitterUrl/login.php", 600, 600);

    return($str);
  }

  function getAuthenticateToken() {
    global $gTwitterUrl;

    $this->loadProperties();

    $consumerKey = $this->consumerKey;
    $consumerSecret = $this->consumerSecret;

    $url = 'http://api.twitter.com/oauth/request_token';

    $params = array();
    $params['oauth_version'] = '1.0';
    $params['oauth_nonce'] = mt_rand();
    $params['oauth_timestamp'] = time();
    $params['oauth_consumer_key'] = $consumerKey;
    $params['oauth_callback'] = "$gTwitterUrl/login.php";
    $params['oauth_signature_method'] = 'HMAC-SHA1';
    $params['oauth_signature'] = $this->computeHmacSha1Signature('POST', $url, $params, $consumerSecret, null);

    $queryParameterString = LibUtils::oauthHttpBuildQuery($params);
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    $response = LibUtils::sendPostCurlRequest($url, $queryParameterString, 80, $headers);

    $authenticateToken = '';

    if (!empty($response)) {
      list($info, $header, $body) = $response;
      $parsedBody = LibUtils::queryStringToArray($body);
      if ($info['http_code'] == 200 && !empty($body) && $parsedBody['oauth_callback_confirmed'] == true) {
        $oauth_token = $parsedBody['oauth_token'];
        $oauth_token_secret = $parsedBody['oauth_token_secret'];
        $authenticateToken = LibUtils::rfc3986Decode($oauth_token);
      }
    }

    LibSession::putSessionValue(SOCIAL_SESSION_TWITTER_TOKEN, $authenticateToken);

    return($authenticateToken);
  }

  function convertRequestToAccessToken($oauth_token, $oauth_verifier) {
    global $gSocialUrl;

    $originalToken = LibSession::getSessionValue(SOCIAL_SESSION_TWITTER_TOKEN);
      
    if ($oauth_token == $originalToken) {
      $this->loadProperties();

      $consumerKey = $this->consumerKey;
      $consumerSecret = $this->consumerSecret;

      $url = 'http://api.twitter.com/oauth/access_token';

      $params = array();
      $params['oauth_version'] = '1.0';
      $params['oauth_nonce'] = mt_rand();
      $params['oauth_timestamp'] = time();
      $params['oauth_signature_method'] = 'HMAC-SHA1';
      $params['oauth_signature'] = $this->computeHmacSha1Signature('POST', $url, $params, $consumerSecret, null);
      $params['oauth_token'] = $oauth_token;
      $params['oauth_verifier'] = $oauth_verifier;
      
      $queryParameterString = LibUtils::oauthHttpBuildQuery($params);
      $headers[] = 'Content-Type: application/x-www-form-urlencoded';
      $response = LibUtils::sendPostCurlRequest($url, $queryParameterString, 80, $headers);

      if (!empty($response)) {
        list($info, $header, $body) = $response;
        $parsedBody = LibUtils::queryStringToArray($body);
        if ($info['http_code'] == 200 && !empty($body) && $parsedBody['user_id']) {
          $twitterUserId = $parsedBody['user_id'];
          $twitterUserName = $parsedBody['screen_name'];

          $email = '';
          if ($socialUser = $this->selectByTwitterUserId($twitterUserId)) {
            $userId = $socialUser->getUserId();

            if ($user = $this->userUtils->selectById($userId)) {
              $email = $user->getEmail();
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
              $email = '';
              $firstname = '';
              $lastname = '';
              $registerUrl = "$gSocialUrl/register.php?twitterUserId=$twitterUserId&email=$email&firstname=$firstname&lastname=$lastname";

              $str = LibHtml::urlDisplayRedirectParentWindow($registerUrl);
              printContent($str);
            }
          }
        }
      }
    }

    $str = LibJavascript::autoCloseWindow();
    printContent($str);
    return;
  }

  function setup($noRedirect) {
    $this->loadProperties();

    if ($this->consumerKey) {
      LibSession::putSessionValue(SOCIAL_SESSION_NO_REDIRECT, $noRedirect);

      return(true);
    }
  }

  function publishNotification($title, $url, $body, $actionLinks, $description, $imageSrc, $autoPublish = false) {
    global $gImagesUserUrl;
    global $gTwitterUrl;

    $message = urlencode($title . ' ' . $body . ' ' . $description . ' ' . $url);

    // TODO 
    //    $str = $this->popupUtils->getDialogPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_TWITTER . "' class='no_style_image_icon' title='Share on Twitter' alt='' />", "$gTwitterUrl/post.php?message=$message", 600, 400);
    $str = $this->popupUtils->getDialogPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_TWITTER . "' class='no_style_image_icon' title='Share on Twitter' alt='' />", "https://twitter.com/intent/tweet?source=webclient&text=$message", 600, 400);

    return($str);
  }

  function postNotification($message) {
    $this->loadProperties();

    $consumerSecret = $this->consumerSecret;

    $url = 'http://api.twitter.com/1/statuses/update.format';

    $params = array();
    $params['oauth_version'] = '1.0';
    $params['oauth_nonce'] = mt_rand();
    $params['oauth_timestamp'] = time();
    $params['oauth_signature_method'] = 'HMAC-SHA1';
    $params['oauth_signature'] = $this->computeHmacSha1Signature('POST', $url, $params, $consumerSecret, null);
    $params['status'] = $message;

    $queryParameterString = LibUtils::oauthHttpBuildQuery($params);
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    $response = LibUtils::sendPostCurlRequest($url, $queryParameterString, 80, $headers);

    if (!empty($response)) {
      list($info, $header, $body) = $response;
      if ($info['http_code'] == 200 && !empty($body)) {
        $parsedBody = LibUtils::queryStringToArray($body);
      }
    }
  }

  // Compute an OAuth HMAC-SHA1 signature
  function computeHmacSha1Signature($httpMethod, $url, $params, $consumerSecret, $tokenSecret) {
    $baseString = LibUtils::signatureBaseString($httpMethod, $url, $params);

    $signatureKey = LibUtils::rfc3986Encode($consumerSecret) . '&' . LibUtils::rfc3986Encode($tokenSecret);

    $signature = base64_encode(hash_hmac('sha1', $baseString, $signatureKey, true));

    return($signature);
  }

}

?>
