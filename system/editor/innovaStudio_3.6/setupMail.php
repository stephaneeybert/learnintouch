<?php

$withCustomTagButton = true;

include($gInnovaHtmlEditorPath . "setupStandard.php");

$strMetaNames = $mailUtils->renderMetaNamesJs();

$gInnovaBodyClose = <<<HEREDOC

$oInnovaName.cmdAssetManager = "window.open('$gInnovaHtmlEditorUrl/imageMail.php', '', 'top=200,left=100,width=640,height=465,scrollbars,resizable');";
$oInnovaName.arrCustomTag = $strMetaNames;
$oInnovaName.REPLACE("body");

</script>
HEREDOC;

?>
