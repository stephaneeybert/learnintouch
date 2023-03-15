<?

class PlayerUtils {

  var $mlText;
  var $websiteText;

  // THe chosen player if any
  var $player;

  // Start to play automatically
  var $autostart;

  // Display the player controls
  var $controls;

  function __construct() {
    $this->init();
  }

  function init() {
    $this->autostart = false;
    $this->controls = false;
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  function setPlayer($player) {
    $this->player = $player;
  }

  function getPlayerNames() {
    $this->loadLanguageTexts();

    $players = array(
        '' => '',
        PLAYER_FLASH_AUDIO_MP3 => $this->mlText[11],
        PLAYER_FLASH_AUDIO_MP3_SPEAKER => $this->mlText[9],
        PLAYER_VIDEO => $this->mlText[1],
        PLAYER_FLASH_VIDEO => $this->mlText[2],
        );

    return($players);
  }

  // Set the automatic start
  function setAutostart($autostart) {
    $this->autostart = $autostart;
  }

  // Set the player controls display
  function setControls($controls) {
    $this->controls = $controls;
  }

  // Guess the players from an audio or video file
  function guessPlayers($mediaFileUrl) {
    $listPlayers = array();

    $libStreaming = new LibStreaming();

    if ($libStreaming->isVideoFile($mediaFileUrl)) {
      array_push($listPlayers, PLAYER_VIDEO);
    }

    if ($libStreaming->isFlashVideoFile($mediaFileUrl)) {
      array_push($listPlayers, PLAYER_FLASH_VIDEO);
    }

    if ($libStreaming->isFlashAudioFile($mediaFileUrl)) {
      array_push($listPlayers, PLAYER_FLASH_AUDIO_MP3);
    }

    $libFlash = new LibFlash();
    if ($libFlash->isFlashFile($mediaFileUrl)) {
      array_push($listPlayers, PLAYER_FLASH);
    }

    return($listPlayers);
  }

  // Guess the player from an audio or video file
  // Use the first compatible player
  function guessPlayer($mediaFileUrl) {
    $listPlayers = $this->guessPlayers($mediaFileUrl);
    if (count($listPlayers) > 0) {
      $player = $listPlayers[0];
    } else {
      $player = '';
    }

    return($player);
  }

  // Render the download link
  function renderDownload($file, $downloadImage = '') {
    global $gImagesUserUrl;
    global $gUtilsUrl;
    global $gIsTouchClient;
    global $gRootPath;
    global $gHomeUrl;

    $this->loadLanguageTexts();

    $str = '';

    if ($file) {
      if ($gIsTouchClient) {
        // This hack is to have the iPhone / iPod able to download the file
        $url = str_replace($gRootPath, $gHomeUrl . '/', $file);
        $onclick = "onclick=\"window.location.href='$url';\"";
      } else {
        $url = "$gUtilsUrl/download.php?filename=$file";
        $onclick = '';
      }
      if (!$downloadImage) {
        $downloadImage = IMAGE_COMMON_DOWNLOAD;
      }
      $str = "<a href='$url' $onclick>"
        . "<img src='$gImagesUserUrl/" . $downloadImage
        . "' class='no_style_image_icon' title='"
        . $this->websiteText[0]
        . "' alt='' /></a>";
    }

    return($str);
  }

  // Render the player
  function renderPlayer($mediaFileUrl) {
    global $gImagesUserUrl;
    global $gIsTouchClient;

    $str = '';

    if ($this->player) {
      $player = $this->player;
    } else {
      $player = $this->guessPlayer($mediaFileUrl);
    }

    $libStreaming = new LibStreaming();
    $libStreaming->autostart = $this->autostart;
    $libStreaming->controls = $this->controls;

    $libStreaming->mediaFileUrl = $mediaFileUrl;

    if ($player == PLAYER_FLASH_AUDIO_MP3) {
      if (!$gIsTouchClient) {
        $str = $libStreaming->renderHtml5AudioPlayerCustom();
      } else {
        $str = $libStreaming->renderHtml5AudioPlayerCustom();
      }
    } else if ($player == PLAYER_FLASH_AUDIO_MP3_SPEAKER) {
      $libStreaming->noStopButton = true;
      if (!$gIsTouchClient) {
        $str = $libStreaming->renderHtml5AudioPlayerCustom();
      } else {
        $str = $libStreaming->renderHtml5AudioPlayerCustom();
      }
    } else if ($player == PLAYER_VIDEO) {
      if (!$gIsTouchClient) {
        $str = $libStreaming->renderVideoPlayer();
      } else {
        $str = $this->renderDownload($mediaFileUrl, IMAGE_PLAYER_VIDEO);
      }
    } else if ($player == PLAYER_FLASH_VIDEO) {
      if (!$gIsTouchClient) {
        $str = $libStreaming->renderFlashVideoPlayer();
      } else {
        $str = $this->renderDownload($mediaFileUrl, IMAGE_PLAYER_VIDEO);
      }
    }

    return($str);
  }

}

?>
