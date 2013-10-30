<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$adminUtils->checkForStaffLogin();

$panelUtils->setHeader($mlText[0], "$gWebsiteUrl/admin.php");

$panelUtils->addLine($panelUtils->addCell($mlText[4], "nb"), $panelUtils->addCell($mlText[9], "nb"), $panelUtils->addCell($mlText[7], "nb"), $panelUtils->addCell($mlText[5], "nb"));
$panelUtils->addLine();

$websites = $websiteUtils->selectAll();

$dbNames = $sqlToolsUtils->getDatabaseNames();

foreach ($websites as $website) {
  $websiteId = $website->getId();
  $dbName = $website->getDbName();
  if (!in_array($dbName, $dbNames)) {
    reportError("The name $dbName is not a database.", '', 'system/website/usage.php');
  }
  $name = $website->getName();
  $domainName = $website->getDomainName();
  $diskSpace = $website->getDiskSpace();

  $strDomainName = "<a href='$domainName'>$domainName</a>";

  // Get the disk usage
  $diskUsage = $websiteUtils->getTotalDiskUsage($name, $dbName);
  $percentDiskUsage = ($diskUsage * 100) / $diskSpace . '%';
  if ($percentDiskUsage > 100) {
    emailStaff("Disk usage: $percentDiskUsage - $domainName is using $diskUsage for an allocated disk space of $diskSpace");
  }

  $panelUtils->addLine($panelUtils->addCell($strDomainName, "n"), $panelUtils->addCell($percentDiskUsage, "n"), $panelUtils->addCell($diskUsage, "n"), $panelUtils->addCell($diskSpace, "n"));
}

$str = $panelUtils->render();

printAdminPage($str);

?>
