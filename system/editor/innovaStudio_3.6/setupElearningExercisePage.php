<?php

include($gInnovaHtmlEditorPath . "setupStandard.php");

$gInnovaBodyClose = <<<HEREDOC

$oInnovaName.cmdAssetManager = "window.open('$gInnovaHtmlEditorUrl/imageElearningExercisePage.php', '', 'top=200,left=100,width=640,height=465,scrollbars,resizable');";
$oInnovaName.REPLACE("$oInnovaContentName");

</script>
HEREDOC;

?>
