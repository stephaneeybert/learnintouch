<?php

class LibFile {

  static function curlGetFileContent($url, $destFile = '') {
    $curlFile = curl_init();
    curl_setopt($curlFile, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curlFile, CURLOPT_URL, $url);
    $content = curl_exec($curlFile);
    curl_close($curlFile);

    if ($content) {
      if ($destFile) {
        $fr = fopen($destFile, 'w');
        fwrite($fr, $content);
        fclose($fr);
      }
      return($content);
    } else {
      return(false);
    }
  }

  // The currently installed libcurl 7.15 does not yet
  // support the method curl_setopt_array( $ch, $options );
  static function NOT_USED_curlGetFileContent($url, $destFile = '') {
    $content = '';

    $header = LibFile::curlGetUrlHeader($url);

    if ($header['errno'] != 0) {
      // Error: bad url, timeout, redirect loop...
      $content = $header['errmsg'];
    } else if ($header['http_code'] != 200) {
      // Error: no page, no permissions, no service...
      $content = $header['errmsg'];
    } else {
      $content = $header['content'];

      if ($destFile) {
        $fr = fopen($destFile, 'w');
        fwrite($fr, $content);
        fclose($fr);
      }
    }

    return($content);
  }

  // Get a web file (HTML, XHTML, XML, image, etc.) from a URL
  // Return an array containing the HTTP server response header fields and content
  static function curlGetUrlHeader($url) {
    $options = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "",       // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        );

    $ch      = curl_init( $url );
    curl_setopt_array( $ch, $options );
    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch );

    $header['errno']   = $err;
    $header['errmsg']  = $errmsg;
    $header['content'] = $content;

    return $header;
  }

  static function Du($dir) {
    $du = popen("/usr/bin/du -sm $dir", "r");
    $size = fgets($du, 256);
    pclose($du);
    $size = explode(" ", $size);
    $size = explode("/", $size[0]);
    return($size[0]);
  }

  // Get the size of the files of a directory
  static function getDirectoryFilesSize($dir) {
    $handle = opendir($dir);

    $size = 0;

    while ($file = readdir($handle)) {
      if ($file != '..' && $file != '.' && !is_dir($dir.'/'.$file)) {
        $size += filesize($dir.'/'.$file);
      } else if (is_dir($dir.'/'.$file) && $file != '..' && $file != '.') {
        $size += LibFile::getDirectoryFilesSize($dir.'/'.$file);
      }
    }

    return($size);
  }

  // Get the size of a directory in mega bytes
  static function getDirectorySize($dir) {
    if (is_dir($dir)) {
      $space = LibFile::getDirectoryFilesSize($dir);

      // Have the size in mega bytes
      $space = ceil($space / (1024 * 1024));
    } else {
      $space = 0;
    }

    return($space);
  }

  // Get the size of a directory
  static function getDirectorySize2($dirName) {
    $size = 0;
    $dirName = LibString::stripTraillingSlash($dirName);
    $dh = opendir($dirName);
    while (($file = readdir($dh)) !== false) {
      if ($file != "." and $file != "..") {
        $path = $dirName."/".$file;

        if (is_dir($path)) {
          $size += LibFile::getDirectorySize($path);
        } elseif (is_file($path)) {
          $size += filesize($path);
        }

      }
    }

    closedir($dh);

    // Have the size in mega bytes
    $mbSize = ceil($size / (1024 * 1024));

    return($mbSize);
  }

  // Write a string into a file
  static function writeString($filename, $str) {
    $fr = fopen($filename, 'w');
    fwrite($fr, $str);
    fclose($fr);

    return(true);
  }

  // Write an array into a file
  static function writeArray($filename, $fileArray) {
    if (count($fileArray) > 0) {
      $str = join('', $fileArray);
      $fr = fopen($filename, 'w');
      fwrite($fr, $str);
      fclose($fr);
    }

    return(true);
  }

  // Get a file content into an array of lines
  static function readIntoLines($fileName) {
    $lines = array();

    if (is_file($fileName)) {
      $lines = file($fileName);
    }

    return($lines);
  }

  // Get a file content into a string
  static function readIntoString($fileName) {
    $str = '';

    if (is_file($fileName)) {
      $fileArray = file($fileName);
      if (count($fileArray) > 0) {
        $str = join('', $fileArray);
      }
    }

    return($str);
  }

  // Get a file suffix
  // The suffix is the string after the last dot .
  static function getFileSuffix($filename) {
    $suffix = '';
    $bits = explode(".", basename($filename));
    if (count($bits) > 1) {
      $suffix = $bits[count($bits) - 1];
    }

    return($suffix);
  }

  // Get a file prefix
  // The prefix is the string before the last dot ., including other dots if any
  static function getFilePrefix($filename) {
    $prefix = '';
    $bits = explode(".", basename($filename));
    if (count($bits) > 1) {
      for ($i = 0; $i < count($bits) - 1; $i++) {
        $prefix .= '.' . $bits[$i];
      }
    }
    $prefix = substr($prefix, 1, strlen($prefix) - 1);

    return($prefix);
  }

  // Remove a file from the directory
  static function deleteFile($filename) {
    if (is_file($filename) && is_writable($filename)) {
      // Remove the file
      unlink($filename);
    }
  }

  // Move a file
  static function move($source, $dest) {
    return(rename($source, $dest));
  }

  static function downloadFile($filename) {
    // Get the name of the file
    $baseFilename = basename($filename);

    // Output the headers
    header("Cache-control: private");
    header("Content-transfer-encoding: binary");
    header("Content-Type: application/zip");
    header("Content-Disposition: attachment; filename=\"$baseFilename\"");

    // Output the file
    if (file_exists($filename)) {
      readfile($filename);
    }

    exit;
  }

}

?>
