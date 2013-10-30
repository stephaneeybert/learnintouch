<?php

class LibDir {

  // Get all the files with a given suffix from a directory tree
  static function getDirFileNames($dir, $suffix = '') {
    $list = array();
    foreach(array_diff(scandir($dir), array('.', '..')) as $file) {
      if (ereg($suffix . '$', $file)) {
      }
      if (is_file($dir . '/' . $file) && (($suffix) ? ereg($suffix . '$', $file) : 1)) {
        $list[] = $dir . '/' . $file;
      } else if (is_dir($dir . '/' . $file)) {
        $list = array_merge($list, LibDir::getDirFileNames($dir . '/' . $file, $suffix));
      }
    }
    return $list;
  } 

  static function getDirNames($dirPath) {
    $filenames = Array();

    if (($dir = opendir($dirPath))) {
      while (($filename = readdir($dir)) !== false) {
        // Add a trailing slash if none
        if (substr($dirPath, -1) != "/") {
          $dirPath .= "/";
        }
        if (is_dir("$dirPath$filename") || $filename == "." || $filename == "..") {
          $filenames[count($filenames)] = $filename;
        }
      }
    }

    closedir($dir);

    return($filenames);
  }

  static function getFileNames($dirPath) {
    if (!$dirPath || !is_dir($dirPath)) {
      return(false);
    }

    $filenames = Array();

    if (($dir = opendir($dirPath))) {
      while (($filename = readdir($dir)) !== false) {
        // Do not list the directories
        if (!is_dir("$dirPath$filename") && $filename != "." && $filename != "..") {
          array_push($filenames, $filename);
        }
      }
    }

    closedir($dir);

    return($filenames);
  }

  // Delete a directory and its content
  static function deleteDirectory($dir) {
    if (is_dir($dir)) {
      $dh = opendir($dir);
      while (($file = readdir($dh)) !== false) {
        if ($file != "." and $file != "..") {
          $dir = LibString::addTraillingSlash($dir);
          LibDir::deleteDirectory($dir.$file);
        }
      }
      rmdir($dir);
      closedir($dh);
    } else if (is_file($dir)) {
      unlink($dir);
    }

    return(true);
  }

}

?>
