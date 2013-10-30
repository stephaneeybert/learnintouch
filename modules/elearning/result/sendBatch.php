<?PHP

require_once("website.php");

$elearningResultId = LibEnv::getEnvHttpGET("elearningResultId");

if ($elearningResultId) {
  $elearningResultUtils->sendResult($elearningResultId);
}

?>
