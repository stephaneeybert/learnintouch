<?php

require_once("website.php");

if (!isset($newsFeedId)) {
  $newsFeedId = LibEnv::getEnvHttpGET("newsFeedId");
}

print($newsFeedUtils->renderRSS($newsFeedId));

?>
