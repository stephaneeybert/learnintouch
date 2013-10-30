<?php

require_once("website.php");

$flashIntro = $flashUtils->renderFlashIntroObject();

$preferenceUtils->init($flashUtils->preferences);
$bgcolor = $preferenceUtils->getValue("FLASH_INTRO_PAGE_BG_COLOR");

// Note that the first line of the page must be the doctype one
// Otherwise IE 6 turns into quirks mode with its well known "IE box model bug"
// So no such line as xml version="1.0" encoding="ISO-8859-1"
// shall be the first line
//<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
//  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
//<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
$str = <<<HEREDOC
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
</head>
<body style='background-color:$bgcolor;'>
<table border='0' width='100%' cellpadding='0' cellspacing='0'>
<tr><td align='center'>$flashIntro</td></tr>
</table>
</body>
</html>
HEREDOC;

print($str);

?>
