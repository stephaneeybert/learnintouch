<?php

class LibImage {

  // Get the image width
  static function getWidth($image) {
    $width = '';

    if (is_file($image)) {
      $info = getimagesize($image);
      $width = $info[0];
    }

    return($width);
  }

  // Get the image height
  static function getHeight($image) {
    $height = '';

    if (is_file($image)) {
      $info = getimagesize($image);
      $height = $info[1];
    }

    return($height);
  }

  // Get the image height from a new width
  static function getHeightFromWidth($image, $newWidth) {
    $height = '';

    if (is_file($image)) {
      $info = getimagesize($image);
      $width = $info[0];
      $height = $info[1];

      // Get the image ratio
      if ($width < 2 || $newWidth < 2) {
        $ratio = 1;
      } else {
        $ratio = ($newWidth / $width);
      }

      // Get the new height
      $height = round($height * $ratio);
    }

    return($height);
  }

  // Get the image width from a new height
  static function getWidthFromHeight($image, $newHeight) {
    $width = '';

    if (is_file($image)) {
      $info = getimagesize($image);
      $width = $info[0];
      $height = $info[1];

      // Get the image ratio
      if ($height == 0 || $newHeight <= 0) {
        $ratio = 1;
      } else {
        $ratio = ($newHeight / $height);
      }

      // Get the new width
      $width = round($width * $ratio);
    }

    return($width);
  }

  // Resize en image
  static function resizeImageToWidth($sourceImage, $destWidth) {
    if (!$destWidth) {
      return;
    }

    $destHeight = LibImage::getHeightFromWidth($sourceImage, $destWidth);

    LibImage::resizeImageToWidthAndHeight($sourceImage, $destWidth, $destHeight);
  }

  // Resize en image
  static function resizeImageToWidthAndHeight($sourceImage, $destWidth, $destHeight) {
    $destImage = LibImage::buildImageTempName($sourceImage);

    LibImage::outputImage($sourceImage, $destImage, $destWidth, $destHeight, '', '');

    if (LibImage::isImage($destImage)) {
      LibFile::move($destImage, $sourceImage);
    }
  }

  // Duplicate an image to a jpg image if not already a jpg image
  static function getJpgImage($imagePath, $image) {
    $imageType = LibImage::getImageType($imagePath . $image);
    if (LibImage::isImage($image)) {
      if ($imageType != "jpeg" && $imageType != "jpg") {
        $jpgImage = LibImage::renameToJpg($image);
        LibImage::copyImage($imagePath . $image, $imagePath . $jpgImage);
        $filename = $imagePath . $jpgImage;
      } else {
        $filename = $imagePath . $image;
      }
    } else {
      $filename = '';
    }
    return($filename);
  }

  // Copy an image
  static function copyImage($sourceFilename, $destFilename, $destWidth = '', $destHeight = '', $transparent = false) {

    if (!$sourceFilename || !$destFilename || $sourceFilename == $destFilename) {
      return(false);
    }

    // Get the image type
    $sourceType = LibImage::getImageType($sourceFilename);

    // Create the image
    if ($sourceType == "jpeg" || $sourceType == "jpg") {
      $sourceImage = LibImage::createImageFromJpg($sourceFilename);
    } elseif ($sourceType == "gif") {
      $sourceImage = imagecreatefromgif($sourceFilename);
    } else if ($sourceType == "png") {
      $sourceImage = imagecreatefrompng($sourceFilename);
    } elseif ($sourceType == "wbmp") {
      $sourceImage = imagecreatefromwbmp($sourceFilename);
    } else {
      return(false);
    }

    // Check for the creation success
    if (!$sourceImage) {
      return(false);
    }

    // Get the image width and height
    $width = imagesx($sourceImage);
    $height = imagesy($sourceImage);
    if (!$destWidth) {
      $destWidth = $width;
    }
    if (!$destHeight) {
      $destHeight = $height;
    }

    // Create the destination image object
    $destImage = imagecreatetruecolor($destWidth, $destHeight);
    imagecopyresampled($destImage, $sourceImage, 0, 0, 0, 0, $width, $height, $destWidth, $destHeight);

    // Destroy the source image object
    imagedestroy($sourceImage);

    // Set the black color to fully transparent
    // Note that the color cannot be used any longer to draw in the image
    if ($transparent) {
      $blackcolor = imagecolorallocatealpha($destImage, 0, 0, 0, 127);
      imagecolortransparent($destImage, $blackcolor);
    }

    // Get the destination image type
    $destinationType = LibImage::getImageType($destFilename);

    // Create the destination image file
    if ($destinationType == "jpeg" || $destinationType == "jpg") {
      $copy = imagejpeg($destImage, $destFilename);
    } else if ($destinationType == "gif") {
      $copy = imagegif($destImage, $destFilename);
    } else if ($destinationType == "png") {
      $copy = imagepng($destImage, $destFilename);
    } elseif ($destinationType == "wbmp") {
      $copy = imagewbmp($destImage, $destFilename);
    } else {
      return;
    }

    // Get the actual image width and height
    $actualWidth = imagesx($copy);
    $actualHeight = imagesy($copy);

    $output = imagecreatetruecolor($width, $height);
    imagecopyresampled($output, $copy, 0, 0, 0, 0, $width, $height, $actualWidth, $actualHeight);
    imagedestroy($copy);
    // Destroy the destination image object
    imagedestroy($destImage);

    // Header indicating the image type
    header("Content-type:image/$type");

    // Create the image
    if ($type == "jpeg" && (imagetypes() & IMG_JPEG)) {
      imagejpeg($output);
    } elseif ($type == "gif" && (imagetypes() & IMG_GIF)) {
      imagegif($output);
    } elseif ($type == "png" && (imagetypes() & IMG_PNG)) {
      imagepng($output);
    } elseif ($type == "wbmp" && (imagetypes() & IMG_WBMP)) {
      image2wbmp($output);
    }
  }

