<?php

class TemplatePropertyUtils extends TemplatePropertyDB {

  var $imagePath;
  var $imageUrl;
  var $imageSize;

  function TemplatePropertyUtils() {
    $this->TemplatePropertyDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imageSize = 200000;
    $this->imagePath = $gDataPath . 'template/model/html/image/';
    $this->imageUrl = $gDataUrl . '/template/model/html/image';
  }

  function createDirectories() {
    global $gTemplateDataPath;

    if (!is_dir($this->imagePath)) {
      mkdir($this->imagePath, 0755, true);
    }
    if (!is_dir($gTemplateDataPath . "export/image/")) {
      mkdir($gTemplateDataPath . "export/image/", 0755, true);
    }
  }

  // Remove the non referenced files from the directory
  function deleteUnusedImageFiles() {
    $handle = opendir($this->imagePath);
    while ($oneFile = readdir($handle)) {
      if ($oneFile != "." && $oneFile != ".." && !strstr($oneFile, '*')) {
        // Check if the image is not present in the database table
        if (!$this->imageIsUsed($oneFile)) {
          $oneFile = str_replace(" ", "\\ ", $oneFile);
          if (file_exists($this->imagePath . $oneFile)) {
            unlink($this->imagePath . $oneFile);
          }
        }
      }
    }
    closedir($handle);
  }

  // Check if an image is being used
  function imageIsUsed($image) {
    $isUsed = true;

    $this->dataSource->selectDatabase();

    if ($result = $this->dao->selectByValue($image)) {
      if ($result->getRowCount() < 1) {
        $isUsed = false;
      }
    }

    return($isUsed);
  }

  // Duplicate a property
  function duplicate($templatePropertyId, $templatePropertySetId) {
    if ($templateProperty = $this->selectById($templatePropertyId)) {
      $templateProperty->setTemplatePropertySetId($templatePropertySetId);
      // Copy the images if any
      $name = $templateProperty->getName();
      if ($name == 'BACKGROUND_IMAGE') {
        $value = $templateProperty->getValue();
        if (is_file($this->imagePath . $value)) {
          $prefix = LibFile::getFilePrefix($value);
          $suffix = LibFile::getFileSuffix($value);
          $randomNumber = LibUtils::generateUniqueId();
          $imageDuplicata = $prefix . '_' . $randomNumber . '.' . $suffix;
          copy($this->imagePath . $value, $this->imagePath . $imageDuplicata);
          $templateProperty->setValue($imageDuplicata);
        }
      }
      $this->insert($templateProperty);
    }
  }

  // Export a property
  function exportXML($xmlNode, $templatePropertyId) {
    global $gTemplateDataPath;

    if ($templateProperty = $this->selectById($templatePropertyId)) {
      $name = $templateProperty->getName();
      $value = $templateProperty->getValue();

      $xmlChildNode = $xmlNode->addChild(TEMPLATE_PROPERTY);
      $attributes = array("name" => $name, "value" => $value);
      if (is_array($attributes)) {
        foreach ($attributes as $aName => $aValue) {
          $xmlChildNode->addAttribute($aName, $aValue);
        }
      }

      // Copy the images if any
      if ($name == 'BACKGROUND_IMAGE') {
        if (is_file($this->imagePath . $value)) {
          copy($this->imagePath . $value, $gTemplateDataPath . "export/image/$value");
        }
      }
    }
  }

}

?>
