<?

class SocialInviterUtils {

  var $profileUtils;

  function __construct() {
  }

  function loadProperties() {
    $this->apiKey = $this->profileUtils->getFacebookApiKey();
    $this->applicationSecret = $this->profileUtils->getFacebookApplicationSecret();
    $this->applicationId = $this->profileUtils->getFacebookApplicationId();
  }

  // Get the api key
  function getApiKey() {
    return($this->apiKey);
  }

  function renderInviter() {
    global $gHomeUrl;
    global $PHP_SELF;

    $str = <<<HEREDOC
<script type="text/javascript">
function getContacts() {
  var url = '$gFacebookUrl/searchAndLoginUserFromFacebookUserId.php?facebookUserId='+facebookUserId+'&noRedirect='+noRedirect;
  ajaxAsynchronousRequest(url, facebookLoginOrRegisterUser);
}

function facebookLoginOrRegisterUser(responseText) {
  var response = eval('(' + responseText + ')');
  var facebookUserId = response.facebookUserId;
  var userId = response.userId;
  var postUserLoginUrl = response.postUserLoginUrl;
  if (userId) {
    // A userId was found for the facebookUserId
    // The facebook user is already associated to an existing user
    // The user is logged in
    if (postUserLoginUrl) {
      window.location = postUserLoginUrl;
    }
  } else {
    // The facebook user is not yet associated to an existing user
    // Ask the user his email address to create a user registration
    var firstname = '';
    var lastname = '';
    FB.Facebook.apiClient.users_getInfo(new Array(facebookUserId), ['first_name', 'last_name'], function(users, ex) {
      if (users.length > 0) {
        firstname = users[0].first_name;
        lastname = users[0].last_name;
        firstname = encodeURIComponent(firstname);
        lastname = encodeURIComponent(lastname);
        var registerUrl = '$gFacebookUrl/register.php?facebookUserId='+facebookUserId+'&firstname='+firstname+'&lastname='+lastname;
        window.open(registerUrl, '');
      }
    });
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

}

?>
