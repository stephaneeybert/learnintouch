<?php

require_once("website.php");

$str = '';

$str .= "\n<div class='system'>";

$facebookLoginButton = $facebookUtils->renderLoginButton();

$str .= <<<HEREDOC
\n<div>
  <h3>Login using your Facebook account</h3>
  <div id="userBox">
    $facebookLoginButton
  </div> 
  <div id="logout"></div> 
</div> 
<script type="text/javascript">

//addLoadListener(searchAndLoginUserFromFacebookUserId);

function searchAndLoginUserFromFacebookUserId() {
  var facebookUserId = FB.Connect.get_loggedInUser();
  if (facebookUserId) {
    facebookUserId = encodeURIComponent(facebookUserId);
    var url = '$gFacebookUrl/searchAndLoginUserFromFacebookUserId.php?facebookUserId='+facebookUserId;
    ajaxAsynchronousRequest(url, loginOrRegisterUser);
  }
}

function loginOrRegisterUser(responseText) {
  var response = eval('(' + responseText + ')');
  var facebookUserId = response.facebookUserId;
  var userId = response.userId;
  var postUserLoginUrl = response.postUserLoginUrl;
  if (userId) {
    // A userId was found for the facebookUserId
    // The facebook user is already associated to an existing user
    // The user is logged in
    // Redirect to a page
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
        window.location = registerUrl;
      }
    });
  }
}

function facebookDoOnUserLogin() {
  searchAndLoginUserFromFacebookUserId();
}

function facebookDoOnUserLogout() {
  var url = '$gUserUrl/ajaxLogout.php';
  ajaxAsynchronousRequest(url, handleLogout);

  clearUserBox();
}

function facebookDoOnUserConnected() {
}

function handleLogout(responseText) {
  var response = eval('(' + responseText + ')');
}

function updateUserBox() {
  var userBox = document.getElementById("userBox");
  userBox.innerHTML = "<span>" + "<fb:profile-pic uid='loggedinuser' facebook-logo='true'></fb:profile-pic> Welcome, <fb:name uid='loggedinuser' useyou='false'></fb:name>. You are signed in with your Facebook account." + "</span>"; 
  var logout = document.getElementById("logout");
  logout.innerHTML = "<a href='#' onclick='javascript:FB.Connect.logout(facebookDoOnUserLogout()); return(false);'>Logout</a>";
  // Because this is XFBML, we need to tell Facebook to re-process the document 
  FB.XFBML.Host.parseDomTree();
}

function clearUserBox() {
  var userBox = document.getElementById("userBox");
  userBox.innerHTML = "<span>Bye bye !</span>"; 
  var logout = document.getElementById("logout");
  logout.innerHTML = "";
}

</script> 
HEREDOC;

$str .= "\n</div>";

$apiKey = $facebookUtils->getApiKey();

$str .= <<<HEREDOC
\n<script type="text/javascript">
// Trigger a callback on page reload to display the user box
// without having to click on the connect button
FB.init("$apiKey", "xd_receiver.htm", {"ifUserConnected": facebookDoOnUserConnected});
</script> 
HEREDOC;

$gTemplate->setPageContent($str);
$namespaceFBML = $facebookUtils->renderNamespaceFBML();
$gTemplate->setFacebookXMLNS($namespaceFBML);
$commonUtils->preventPageCaching();
require_once($gTemplatePath . "render.php");

?>
