<?php

class FileUploadUtils {

  var $mlText;

  var $maximumFileSize;

  var $allowedImageTypes;
  var $gifImageType;
  var $allowedCSVTypes;
  var $allowedArchiveTypes;
  var $allowedFaviconTypes;
  var $allowedFlashTypes;
  var $allowedVideoTypes;

  var $languageUtils;

  function FileUploadUtils() {
    $this->init();
  }

  function init() {
    $this->maximumFileSize = 8192000;
    $this->allowedImageTypes = array('jpg', 'jpeg', 'jpe', 'png', 'gif');
    $this->gifImageType = array('gif');
    $this->allowedArchiveTypes = array('zip');
    $this->allowedCSVTypes = array('csv', 'txt', 'xls');
    $this->allowedFaviconTypes = array('ico');
    $this->allowedFlashTypes = array('swf', 'flv');
    $this->allowedVideoTypes = array('avi', 'ogg', '264', 'mkv', 'mp4', 'm4v', 'ogv');
    $this->allowedAudioTypes = array('mp3', 'ogg', 'm4a');
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  // Check if the media file has an authorised type
  function isMediaType($filename) {
    $allowedMediaTypes = array_merge($this->allowedImageTypes, $this->allowedFlashTypes, $this->allowedVideoTypes);

    return($this->isFileType($filename, $allowedMediaTypes));
  }

  // Check if the image is of the gif type
  function isGifImage($filename) {
    return($this->isFileType($filename, $this->gifImageType));
  }

  // Check if the image has an authorised type
  function isImageType($filename) {
    return($this->isFileType($filename, $this->allowedImageTypes));
  }

  // Check if the favicon has an authorised type
  function isFaviconType($filename) {
    return($this->isFileType($filename, $this->allowedFaviconTypes));
  }

  // Check if the csv file has an authorised type
  function isCSVType($filename) {
    return($this->isFileType($filename, $this->allowedCSVTypes));
  }

  // Check if the archive file has an authorised type
  function isArchiveType($filename) {
    return($this->isFileType($filename, $this->allowedArchiveTypes));
  }

  // Check if the flash file has an authorised type
  function isFlashType($filename) {
    return($this->isFileType($filename, $this->allowedFlashTypes));
  }

  // Check if the audio file has an authorised type
  function isAudioType($filename) {
    return($this->isFileType($filename, $this->allowedAudioTypes));
  }

  // Check if the file is an mp3 type
  function isMP3Type($filename) {
    return($this->isFileType($filename, 'mp3'));
  }

  // Check if the file has an authorised type
  function isFileType($filename, $allowedFileTypes) {
    $allowed = false;
    $bits = explode(".", basename($filename));
    if (count($bits) > 1) {
      $file_type = $bits[count($bits)-1];
      if ($file_type) {
        if (is_array($allowedFileTypes)) {
          for ($i = 0; $i < count($allowedFileTypes); $i++ ) {
            if (strstr(strtolower($allowedFileTypes[$i]), strtolower($file_type))) {
              $allowed = true;
            }
          }
        } else {
          if (strstr(strtolower($allowedFileTypes), strtolower($file_type))) {
            $allowed = true;
          }
        }
      }
    }
    return($allowed);
  }

  // Open the file rights to allow erasing and rewriting
  function openFileRights($filename) {
    chmod($filename, 0766);
  }

  // Check if the file type is allowed
  function checkMediaFileType($filename) {
    $this->loadLanguageTexts();

    if (!$this->isMediaType($filename)) {
      $allowedMediaTypes = array_merge($this->allowedImageTypes, $this->allowedFlashTypes, $this->allowedVideoTypes);

      $str = $this->mlText[22];
      $str = $this->mlText[3] . " " . join($allowedMediaTypes, ", ");

      return($str);
    }

    return(0);
  }

  // Check if the file type is allowed
  function checkImageFileType($filename) {
    $this->loadLanguageTexts();

    if (!$this->isImageType($filename)) {
      $str = $this->mlText[22];
      $str = $this->mlText[3] . " " . join($this->allowedImageTypes, ", ");

      return($str);
    }

    return(0);
  }

  // Check if the file type is allowed
  function checkCSVFileType($filename) {
    $this->loadLanguageTexts();

    if (!$this->isCSVType($filename)) {
      $str = $this->mlText[22];
      $str = $this->mlText[3] . " " . join($this->allowedCSVTypes, ", ");

      return($str);
    }

    return(0);
  }

  // Check if the file type is allowed
  function checkArchiveFileType($filename) {
    $this->loadLanguageTexts();

    if (!$this->isArchiveType($filename)) {
      $str = $this->mlText[22];
      $str = $this->mlText[3] . " " . join($this->allowedArchiveTypes, ", ");

      return($str);
    }

    return(0);
  }

  // Check if the file type is allowed
  function checkFlashFileType($filename) {
    $this->loadLanguageTexts();

    if (!$this->isFlashType($filename)) {
      $str = $this->mlText[22];
      $str = $this->mlText[3] . " " . join($this->allowedFlashTypes, ", ");

      return($str);
    }

    return(0);
  }

  // Check if the file type is allowed
  function checkFaviconFileType($filename) {
    $this->loadLanguageTexts();

    if (!$this->isFaviconType($filename)) {
      $str = $this->mlText[22];
      $str = $this->mlText[3] . " " . join($this->allowedFaviconTypes, ", ");

      return($str);
    }

    return(0);
  }

  // Check if the file type is allowed
  function checkAudioFileType($filename) {
    $this->loadLanguageTexts();

    if (!$this->isAudioType($filename)) {
      $str = $this->mlText[22];
      $str = $this->mlText[3] . " " . join($this->allowedAudioTypes, ", ");

      return($str);
    }

    return(0);
  }

  // Check if the file type is allowed
  function checkMP3FileType($filename) {
    $this->loadLanguageTexts();

    if (!$this->isMP3Type($filename)) {
      $str = $this->mlText[22];
      $str = $this->mlText[3] . " mp3";

      return($str);
    }

    return(0);
  }

  // Check if the file type is allowed
  function checkFileType($filename, $allowedTypes) {
    $this->loadLanguageTexts();

    if (!$this->isFileType($filename, $allowedTypes)) {
      $str = $this->mlText[22];

      return($str);
    }

    return(0);
  }

  function getFileSizeMessage($size) {
    $this->loadLanguageTexts();

    $str = $this->mlText[0] . " " . round($size / 1000) . " " . $this->mlText[1];

    return($str);
  }

  // Check if the file is no bigger than the allowed file size
  function checkFileSize($size, $sizeLimit) {
    $this->loadLanguageTexts();

    if ($size == 0) {
      $str = $this->mlText[2];

      return($str);
    }

    // The size limit is the maximum of the system one and the module one
    $sizeLimit = max($sizeLimit, $this->maximumFileSize);

    if ($size > $sizeLimit) {
      $str = $this->mlText[23];
      $str .= "<br><br>" . $this->mlText[0] . " " . round($sizeLimit / 1000) . " " . $this->mlText[1];

      return($str);
    }

    return(0);
  }

  // Check if the file has been correctly specified
  function checkFileName($filename) {
    $this->loadLanguageTexts();

    if (!$filename) {
      $str = $this->mlText[27];

      return($str);
    }

    if (LibFile::getFileSuffix($filename) == 'php') {
      $str = $this->mlText[22];

      return($str);
    }

    return(0);
  }

  // Upload the file and copy it into a directory
  function uploadFile($file, $filename, $filepath) {
    $this->loadLanguageTexts();

    // Strip special characters from the file name
    $filename = LibString::stripNonFilenameChar($filename);

    // Add a trailing slash to the current directory if needed
    $filepath = LibString::addTraillingSlash($filepath);

    // Copy the file to a directory
    if (!is_uploaded_file($file) || !move_uploaded_file($file, $filepath . $filename)) {
      $str = $this->mlText[4];

      return($str);
    }

    // Open the rights
    $this->openFileRights($filepath . $filename);

    return(0);
  }

}

?>
