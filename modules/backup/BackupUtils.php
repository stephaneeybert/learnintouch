<?

class BackupUtils extends BackupDB {

  var $mlText;

  var $backupFilePath;
  var $backupFileUrl;

  var $latestBackupFilePath;
  var $latestBackupFileUrl;

  var $exportFilePath;
  var $exportFileUrl;

  // The tables that are not backed up
  var $rubishTables;
  var $secretTables;

  // Prefix of the database backup file names
  var $backupFilePrefix;

  // Names of the data formats
  var $backupDataFormats;

  var $preferences;

  var $languageUtils;
  var $preferenceUtils;
  var $clockUtils;
  var $websiteUtils;

  function BackupUtils() {
    $this->BackupDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;
    global $gAccountPath;
    global $gAccountUrl;

    $this->backupFilePath = $gAccountPath . 'backup/file/';
    $this->backupFileUrl = $gAccountUrl . '/backup/file';
    $this->latestBackupFilePath = $gDataPath . 'backup/';
    $this->latestBackupFileUrl = $gDataUrl . '/backup';
    $this->exportFilePath = $gAccountPath . 'backup/export/';
    $this->exportFileUrl = $gAccountUrl . '/backup/export';

    $this->rubishTables = array('statistics_visit', 'syslog');
    $this->secretTables = array('template_model', 'template_container', 'template_element', 'template_tag', 'template_property_set', 'template_property');

    $this->backupFilePrefix = 'backup_';

    $this->backupDataFormats = array("INSERT", "CSV");

    $this-> createDirectories();
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;
    global $gAccountPath;

    if (!is_dir($this->backupFilePath)) {
      if (!is_dir($gAccountPath . 'backup')) {
        mkdir($gAccountPath . 'backup');
      }
      mkdir($this->backupFilePath, 0755);
    }

    if (!is_dir($this->latestBackupFilePath)) {
      mkdir($this->latestBackupFilePath, 0755);
    }

    if (!is_dir($this->exportFilePath)) {
      mkdir($this->exportFilePath, 0755);
    }
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  function loadPreferences() {
    $this->loadLanguageTexts();

    $this->preferences = array(
      "BACKUP_MAIL_ON_BACKUP" =>
      array($this->mlText[0], $this->mlText[1], PREFERENCE_TYPE_SELECT, array('' => $this->mlText[2], BACKUP_MAIL_WEEKLY => $this->mlText[3], BACKUP_MAIL_MONTHLY => $this->mlText[4], BACKUP_MAIL_ALWAYS => $this->mlText[5])),
      );

    $this->preferenceUtils->init($this->preferences);
  }

  // Check if an email is to be sent
  function mailOnBackup() {
    $mail = false;

    $mailOnBackup = $this->preferenceUtils->getValue("BACKUP_MAIL_ON_BACKUP");

    if ($mailOnBackup == BACKUP_MAIL_ALWAYS) {
      $mail = true;
    } else if ($mailOnBackup == BACKUP_MAIL_WEEKLY && $this->clockUtils->isFirstDayOfWeek()) {
      $mail = true;
    } else if ($mailOnBackup == BACKUP_MAIL_MONTHLY && $this->clockUtils->isFirstDayOfMonth()) {
      $mail = true;
    }

    return($mail);
  }

  // Backup the engine database
  function backupCommonDatabase($filename, $tableStructure = true, $tableData = true, $dataFormat = 0, $fullInsert = false) {
    $dbName = DB_COMMON_DB_NAME;

    return($this->backupTables($filename, $dbName, $tableStructure, $tableData, $dataFormat, $fullInsert, true));
  }

  // Backup the account database
  function backupDatabase($filename, $tableStructure = true, $tableData = true, $dataFormat = 0, $fullInsert = false, $noSecret = false) {
    $dbName = DB_NAME;

    return($this->backupTables($filename, $dbName, $tableStructure, $tableData, $dataFormat, $fullInsert, $noSecret));
  }

  // Backup a database
  // The function has the same options than the phpmyadmin tool, that is, backup the structure and/or the data, output csv or sql
  // The options are :
  // $tableStructure : if true then save the table structure
  // $tableData : if true then save the table data
  // $dataFormat : the data format ('INSERT' = INSERT statements, 'CSV' = comma separated data)
  // $fullInsert (optional) : if true then INSERT with field names
  function backupTables($filename, $dbName, $tableStructure, $tableData, $dataFormat, $fullInsert, $noSecret) {

    // If the file aready exists, delete it before creating a new empty one
    if (file_exists($filename)) {
      unlink($filename);
    }

    $options = '--default-character-set=utf8 --skip-extended-insert ';

    if ($tableStructure != 1) {
      $options .= ' --no-create-info';
    } else {
      $options .= ' --add-drop-table';
    }

    if ($tableData != 1) {
      $options .= ' --no-data';
    }

    $dbUser = DB_USER;
    $dbPassword = DB_PASS;

    $lastLine = system("mysqldump --user=$dbUser --password=$dbPassword $options $dbName > $filename", $returnValue);

    return(true);
  }

  // This method is not being used any longer as it was wrongly converting the NULL sql values
  // into empty php values, preventing the NULL values to be inserted back into a database
  // schema as required
  function NOT_USED_backupTables($filename, $tableStructure, $tableData, $dataFormat, $fullInsert, $noSecret) {
    $dataFormat = strtoupper($dataFormat);

    // If the file aready exists, delete it before creating a new empty one
    if (file_exists($filename)) {
      unlink($filename);
    }

    // Open the file
    $fp = fopen($filename, "w");

    // Check that the file pointer is valid
    if (!is_resource($fp)) {
      return(false);
    }

    // Get the list of tables
    if ($tableNames = $this->getTableNames()) {
      foreach ($tableNames as $tableName) {
        // Output the sql statements used to create the table structure
        if (!$this->isSecretTable($tableName, $noSecret) && $tableStructure == true && $dataFormat == 0) {
          fwrite($fp, "\n\nDROP TABLE IF EXISTS `$tableName`;\n");
          if ($resultCreate = $this->dao->showCreateTable($tableName)) {
            $row = $resultCreate->getRow(0);
            $schema = $row[1] . ";";
            fwrite($fp, "$schema\n\n");
          }
        }

        // Output the sql statements used to create the table data
        if ($tableData == true) {
          if ($resultData = $this->dao->selectAllFromTable($tableName)) {
            if ($resultData->getRowCount() > 0) {
              $sFieldnames = '';
              if ($fullInsert == true) {
                for ($j = 0; $j < $resultData->getFieldCount(); $j++) {
                  $sFieldnames .= "`" . $resultData->getFieldName($j) . "`,";
                }
                $sFieldnames = "(" . substr($sFieldnames, 0, -1) . ")";
              }
              $sInsert = "INSERT INTO `$tableName` $sFieldnames values ";

              for ($k = 0; $k < $resultData->getRowCount(); $k++) {
                $rowData = $resultData->getRow($k, DB_RESULT_ARRAY_ASSOC);
                $strRow = "<quote>" . implode("<quote>,<quote>", $rowData) . "<quote>";
                $strRow = str_replace("<quote>", "'", addslashes($strRow));

                if ($dataFormat == 0) {
                  $strRow = "$sInsert($strRow);";
                }
                fwrite($fp, "$strRow\n");
              }
            }
          }
        }
      }
    }

    fclose($fp);

    return(true);
  }

  // Backup the account data directory
  function backupDataPath($filename) {
    global $gDataPath;

    $dirList = array();

    $dirs = LibDir::getDirNames($gDataPath);
    if (is_array($dirs)) {
      $i = 0;
      foreach ($dirs as $dir) {
        // Do not backup some directories
        if ($dir != "." && $dir != ".." && $dir != "backup") {
          $dirList[$i]= $gDataPath . $dir;
          $i++;
        }
      }
    }
    sort($dirList);

    $success = true;
    try {
      $tarArchive = new PharData($filename);
      $tarArchive->buildFromDirectory($gDataPath);
      if (Phar::canCompress()) {
        $tarArchive->compress(\Phar::GZ);
        unset($tarArchive);
        unlink($filename);
      }
    } catch (Exception $e) {
      $success = false;
    }

    return($success);
  }

  // Backup the language files
  function backupLanguageFiles($filename, $languageCode) {
    global $gDataPath;

    $languageFiles = $this->languageUtils->getLanguageFilenames($languageCode);

    $success = true;
    try {
      $tarArchive = new PharData($filename);
      foreach ($languageFiles as $languageFile) {
        $tarArchive->addFile($languageFile);
      }
      $tarArchive->compress(Phar::GZ);
      $success = true;
    } catch (Exception $e) {
      $success = false;
    }

    return($success);
  }

  // Delete the backup files of the account
  function deleteBackup() {
    $accountName = $this->websiteUtils->getSetupWebsiteName();

    $this->deleteAccountBackup($accountName);
  }

  // Render sql file url
  function renderSqlFileUrl($filename) {
    $url = $this->backupFileUrl . '/' . $filename;

    return($url);
  }

  // Render the file suffix
  function renderBackupFileSuffix() {
    if (Phar::canCompress()) {
      return("tar.gz");
    } else {
      return("tar");
    }
  }
  // Render data file url
  function renderBackupFileUrl() {
    global $gAccountUrl;

    $url = $gAccountUrl . '/' . $this->websiteUtils->getSetupWebsiteName() . "." . $this->renderBackupFileSuffix();

    return($url);
  }

  // Render data file path
  function renderBackupFilePath() {
    global $gAccountPath;

    $backupFilePath = $gAccountPath . $this->websiteUtils->getSetupWebsiteName() . "." . $this->renderBackupFileSuffix();

    return($backupFilePath);
  }

  // Delete the backup files of a specified account
  function deleteAccountBackup($accountName) {
    global $gAccountPath;

    if (!$accountName) {
      return;
    }

    // Create the file name
    $dataFilename = $this->renderBackupFilePath();

    // Delete the main backup file
    $this->deleteBackupFile($dataFilename);

    // Delete the old database backup files
    $this->deleteDBBackupFiles();
  }

  // Delete the backup file
  function deleteBackupFile($filename) {
    if (file_exists($filename)) {
      unlink($filename);
    }
  }

  // Delete the old backup database files
  // Keep a minimum number of database files
  // The database files are moved out of the tar-ed data/ directory
  // so as not to have all of them in the tar archive, but only the last one
  function deleteDBBackupFiles($min = 3) {
    if (($backupDir = opendir($this->backupFilePath)) == false) {
      return(false);
    }

    if (($latestBackupDir = opendir($this->latestBackupFilePath)) == false) {
      return(false);
    }

    // Move the previous backup db file in a parent directory
    while (($fileName = readdir($latestBackupDir)) !== false) {
      if (!is_dir($this->latestBackupFilePath . $fileName) && is_file($this->latestBackupFilePath . $fileName)) {
        rename($this->latestBackupFilePath . $fileName, $this->backupFilePath . $fileName);
      }
    }
    closedir($latestBackupDir);

    $fileTimestamps = Array();
    $fileNames = Array();
    while (($fileName = readdir($backupDir)) !== false) {
      if (!is_dir($fileName)) {
        $fileTimestamp = filemtime($this->backupFilePath . $fileName);
        $fileTimestamps[$fileName] = $fileTimestamp;
      }
    }
    // Sort on the timestamps in reverse order
    arsort($fileTimestamps);

    $i = 1;
    foreach ($fileTimestamps as $fileName => $fileTimestamp) {
      if ($i > $min) {
        if (file_exists($this->backupFilePath . $fileName)) {
          unlink($this->backupFilePath . $fileName);
        }
      }
      $i++;
    }
    closedir($backupDir);
  }

  // Check if a table is secret
  function isSecretTable($tableName, $noSecret) {

    // Check if some tables are considered secret
    if ($noSecret) {
      return(false);
    }

    if (in_array($tableName, $this->secretTables)) {
      return(true);
    } else {
      return(false);
    }
  }

  // Get the list of tables
  function getTableNames() {
    $this->dataSource->selectDatabase();

    $tableList = array();
    if ($result = $this->dao->listTables()) {
      for ($i = 0; $i < $result->getRowCount(); $i++) {
        $row = $result->getRow($i);
        $tableName = $row[0];

        // Do not backup rubish data
        if (in_array($tableName, $this->rubishTables)) {
          continue;
        }

        array_push($tableList, $tableName);
      }
    }

    return($tableList);
  }

  // Export a table content
  function exportTable($filename, $tableName) {
    $this->dataSource->selectDatabase();

    // If the file aready exists, delete it before creating a new empty one
    if (file_exists($filename)) {
      unlink($filename);
    }

    // Open the file
    $fp = fopen($filename, "w");

    // Check that the file pointer is valid
    if (!is_resource($fp)) {
      return(false);
    }

    $resultData = $this->dao->selectAllFromTable($tableName);
    if ($resultData->getRowCount() > 0) {
      for ($k = 0; $k < $resultData->getRowCount(); $k++) {
        $rowData = $resultData->getRow($k, DB_RESULT_ARRAY_ASSOC);
        $strRow = "<quote>" . implode("<quote>,<quote>", $rowData) . "<quote>";
        $strRow = str_replace("<quote>", "'", addslashes($strRow));
        fwrite($fp, "$strRow\n");
      }
    }

    fclose($fp);

    return(true);
  }

}

?>
