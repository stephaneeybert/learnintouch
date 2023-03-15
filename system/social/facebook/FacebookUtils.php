<?

class FacebookUtils extends SocialUserDB {

  var $apiKey;
  var $applicationSecret;
  var $applicationId;
  var $activeSession;

  var $commonUtils;
  var $profileUtils;
  var $userUtils;

  function __construct() {
    parent::__construct();
  }

  function init() {
    $this->apiKey = $this->profileUtils->getFacebookApiKey();
    $this->applicationSecret = $this->profileUtils->getFacebookApplicationSecret();
    $this->applicationId = $this->profileUtils->getFacebookApplicationId();
  }

  function isEnabled() {
    $this->apiKey = $this->profileUtils->getFacebookApiKey();
    if ($this->apiKey) {
      return(true);
    } else {
      return(false);
    }
  }

  // Get the api key
  function getApiKey() {
    $this->init();

    return($this->apiKey);
  }

  // Get the application id
  function getApplicationId() {
    $this->init();

    return($this->applicationId);
  }

  function renderLoginJs($noRedirect) {
    global $gSocialUrl;
    global $gFacebookUrl;

    $postUserLoginUrl = $this->userUtils->getPostUserLoginUrl();

    $str = <<<HEREDOC
<script type="text/javascript">
function facebookLogin() {
  FB.login(function(response) {
    if (response.authResponse) {
      var accessToken = response.authResponse.accessToken;
      facebookDoOnUserLogin();
    }
  });
}

function facebookDoOnUserLogin() {
  var facebookUserId = '';
  window.fbEnsureInit(function () {
    facebookUserId = FB.Helper.getLoggedInUser();
    if (facebookUserId) {
      facebookUserId = encodeURIComponent(facebookUserId);
      var noRedirect = '$noRedirect';
      var url = '$gFacebookUrl/searchAndLoginUserFromFacebookUserId.php?facebookUserId='+facebookUserId+'&noRedirect='+noRedirect;
      ajaxAsynchronousRequest(url, facebookLoginOrRegisterUser);
    }
  });
}

function facebookLoginOrRegisterUser(responseText) {
  var response = eval('(' + responseText + ')');
  var userAutoRegister = response.userAutoRegister;
  var facebookUserId = response.facebookUserId;
  var userId = response.userId;
  var postUserLoginUrl = response.postUserLoginUrl;
  // Check if a userId was found for the facebookUserId
  if (userId) {
    if (postUserLoginUrl) {
      window.location = postUserLoginUrl;
    }
  } else {
    if (userAutoRegister) {
      // The facebook user is not yet associated to an existing user
      // Retrieve the user details and offer the user to register
      window.fbEnsureInit(function () {
        FB.getLoginStatus(function(response) {
          if (response.authResponse) {
          var query = FB.Data.query('select name,first_name,last_name,email,hometown_location, sex, pic_square from user where uid={0}', facebookUserId
);
          query.wait(function(rows) {
            var uid = rows[0].uid;
            var name = rows[0].name;
            var firstname = rows[0].first_name;
            var lastname = rows[0].last_name;
            var email = rows[0].email;
            var hometown_location = rows[0].hometown_location;
            var sex = rows[0].sex;
            var picture = rows[0].pic_square;

            email = encodeURIComponent(email);
            firstname = encodeURIComponent(firstname);
            lastname = encodeURIComponent(lastname);
            picture = encodeURIComponent(picture);
            var registerUrl = '$gSocialUrl/register.php?facebookUserId='+facebookUserId+'&email='+email+'&firstname='+firstname+'&lastname='+lastname;
            window.location = registerUrl;
          });
          }
        });
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

  function renderLogoutJs($redirectUrl = '') {
    global $gFacebookUrl;
    global $gHomeUrl;

    if (!$redirectUrl) {
      $redirectUrl = $gHomeUrl;
    }

    // A client redirect instead of a server redirect is required to ensure a Facebook logoout
    $str = <<<HEREDOC
<script type="text/javascript">
function redirect() {
  window.location = '$redirectUrl';
}
function facebookDoOnUserLogout() {
  // Give poor Facebook some time to log out
  var timerID = setTimeout('redirect()', 5000);
}
window.fbEnsureInit(function () {
  FB.logout(facebookDoOnUserLogout());
});
</script>
HEREDOC;

    return($str);
  }

  function publishNotification($title, $url, $body, $actionLinks, $description, $imageSrc, $autoPublish = false) {
    global $gImagesUserUrl;
    global $gJSNoStatus;

    $str = '';

    $apiKey = $this->getApiKey();

    if ($apiKey) {
      $actionLinksJs = '[';
      foreach ($actionLinks as $actionLink) {
        list($text, $href) = $actionLink;
        if ($actionLinksJs != '[') {
          $actionLinksJs .= ', ';
        }
        $actionLinksJs .= '{"name": "' . $text . '", "link": "' . $href . '"}';
      }
      $actionLinksJs .= ']';

      if ($autoPublish) {
        $jsAutoPublish = 'true';
      } else {
        $jsAutoPublish = 'false';
      }

      $IMAGE_COMMON_FACEBOOK = IMAGE_COMMON_FACEBOOK;
      $str = <<<HEREDOC
<script type="text/javascript">
function facebookPublish() {
  FB.ui({
    method: 'stream.publish',
    caption: "$body",
    name: "$title",
    picture: "$imageSrc",
    link: "$url",
    description: "$description",
    actions: $actionLinksJs
  },
  function(response) {
    if (response && response.post_id) {
      // The post has been published
    } else {
      // The post has NOT been published
    }
  });
}
</script>
<a href='javascript:facebookPublish();' $gJSNoStatus><img border='0' src='$gImagesUserUrl/$IMAGE_COMMON_FACEBOOK' title='Share on Facebook'></a>
HEREDOC;
    }

    return($str);
  }

  function renderLoginButton() {
    global $gImagesUserUrl;
    global $gJSNoStatus;

    $IMAGE_COMMON_FACEBOOK = IMAGE_COMMON_FACEBOOK;
    $str = "<a href='#' onclick='facebookLogin(); return false;' title='Log in with Facebook' $gJSNoStatus><img border='0' src='$gImagesUserUrl/$IMAGE_COMMON_FACEBOOK'></a>";


    return($str);
  }

  function renderLoginSetup($noRedirect) {
    global $gTemplate;

    $str = '';

    $apiKey = $this->getApiKey();

    if ($apiKey) {
      $str .= $this->renderLoginJs($noRedirect);
      $namespaceFBML = $this->renderNamespaceFBML();
      $gTemplate->setFacebookXMLNS($namespaceFBML);
    }

    return($str);
  }

  function renderLogoutSetup() {
    global $gTemplate;

    $str = '';

    $apiKey = $this->getApiKey();

    if ($apiKey) {
      $str .= $this->renderLogoutJs();
      $namespaceFBML = $this->renderNamespaceFBML();
      $gTemplate->setFacebookXMLNS($namespaceFBML);
      $this->commonUtils->preventPageCaching();
    }

    return($str);
  }

  // Render the doctype with the xml namespace required to parse the fbml elements
  function renderNamespaceFBML() {
    $str = <<<HEREDOC
xmlns:fb="https://www.facebook.com/2008/fbml"
HEREDOC;

    return($str);
  }

  // Render the library
  function renderLibrary() {
    $applicationId = $this->getApplicationId();

    $str = <<<HEREDOC
<div id="fb-root"></div>
<script type="text/javascript">
  window.fbAsyncInit = function() {
    FB.init({
      appId: '$applicationId',
      status: true,
      cookie: true,
      xfbml: true,
      oauth: true
    });
    fbApiInitialized = true;
  };

  (function() {
    var e = document.createElement('script');
    e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
    e.async = true;
    document.getElementById('fb-root').appendChild(e);
  }());

  function fbEnsureInit(callback) {
    if (!window.fbApiInitialized) {
      setTimeout(function() { fbEnsureInit(callback); }, 50);
    } else {
      if (callback) { callback(); }
    }
  }
</script>
HEREDOC;

    return($str);
  }

  function NOT_USED_renderUserProfile($apiKey) {
    $str = <<<HEREDOC
\n<div id='facebookUserProfile'></div>
<script type="text/javascript">
function facebookDisplayUserProfile() {
  var facebookUserProfile = document.getElementById("facebookUserProfile");
  facebookUserProfile.innerHTML = "<fb:profile-pic uid='loggedinuser' facebook-logo='true'></fb:profile-pic> Welcome, <fb:name uid='loggedinuser' useyou='false'></fb:name>. You are signed in with your Facebook account.";

  // Because this is XFBML, we need to tell Facebook to re-process the document
  FB.XFBML.Host.parseDomTree();
}

// Trigger a callback on page reload to display the user box
// without having to click on the connect button
FB.init("$apiKey", "xd_receiver.htm", {"ifUserConnected": facebookDisplayUserProfile});
</script>
HEREDOC;

    return($str);
  }

}

?>