  // Print an image
  static function printImage($filename, $width = '', $height = '', $watermark = '', $bottomWatermark = '') {
    LibImage::outputImage($filename, '', $width, $height, $watermark, $bottomWatermark);
  }

  static function outputImage($filename, $destFilename, $width, $height, $watermark, $bottomWatermark) {
    global $gApiPath;

    if (!$filename) {
      return;
    }

    // Check for the display width and height
    // Note that trying to create an image with a width or a height of 2
    // creates an image that contains errors
    if ($width < 2 || $height < 2) {
      return;
    }

    // Get the image type
    $type = LibImage::getImageType($filename);

    // Create the image
    if (($type == "jpeg" || $type == "jpg") && (imagetypes() & IMG_JPEG)) {
      $copy = LibImage::createImageFromJpg($filename);
    } else if ($type == "gif" && (imagetypes() & IMG_GIF)) {
      $copy = imagecreatefromgif($filename);
    } else if ($type == "png" && (imagetypes() & IMG_PNG)) {
      $copy = imagecreatefrompng($filename);
    } else if ($type == "wbmp" && (imagetypes() & IMG_WBMP)) {
      $copy = imagecreatefromwbmp($filename);
    } else {
      return;
    }

    // If the copy failed
    if (!$copy) {
      // Then create a blank image
      $output = imagecreate($width, $height);
      $backgroundColor = imagecolorallocate($output, 255, 255, 255);
      imagefilledrectangle($output, 0, 0, $width, $height, $backgroundColor);
      $type = "png";
    } else {
      // Get the actual image width and height
      $actualWidth = imagesx($copy);
      $actualHeight = imagesy($copy);

      $output = imagecreatetruecolor($width, $height);
      imagecopyresampled($output, $copy, 0, 0, 0, 0, $width, $height, $actualWidth, $actualHeight);
      imagedestroy($copy);

      // Display a watermark if any
      if ($watermark) {
        $fontSize = 20;
        $fontAngle = 45;
        $fontColor = imagecolorallocate($output, 219, 219, 219);
        $fontType = $gApiPath . 'font/LucidaTypewriterRegular.ttf';
        $textSize = imagettfbbox($fontSize, $fontAngle, $fontType, $watermark);
        $textWidth = abs($textSize[2] - $textSize[0]);
        $textHeight = abs($textSize[5] - $textSize[3]);
        $watermarkImage = imagecreatetruecolor($textWidth, $textHeight);
        $x = imagesx($output) / 2 - $textWidth / 2;
        $y = imagesy($output) / 2 + $textWidth / 2;
        imagettftext($output, $fontSize, $fontAngle, $x, $y, $fontColor, $fontType, $watermark);
        // The above True Type font is better
        //        imagestring($output, 5, $x, $y, $watermark, $fontColor);
      }

      // Display a bottom watermark if any
      if ($bottomWatermark) {
        $fontSize = 12;
        $fontAngle = 0;
        $fontColor = imagecolorallocate($output, 219, 219, 219);
        $fontType = $gApiPath . 'font/LucidaTypewriterRegular.ttf';
        $textSize = imagettfbbox($fontSize, $fontAngle, $fontType, $bottomWatermark);
        $textWidth = abs($textSize[2] - $textSize[0]);
        $textHeight = abs($textSize[5] - $textSize[3]);
        $watermarkImage = imagecreatetruecolor($textWidth, $textHeight);
        $x = imagesx($output) - $textWidth - 10;
        $y = imagesy($output) - 10;
        imagettftext($output, $fontSize, $fontAngle, $x, $y, $fontColor, $fontType, $bottomWatermark);
      }
    }

    // Create the image
    if ($type == "jpeg" && (imagetypes() & IMG_JPEG)) {
      if ($destFilename) {
        imagejpeg($output, $destFilename);
      } else {
        // Header indicating the image type
        header("Content-type:image/$type");

        imagejpeg($output);
      }
    } elseif ($type == "gif" && (imagetypes() & IMG_GIF)) {
      if ($destFilename) {
        imagegif($output, $destFilename);
      } else {
        // Header indicating the image type
        header("Content-type:image/$type");

        imagegif($output);
      }
    } elseif ($type == "png" && (imagetypes() & IMG_PNG)) {
      if ($destFilename) {
        imagepng($output, $destFilename);
      } else {
        // Header indicating the image type
        header("Content-type:image/$type");

        imagepng($output);
      }
    } elseif ($type == "wbmp" && (imagetypes() & IMG_WBMP)) {
      if ($destFilename) {
        image2wbmp($output, $destFilename);
      } else {
        // Header indicating the image type
        header("Content-type:image/$type");

        image2wbmp($output);
      }
    }

    imagedestroy($output);
  }

