<?php

require_once("website.php");

$documentId = LibEnv::getEnvHttpGET("documentId");

if ($document = $documentUtils->selectById($documentId)) {
  $file = $document->getFile();
  $filename = $documentUtils->filePath . $file;
  $str = LibHtml::urlRedirect("$gUtilsUrl/download.php?filename=$filename");
  printContent($str);
  return;
}

?>
