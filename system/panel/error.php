<?php

require_once("website.php");

$REQUEST_URI = LibEnv::getEnvSERVER('REQUEST_URI');
$SCRIPT_URI = LibEnv::getEnvSERVER('SCRIPT_URI');
$QUERY_STRING = LibEnv::getEnvSERVER('QUERY_STRING');

$mlText = $languageUtils->getMlText(__FILE__);

$emailSubject = "$mlText[2]";
$emailBody = "$mlText[3]\n\n$mlText[4] $REQUEST_URI";

$websiteName = $profileUtils->getProfileValue("website.name");
$webmasterEmail = $profileUtils->getProfileValue("webmaster.email");
$webmasterName = $profileUtils->getProfileValue("webmaster.name");

if (LibEmail::validate($webmasterEmail)) {
//  LibEmail::sendMail($webmasterEmail, $webmasterName, $emailSubject, $emailBody, $webmasterEmail, $websiteName);
  }

$str = '';
$str .= "<br><br>";
$str .= "<table border='0' cellspacing='2' cellpadding='2' width='100%'>";
$str .= "<tr>";
$str .= "<td align=center><h1>$mlText[2]</h1></td>";
$str .= "</tr><tr>";
$str .= "<td align=center><br></td>";
$str .= "</tr><tr>";
$str .= "<td align=center>$mlText[0]</td>";
$str .= "</tr><tr>";
$str .= "<td align=center><br></td>";
$str .= "</tr><tr>";
$str .= "<td align=center>$mlText[1]</td>";
$str .= "</tr>";
$str .= "</table>";
$str .= "<br><br>";

$TEMPLATE_PAGE = $str;

require_once($gTemplatePath . "displayHtml.php");

?>
