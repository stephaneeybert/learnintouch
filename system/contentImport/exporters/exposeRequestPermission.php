<?PHP

require_once("website.php");


// Check that the importer is registered
$domainName = LibEnv::getEnvHttpGET("domainName");
$importCertificate = LibEnv::getEnvHttpGET("importCertificate");
$permissionKey = LibEnv::getEnvHttpGET("permissionKey");

if ($contentImportUtils->isValidCertificate($importCertificate)) {
  $permissionKey = $contentImportUtils->registerPermissionRequestWasReceived($domainName, $permissionKey);

  print($permissionKey);
  }

?>
