<?PHP

require_once("website.php");

$userId = LibEnv::getEnvHttpGET("userId");
$tokenName = LibEnv::getEnvHttpGET("tokenName");
$tokenValue = LibEnv::getEnvHttpGET("tokenValue");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

if ($uniqueTokenUtils->isValid($tokenName, $tokenValue)) {
  if ($user = $userUtils->selectById($userId)) {
    $str .= "\n<div class='system_comment'>$websiteText[1]</div>";
    $str .= "\n<div class='system_comment'>$websiteText[4] <a href='$gUserUrl/login.php' $gJSNoStatus>$websiteText[6]</a> $websiteText[5]";
    $user->setUnconfirmedEmail(false);
    $userUtils->update($user);
  } else {
    $str .= "\n<div class='system_comment'>$websiteText[2]</div>";
    $subject = $websiteText[8] . ' ' . $userId . ' ' . $websiteText[9];
    $subject = urlencode($subject);
    $str .= "\n<div class='system_comment'>$websiteText[4] <a href='$gContactUrl/post.php?subject=$subject' $gJSNoStatus>$websiteText[6]</a> $websiteText[7]";
  }
} else {
  $subject = $websiteText[8] . ' ' . $userId . ' ' . $websiteText[10];
  $subject = urlencode($subject);
  $str .= "\n<div class='system_comment'>$websiteText[3]</div>";
  $str .= "\n<div class='system_comment'>$websiteText[4] <a href='$gContactUrl/post.php?subject=$subject' $gJSNoStatus>$websiteText[6]</a> $websiteText[7]";
}

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
