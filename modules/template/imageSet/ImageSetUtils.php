<?

class ImageSetUtils {

  var $computerImagePath;
  var $computerImageUrl;
  var $phoneImagePath;
  var $phoneImageUrl;

  var $computerCustomImagePath;
  var $computerCustomImageUrl;
  var $phoneCustomImagePath;
  var $phoneCustomImageUrl;

  var $imageSize;

  function ImageSetUtils() {
    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->computerImagePath = $gDataPath . 'imageSet/computer/';
    $this->computerImageUrl = $gDataUrl . '/imageSet/computer';
    $this->phoneImagePath = $gDataPath . 'imageSet/phone/';
    $this->phoneImageUrl = $gDataUrl . '/imageSet/phone';

    $this->computerCustomImagePath = $gDataPath . 'imageSet/computerCustom/';
    $this->computerCustomImageUrl = $gDataUrl . '/imageSet/computerCustom';
    $this->phoneCustomImagePath = $gDataPath . 'imageSet/phoneCustom/';
    $this->phoneCustomImageUrl = $gDataUrl . '/imageSet/phoneCustom';

    $this->imageSize = 100000;
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->computerImagePath)) {
      if (!is_dir($gDataPath . 'imageSet')) {
        mkdir($gDataPath . 'imageSet');
      }
      mkdir($this->computerImagePath);
      chmod($this->computerImagePath, 0755);
    }
    if (!is_dir($this->phoneImagePath)) {
      mkdir($this->phoneImagePath);
      chmod($this->phoneImagePath, 0755);
    }

    if (!is_dir($this->computerCustomImagePath)) {
      if (!is_dir($gDataPath . 'imageSet')) {
        mkdir($gDataPath . 'imageSet');
      }
      mkdir($this->computerCustomImagePath);
      chmod($this->computerCustomImagePath, 0755);
    }
    if (!is_dir($this->phoneCustomImagePath)) {
      mkdir($this->phoneCustomImagePath);
      chmod($this->phoneCustomImagePath, 0755);
    }
  }

  // Copy the standard computer images into the website images directory
  function copyComputerStandardImage($image) {
    global $gTemplateImagePath;

    $sourcePath = $gTemplateImagePath . "images/computer/";
    $destPath = $this->computerImagePath;
    if (is_file($sourcePath . $image)) {
      copy($sourcePath . $image, $destPath . $image);
    }
  }

  // Copy the standard phone images into the website images directory
  function copyPhoneStandardImage($image) {
    global $gTemplateImagePath;

    $sourcePath = $gTemplateImagePath . "images/phone/";
    $destPath = $this->phoneImagePath;
    if (is_file($sourcePath . $image)) {
      copy($sourcePath . $image, $destPath . $image);
    }
  }

  // Copy a custom computer image into the website images directory
  function copyComputerCustomImage($image) {
    global $gImagePath;

    $sourcePath = $this->computerCustomImagePath;
    $destPath = $this->computerImagePath;
    if (is_file($sourcePath . $image)) {
      copy($sourcePath . $image, $destPath . $image);
    }
  }

  // Copy a custom phone image into the website images directory
  function copyPhoneCustomImage($image) {
    global $gImagePath;

    $sourcePath = $this->phoneCustomImagePath;
    $destPath = $this->phoneImagePath;
    if (is_file($sourcePath . $image)) {
      copy($sourcePath . $image, $destPath . $image);
    }
  }

  // Copy the standard computer image from the image set to the website image directory
  function deleteComputerCustomImage($image) {
    global $gTemplateImagePath;

    $sourcePath = $gTemplateImagePath . "images/computer/";
    $destPath = $this->computerImagePath;
    if (is_file($sourcePath . $image)) {
      copy($sourcePath . $image, $destPath . $image);
    }
    if (is_file($this->computerCustomImagePath . $image)) {
      unlink($this->computerCustomImagePath . $image);
    }
  }

  // Copy the standard phone image from the image set to the website image directory
  function deletePhoneCustomImage($image) {
    global $gTemplateImagePath;

    $sourcePath = $gTemplateImagePath . "images/phone/";
    $destPath = $this->phoneImagePath;
    if (is_file($sourcePath . $image)) {
      copy($sourcePath . $image, $destPath . $image);
    }
    if (is_file($this->phoneCustomImagePath . $image)) {
      unlink($this->phoneCustomImagePath . $image);
    }
  }

  // Reset the images
  function resetImages() {
    global $gTemplateImagePath;

    $sourcePath = $gTemplateImagePath . "images/computer/";
    $images = LibDir::getFileNames($sourcePath);
    foreach ($images as $image) {
      if (is_file($sourcePath . $image)) {
        $this->copyComputerStandardImage($image);
      }
    }
    $sourcePath = $gTemplateImagePath . "images/phone/";
    $images = LibDir::getFileNames($sourcePath);
    foreach ($images as $image) {
      if (is_file($sourcePath . $image)) {
        $this->copyPhoneStandardImage($image);
      }
    }
  }

  // Delete the images
  function deleteImages() {
    $computerImagePath = $this->computerImagePath;
    $images = LibDir::getFileNames($computerImagePath);
    foreach ($images as $image) {
      if (is_file($computerImagePath . $image)) {
        unlink($computerImagePath . $image);
      }
    }
    $phoneImagePath = $this->phoneImagePath;
    $images = LibDir::getFileNames($phoneImagePath);
    foreach ($images as $image) {
      if (is_file($phoneImagePath . $image)) {
        unlink($phoneImagePath . $image);
      }
    }
  }

}

?>
