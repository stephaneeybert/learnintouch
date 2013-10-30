<?php

require_once("website.php");

$mlText = $languageUtils->getWebsiteText(__FILE__);

$userUtils->closeUserSession();

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$mlText[0]</div>";

$str .= "\n</div>";

$str .= LibHtml::urlDisplayRedirect($gHomeUrl);

$gTemplate->setPageContent($str);

require_once($gTemplatePath . "render.php");

?>
