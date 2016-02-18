<?php

// Redirect to a page
function redirectTo($url, $delay = '') {
  if (!$delay) {
    $delay = $gRedirectDelay;
  }

  LibHtml::urlRedirect($url, $delay);
}

// Get an error message
function formatErrorMessage($error) {
  $webmasterEmail = $profileUtils->getProfileValue("webmaster.email");
  $websiteEmail = $profileUtils->getProfileValue("website.email");

  $str = '';
  $str .= formatMessageContent($error);

  if ($webmasterEmail && LibEmail::validate($webmasterEmail)) {
    $str .= "\n<br><br><h1>Webmaster: <a href='mailto:$webmasterEmail'>$webmasterEmail</a>";
  }

  if ($websiteEmail && LibEmail::validate($websiteEmail)) {
    $str .= "\n<br><br>Website: <a href='mailto:$websiteEmail'>$websiteEmail</a></h1>";
  }

  return($str);
}

// Format a message
function formatMessageContent($message) {
  global $gPanelUrl;

  $str = ''
    . "\n<html>\n<head>"
    . "\n</head>"
    . "\n<body>"
    . "\n<table border='0' width='100%' cellpadding='0' cellspacing='0'>"
    . "<tr><td align='center'><br><br><div class='warning'>"
    . $message
    . "</div></td></tr>"
    . "</table>"
    . "\n<link href='$gPanelUrl/css/default.css' rel='stylesheet' type='text/css' />"
    . "\n</body></html>";

  return($str);
}

// Print a warning message
function printMessage($str) {
  $str = formatMessageContent($str);

  print($str);

  flush();
}

// Print some content
function printContent($str, $head = '', $bodyOnLoad = '') {

  $str = ''
    . "\n<html>\n<head>"
    . "\n$head\n"
    . "\n</head>"
    . "\n<body onLoad=\"$bodyOnLoad\">"
    . $str
    . "\n</body></html>";

  print($str);

  flush();
}

// Print an admin page
function  printAdminPage($body, $head = '', $bodyOnLoad = '') {
  global $gPanelUrl;
  global $gJsUrl;
  global $gWebsiteTitle;
  global $gAdminSessionLogin;
  global $gIsPhoneClient;

  if ($gAdminSessionLogin) {
    $gAdminSessionLogin = "[$gAdminSessionLogin]";
  }

  $str = <<<HEREDOC
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>$gAdminSessionLogin $gWebsiteTitle</title>
<meta http-equiv='content-type' content='text/html; charset=iso-8859-1'>
$head
</head>
<body onLoad="setPageTitle(); formFocus(); $bodyOnLoad">
 $body &nbsp;
<script type="text/javascript">
$(document).ready(function() {
  $(".tooltip").wTooltip({
    follow: false,
    fadeIn: 300,
    fadeOut: 500,
    delay: 500,
    style: {
      width: "500px", // Required to avoid the tooltip being displayed off the right
      background: "#ffffff",
      color: "#000",
      fontSize: 14
    }
  });
});
</script>

<script type="text/javascript">
function setPageTitle() {
  parent.document.title = "$gAdminSessionLogin $gWebsiteTitle";
}
</script>
<script type='text/javascript' src='$gJsUrl/popup.js'></script>
<script type='text/javascript' src='$gJsUrl/ajax.js'></script>
<script type='text/javascript' src='$gJsUrl/utilities.js'></script>
<script type='text/javascript' src='$gJsUrl/cookies.js'></script>
<script type='text/javascript' src='$gJsUrl/adddomloadevent-compressed.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/jquery-1.7.1.min.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/datepicker/language/jquery.ui.datepicker-da.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/datepicker/language/jquery.ui.datepicker-de.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/datepicker/language/jquery.ui.datepicker-en-GB.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/datepicker/language/jquery.ui.datepicker-es.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/datepicker/language/jquery.ui.datepicker-fi.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/datepicker/language/jquery.ui.datepicker-fr.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/datepicker/language/jquery.ui.datepicker-it.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/datepicker/language/jquery.ui.datepicker-nl.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/datepicker/language/jquery.ui.datepicker-no.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/datepicker/language/jquery.ui.datepicker-ru.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/datepicker/language/jquery.ui.datepicker-sv.js'></script>
<link rel='stylesheet' type='text/css' href='$gJsUrl/jquery/ui/css/smoothness/jquery-ui-1.8.17.custom.css' />
<script type="text/javascript" src="$gJsUrl/socket/socket.io.min.js"></script>
<script type="text/javascript" src="$gJsUrl/jquery/ui/jquery-ui-1.8.17.custom.min.js"></script>
<script type="text/javascript" src="$gJsUrl/jquery/jquery-ui-autocomplete-extension/scottgonzalez-jquery-ui-extensions-e34c945/autocomplete/jquery.ui.autocomplete.html.js"></script>
<script type='text/javascript' src='$gJsUrl/jquery/wtooltip.min.js'></script>
<script type="text/javascript" src="$gJsUrl/jquery/cycle/jquery.cycle.all.min.2.99.js"></script>
<link rel='stylesheet' type='text/css' href='$gPanelUrl/css/default.css' />
</body>
</html>
HEREDOC;

  if ($gIsPhoneClient) {
    $str .= <<<HEREDOC
<script type="text/javascript" src="$gJsUrl/jquery/ui/furf-jquery-ui-touch-punch-766dcf9/jquery.ui.touch-punch.min.js"></script>
HEREDOC;
  }

  print($str);
}

?>