  // Print a number image
  static function printNumberImage($number, $securityCodeFontSize) {
    if (!$securityCodeFontSize) {
      $securityCodeFontSize = 15;
    }

    // Set the width and height
    $width = ($securityCodeFontSize * strlen(strval($number))) + 2;
    $height = $securityCodeFontSize * 2;

    // Create a blank image
    $image = imagecreatetruecolor($width, $height);

    $blue = imagecolorallocate($image, 0, 0, 127);
    $white = imagecolorallocate($image, 255, 255, 255);

    // Fill up the image background
    imagefilledrectangle($image, 0, 0, $width, $height, $white);

    // Draw each number digit
    for ($counter = 0; $counter < strlen(strval($number)); $counter++) {
      $digit = substr(strval($number), $counter, 1);
      imagestring($image, 5, 2 + $counter * 10, 2, $digit, $blue);
    }

    // Header indicating the image type
    header("Content-type:image/jpeg");

    // Create the image in the best jpeg quality
    imagejpeg($image, NULL, 100);

    // Destroy the image
    imagedestroy($image);
  }

  // Print a number image
  static function printTTFNumberImage($number, $securityCodeFontSize) {
    global $gApiPath;

    if (!$securityCodeFontSize) {
      $securityCodeFontSize = 15;
    }

    // Set the width and height
    $width = ($securityCodeFontSize * strlen(strval($number))) + 10;
    $height = $securityCodeFontSize * 1.5;

    // Get a random font size
    $fontSize = $securityCodeFontSize;

    // Get a random angle
    $fontAngle = rand(0, 5);

    // Create a blank image
    $image = imagecreatetruecolor($width, $height);

    // Get a random font color
    $fontColor = ImageColorAllocate($image, rand(0, 100), rand(0, 100), rand(0, 100));

    // Determine text size, and use dimensions to generate x & y coordinates
    $fontType = $gApiPath . 'font/LucidaTypewriterRegular.ttf';
    $textSize = imagettfbbox($fontSize, $fontAngle, $fontType, $number);
    $textWidth = abs($textSize[2] - $textSize[0]);
    $textHeight = abs($textSize[5] - $textSize[3]);
    $x = 10;
    $y = $textHeight + ($securityCodeFontSize / 4);

    // Fill up the image background
    $white = imagecolorallocate($image, 255, 255, 255);
    $black = imagecolorallocate($image, 0, 0, 0);
    imagefilledrectangle($image, 0, 0, $width, $height, $white);
    imagerectangle($image, 0, 0, $width - 1, $height - 1, $black);

    // Add text to image
    imagettftext($image, $fontSize, $fontAngle, $x, $y, $fontColor, $fontType, $number);

    // Header indicating the image type
    header("Content-type:image/jpeg");

    // Create the image in the best jpeg quality
    imagejpeg($image, NULL, 100);

    // Destroy the image
    imagedestroy($image);
  }

