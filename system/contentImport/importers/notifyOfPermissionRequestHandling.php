<?PHP

require_once("website.php");


// Check that the importer is registered
$importCertificate = LibEnv::getEnvHttpGET("importCertificate");
$domainName = LibEnv::getEnvHttpGET("domainName");
$permissionKey = LibEnv::getEnvHttpGET("permissionKey");
$permissionStatus = LibEnv::getEnvHttpGET("permissionStatus");

if ($contentImportUtils->isValidCertificate($importCertificate)) {
  $contentImportUtils->registerPermissionHandling($domainName, $permissionKey, $permissionStatus);
  }

?>
