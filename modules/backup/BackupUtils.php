<?

class BackupUtils extends BackupDB {

  var $mlText;

  var $backupFilePath;
  var $backupFileUrl;

  var $previousBackupFilePath;

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

    $this->backupFilePath = $gDataPath . 'backup/file/';
    $this->backupFileUrl = $gDataUrl . '/backup/file';
    $this->previousBackupFilePath = $gAccountPath . 'db_backup/';
    $this->previousBackupFileUrl = $gAccountUrl . '/db_backup';
    $this->exportFilePath = $gDataPath . 'backup/export/';
    $this->exportFileUrl = $gDataUrl . '/backup/export';

    $this->rubishTables = array('statistics_visit', 'syslog');
    $this->secretTables = array('template_model', 'template_container', 'template_element', 'template_tag', 'template_property_set', 'template_property');

    $this->backupFilePrefix = 'backup_db_';

    $this->backupDataFormats = array("INSERT", "CSV");

    $this-> createDirectories();
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;
    global $gAccountPath;

    if (!is_dir($this->backupFilePath)) {
      if (!is_dir($gDataPath . 'backup')) {
        mkdir($gDataPath . 'backup');
      }
      mkdir($this->backupFilePath);
      chmod($this->backupFilePath, 0755);
    }

    if (!is_dir($this->previousBackupFilePath)) {
      mkdir($this->previousBackupFilePath);
      chmod($this->previousBackupFilePath, 0755);
    }

    if (!is_dir($this->exportFilePath)) {
      if (!is_dir($gDataPath . 'backup')) {
        mkdir($gDataPath . 'backup');
      }
      mkdir($this->exportFilePath);
      chmod($this->exportFilePath, 0755);
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
        // Do not backup the current and parent directories
        if ($dir != "." && $dir != "..") {
          $dirList[$i]= $gDataPath . $dir;
          $i++;
        }
      }
    }
    sort($dirList);

    $tarArchive = new Archive_Tar($filename);

    $success = $tarArchive->create($dirList);

    return($success);
  }

  // Backup the language files
  function backupLanguageFiles($filename, $languageCode) {
    global $gDataPath;

    $languageFiles = $this->languageUtils->getLanguageFilenames($languageCode);

    $tarArchive = new Archive_Tar($filename);

    $success = $tarArchive->create($languageFiles);

    return($success);
  }

  // Delete the backup files of the account
  function deleteBackup() {
    $accountName = $this->websiteUtils->getSetupWebsiteName();

    $this->deleteAccountBackup($accountName);
  }

  // Render sql file url
  function renderSqlFileUrl($filename) {
    $url = $this->previousBackupFileUrl . '/' . $filename;

    return($url);
  }

  // Render data file url
  function renderBackupFileUrl() {
    global $gAccountUrl;

    $url = $gAccountUrl . '/' . $this->websiteUtils->getSetupWebsiteName() . ".tar";

    return($url);
  }

  // Render data file path
  function renderBackupFilePath() {
    global $gAccountPath;

    $backupFilePath = $gAccountPath . $this->websiteUtils->getSetupWebsiteName() . ".tar";

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
  function deleteDBBackupFiles($min = 7) {
    if (($backupDir = opendir($this->backupFilePath)) == false) {
      return(false);
    }

    if (($previousBackupDir = opendir($this->previousBackupFilePath)) == false) {
      return(false);
    }

    // Move the previous backup db file in a parent directory
    while (($fileName = readdir($backupDir)) !== false) {
      if (!is_dir($fileName)) {
        rename($this->backupFilePath . $fileName, $this->previousBackupFilePath . $fileName);
      }
    }

    closedir($backupDir);

    $fileTimestamps = Array();
    $fileNames = Array();
    while (($fileName = readdir($previousBackupDir)) !== false) {
      if (!is_dir($fileName)) {
        $fileTimestamp = filemtime($this->previousBackupFilePath . $fileName);

        $fileTimestamps[$fileName] = $fileTimestamp;
      }
    }

    // Sort on the timestamps in reverse order
    arsort($fileTimestamps);

    $i = 1;
    foreach ($fileTimestamps as $fileName => $fileTimestamp) {
      if ($i > $min) {
        if (file_exists($this->previousBackupFilePath . $fileName)) {
          unlink($this->previousBackupFilePath . $fileName);
        }
      }
      $i++;
    }

    closedir($previousBackupDir);
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