  // Print a bar image
  static function printBarImage($color, $width, $height) {
    // Create a blank image
    $image = imagecreatetruecolor($width, $height);

    if (strlen($color) == 6) {
      $color = "#" . $color;
    }

    $r = intval(substr($color, 1, 2), 16);
    $g = intval(substr($color, 3, 2), 16);
    $b = intval(substr($color, 5, 2), 16);
    $color = imagecolorallocate($image, $r, $g, $b);

    // Fill up the image background
    imagefilledrectangle($image, 0, 0, $width, $height, $color);

    // Header indicating the image type
    header("Content-type:image/jpeg");

    // Create the image in the best jpeg quality
    imagejpeg($image, NULL, 100);

    // Destroy the image
    imagedestroy($image);
  }

  // Check if the file is an image
  static function isImage($filename) {
    $imageTypes = array('jpg', 'jpeg', 'png', 'gif');

    if (in_array(LibImage::getImageType($filename), $imageTypes)) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if the image is a gif
  static function isGif($filename) {
    if (LibImage::getImageType($filename) == 'gif') {
      return(true);
    } else {
      return(false);
    }
  }

  // Rename to jpg
  static function renameToJpg($filename) {
    $name = LibImage::renameWithSuffix($filename, "jpg");

    return($name);
  }

  // Rename to png
  static function renameToPng($filename) {
    $name = LibImage::renameWithSuffix($filename, "png");

    return($name);
  }

  // Rename with a file suffix
  static function renameWithSuffix($filename, $suffix) {

    // Get the image suffix
    $pieces = explode(".", basename($filename));
    if (count($pieces) > 1) {
      $type = strtolower($pieces[count($pieces) - 1]);
    }

    // Determine the type
    if (strtolower($type) != $suffix) {
      $renamed = $pieces[0] . "." . $suffix;
    } else {
      $renamed = $filename;
    }

    return($renamed);
  }

  // Get the image basename
  static function buildImageTempName($filename) {
    $imageType = LibImage::getImageType($filename);
    $suffixLess = substr($filename, 0, strlen($filename) - (strlen($imageType)));
    $name = $suffixLess . "-tmp" . "." . $imageType;
    return($name);
  }

  // Get the image type
  static function getImageType($filename) {
    $type = '';

    $pieces = explode(".", basename($filename));
    if (count($pieces) > 1) {
      $type = strtolower($pieces[count($pieces) - 1]);
    }

    if ($type == "jpg" || $type == "jpe") {
      $type = "jpeg";
    }

    return($type);
  }

  // Check and try to fix an invalid jpeg image
  // Some jpeg images have an invalid format and are encoded not as they should
  // A JPEG image should start with 0xFFD8 and end with 0xFFD9
  static function checkAndFixJpg($filename, $fix = false) {
    if (false !== ($fd = fopen($filename, 'r+b'))) {
      if (fread($fd,2) == chr(255).chr(216)) {
        fseek ($fd, -2, SEEK_END);
        if (fread($fd,2) == chr(255).chr(217)) {
          fclose($fd);
          return(true);
        } else {
          if ($fix && fwrite($fd, chr(255).chr(217))) {
            fclose($fd);
            return(true);
          }
          fclose($fd);
          return(false);
        }
      } else {
        fclose($fd);
        return(false);
      }
    } else {
      return(false);
    }
  }

  // Watermark an image
  static function NOT_USED_watermark($file, $img_height, $waterMark) {

    $img_temp = LibImage::createImageFromJpg($file);

    $black = imagecolorallocate($img_temp, 0, 0, 0);
    $white = imagecolorallocate($img_temp, 255, 255, 255);

    $font = 2;

    $img_width = imagesx($img_temp) / imagesy($img_temp) * $img_height;
    $img_thumb = imagecreatetruecolor($img_width, $img_height);

    imagecopyresampled($img_thumb, $img_temp, 0, 0, 0, 0, $img_width, $img_height, imagesx($img_temp), imagesy($img_temp));

    $originx = imagesx($img_thumb) - 100;
    $originy = imagesy($img_thumb) - 15;

    imagestring($img_thumb, $font, $originx + 10, $originy, $waterMark, $black);
    imagestring($img_thumb, $font, $originx + 11, $originy - 1, $waterMark, $white);

    header("Content-type: image/jpeg");
    imagejpeg($img_thumb, NULL, 60);

    imagedestroy($img_thumb);
  }

  // Create an image from a jpg one
  function createImageFromJpg($file) {
    LibImage::checkAndFixJpg($file, true);

    $image = imagecreatefromjpeg($file);

    if (!$image) {
      $image  = imagecreatetruecolor(150, 30);
      $bgc = imagecolorallocate($im, 255, 255, 255);
      $tc  = imagecolorallocate($im, 0, 0, 0);
      imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
      imagestring($image, 1, 5, 5, "The jpeg image $file has an invalid format and could not be uploaded.", $tc);
    }

    return($image);
  }

}

?>
