<?

class LinkedinUtils extends SocialUserDB {

  var $apiKey;

  var $commonUtils;
  var $profileUtils;
  var $userUtils;

  function LinkedinUtils() {
    $this->SocialUserDB();
  }

  function loadAPIKey() {
    $this->apiKey = $this->profileUtils->getLinkedinApiKey();
  }

  function getApiKey() {
    $this->loadAPIKey();

    return($this->apiKey);
  }

  function isEnabled() { 
    $this->apiKey = $this->profileUtils->getLinkedinApiKey();
    if ($this->apiKey) { 
      return(true);
    } else {
      return(false);
    }
  }

  function renderLoginJs($noRedirect) {
    global $gLinkedinUrl;
    global $gSocialUrl;

    $postUserLoginUrl = $this->userUtils->getPostUserLoginUrl();

    $str = <<<HEREDOC
<script type="text/javascript">
function linkedinLogin() {
  IN.User.authorize();
  IN.Event.on(IN, 'auth', function(){
    linkedinDoOnUserLogin();
  });
}

function linkedinDoOnUserLogin() {
  IN.API.Profile("me").result(function(profiles) {
    var me = profiles.values[0];
    var linkedinUserId = me.id;
    var noRedirect = '$noRedirect';
    var url = '$gLinkedinUrl/searchAndLoginUserFromLinkedinUserId.php?linkedinUserId='+linkedinUserId+'&noRedirect='+noRedirect;
    ajaxAsynchronousRequest(url, linkedinLoginOrRegisterUser);
  });
}

function linkedinLoginOrRegisterUser(responseText) {
  var response = eval('(' + responseText + ')');
  var userAutoRegister = response.userAutoRegister;
  var linkedinUserId = response.linkedinUserId;
  var userId = response.userId;
  var postUserLoginUrl = response.postUserLoginUrl;
  // Check if a userId was found for the linkedinUserId 
  if (userId) {
    if (postUserLoginUrl) {
      window.location = postUserLoginUrl;
    }
  } else {
    if (userAutoRegister) {
      // The LinkedIn user is not yet associated to an existing user
      // Retrieve the user details and offer the user to register
      IN.API.Profile("me").result(function(profiles) {
        var me = profiles.values[0];
        var linkedinUserId = me.id;
        var firstname = me.firstName;
        var lastname = me.lastName;
        var email = me.email;
        var registerUrl = '$gSocialUrl/register.php?linkedinUserId='+linkedinUserId+'&email='+email+'&firstname='+firstname+'&lastname='+lastname;
        window.location = registerUrl;
      });
    } else {
      window.location = '$postUserLoginUrl';
    }
  }

  // Call a function after the login, only if the function exists
  if (typeof (window.postSocialLogin) == 'function') {
    postSocialLogin(response);
  }
}
</script>
HEREDOC;

    return($str);
  }

  function publishNotification($title, $url, $body, $actionLinks, $description, $imageSrc, $autoPublish = false) {
    global $gImagesUserUrl;
    global $gJSNoStatus;

    $str = '';

    if ($this->getApiKey()) {
      $IMAGE_COMMON_LINKEDIN = IMAGE_COMMON_LINKEDIN;
      $str = <<<HEREDOC
<script type="text/javascript">
  function linkedInShare() {
    IN.UI.Share().params({
      url: "$url",
      "title": "$title",
      "description": "$imageSrc $description"
// The following properties are not yet supported 
//      "submitted-image-url": "$imageSrc", // Not yet supported by LinkedIn
//      "comment": "$body" // Not yet supported by LinkedIn
    }).place()
  }
</script>
<a href='javascript:linkedInShare();' $gJSNoStatus><img border='0' src='$gImagesUserUrl/$IMAGE_COMMON_LINKEDIN' title='Share on LinkedIn'></a>
HEREDOC;
    }

    return($str);
  }

  function renderLoginButton() {
    global $gImagesUserUrl;
    global $gJSNoStatus;

    $IMAGE_COMMON_LINKEDIN = IMAGE_COMMON_LINKEDIN;
    $str = "<a href='#' onclick='linkedinLogin(); return false;' title='Log in with LinkedIn' $gJSNoStatus><img border='0' src='$gImagesUserUrl/$IMAGE_COMMON_LINKEDIN'></a>";

    return($str);
  }

  function renderLoginSetup($noRedirect) {
    global $gTemplate;

    $str = '';

    if ($this->getApiKey()) {
      $str .= $this->renderLoginJs($noRedirect);
    }

    return($str);
  }

  // Render the library
  function renderLibrary() {
    $apiKey = $this->getApiKey();

    // Do not auto log in the user but let him click on the "Login with LinkedIn" button
    $str = <<<HEREDOC
<script type='text/javascript' src='http://platform.linkedin.com/in.js'>
api_key: $apiKey
authorize: false
</script>
HEREDOC;

    return($str);
  }

}

?>
