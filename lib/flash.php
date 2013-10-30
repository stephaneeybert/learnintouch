<?php

// This is the player for the Flash files

class LibFlash {

  var $file;
  var $width;
  var $height;
  var $bgcolor;

  var $defaultWidth;
  var $defaultHeight;

  function LibFlash($file = '') {
    $this->defaultWidth = 300;
    $this->defaultHeight = 300;

    if ($file) {
      $this->file = $file;
    }
  }

  // Render a Flash object
  function renderObject($file = '') {
    $strStream = '';

    if (!$file) {
      $file = $this->file;
    }

    $width = $this->width;
    $height = $this->height;

    if (!$width) {
      $width = $this->defaultWidth;
    }

    if (!$height) {
      $height = $this->defaultHeight;
    }

    $bgcolor = $this->bgcolor;

    if ($file) {
      // The following code is the normal macromedia one
      // /but it does not validate xhtml strict
      $strStream = <<<HEREDOC
<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000'
  codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0'
  width='$width'
  height='$height'>
<param name='movie' value='$file' />
<param name='quality' value='high' />
<param name='bgcolor' value='$bgcolor' />
<param name='wmode' value='transparent' />
<embed src='$file'
  quality='high'
  wmode='transparent'
  bgcolor='$bgcolor'
  width='$width'
  height='$height'
  type='application/x-shockwave-flash'
  pluginspage='http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash'>
</embed>
</object>
HEREDOC;
      // The following code validates xhtml strict but prevents IE from streaming the file
      // That is, IE will download the full file before starting playing it
/*
        $strStream = <<<HEREDOC
<object type="application/x-shockwave-flash" data="$file" width="$width" height="$height">
  <param name="movie" value="$file" />
  <param name='bgcolor' value='$bgcolor' />
</object>
HEREDOC;
 */
    }

    return($strStream);
  }

  // Check if the file is a Flash media file
  function isFlashFile($file) {
    $isMedia = false;

    $bits = explode(".", $file);

    if (count($bits) > 1) {
      $suffix = strtolower($bits[count($bits)-1]);
      if ($suffix == 'swf' || $suffix == 'flv') {
        $isMedia = true;
      }
    }

    return($isMedia);
  }

}

?>
