<?PHP

require_once("website.php");

$strHead = <<<HEREDOC
<script src='$gJsUrl/jquery/jquery-1.7.1.min.js' type='text/javascript'></script>
<link rel="stylesheet" type="text/css" href="$gApiUrl/phpfreechat-2.1.0/client/themes/default/jquery.phpfreechat.min.css" />
<script src="$gApiUrl/phpfreechat-2.1.0/client/jquery.phpfreechat.min.js" type="text/javascript"></script>
HEREDOC;

$strBody = <<<HEREDOC
<div id="chatsystem"><a href="http://www.phpfreechat.net">Creating chat rooms everywhere - phpFreeChat</a></div>
<script type="text/javascript">
  $('#chatsystem').phpfreechat({ 
    serverUrl: '$gApiUrl/phpfreechat-2.1.0/server' 
  });
</script>
HEREDOC;

$userId = LibEnv::getEnvHttpGET("userId");

printContent($strBody, $strHead);

?>
