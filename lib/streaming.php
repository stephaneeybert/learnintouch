<?php

class LibStreaming {

  var $miniPlayer;
  var $mediaFileUrl;
  var $autostart;
  var $controls;
  var $noStopButton;

  function LibStreaming() {
    $this->autostart = false;
    $this->controls = false;
    $this->miniPlayer = false;
    $this->noStopButton = false;
  }

  // Render an HTML5 audio player customized and controlled by javascript
  function renderHtml5AudioPlayerCustom() {
    global $gImagesUserUrl;

    $str = '';

    $mediaFileUrl = $this->mediaFileUrl;

    $uniqueId = LibUtils::generateUniqueId();

    $playPauseButtonDomId = "playerPlayPauseButton$uniqueId";
    $stopButtonDomId = "playerStopButton$uniqueId";

    $buttonPauseImage = $gImagesUserUrl . '/' . IMAGE_AUDIO_PAUSE;
    $buttonPlayImage = $gImagesUserUrl . '/' . IMAGE_AUDIO_PLAY;
    $buttonStopImage = $gImagesUserUrl . '/' . IMAGE_AUDIO_STOP;

    if ($mediaFileUrl) {
      if ($this->autostart == true) {
        $autostart = "true";
      } else if ($this->autostart == false) {
        $autostart = "false";
      }

      $str = <<<HEREDOC
<img id="$playPauseButtonDomId" src="$buttonPlayImage" title="" />
HEREDOC;

      if (!$this->noStopButton) {
        $str .= <<<HEREDOC
 <img id="$stopButtonDomId" src="$buttonStopImage" title="" />
HEREDOC;
      }

      $str .= <<<HEREDOC
<script type="text/javascript">
$(document).ready(function() {
HEREDOC;

      $str .= <<<HEREDOC
  var audio$uniqueId;
  soundManager.onready(function() {
    audio$uniqueId = soundManager.createSound({
      id:'audio$uniqueId',
      url:'$mediaFileUrl',
      autoPlay:$autostart,
      onplay:function() {
        document.getElementById("$playPauseButtonDomId").src = "$buttonPauseImage";
      },
      onresume:function() {
        document.getElementById("$playPauseButtonDomId").src = "$buttonPauseImage";
      },
      onpause:function() {
        document.getElementById("$playPauseButtonDomId").src = "$buttonPlayImage";
      },
      onstop:function() {
        document.getElementById("$playPauseButtonDomId").src = "$buttonPlayImage";
      },
      onfinish:function() {
        document.getElementById("$playPauseButtonDomId").src = "$buttonPlayImage";
      }
    });
    document.getElementById("$playPauseButtonDomId").addEventListener('click', function(){
      audio$uniqueId.togglePause();
    }, false);
    if (document.getElementById("$stopButtonDomId") != null) {
      document.getElementById("$stopButtonDomId").addEventListener('click', function(){
        audio$uniqueId.stop();
      }, false);
    }
  });
HEREDOC;
    }

    if ($this->autostart == true) {
      $str .= <<<HEREDOC
  soundManager.onready(function() {
    audio$uniqueId.play();
  });
HEREDOC;
    }

    $str .= <<<HEREDOC
});
</script>
HEREDOC;

    return($str);
  }

  // Render a video player
  function renderVideoPlayer() {
    global $gPlayerUrl;

    $str = '';

    $mediaFileUrl = $this->mediaFileUrl;

    if ($mediaFileUrl) {
      if ($this->autostart == true) {
        $autostart = "true";
      } else if ($this->autostart == false) {
        $autostart = "false";
      }

      $width = 480;
      $height = 270;
      $str = <<<HEREDOC
<video width="$width" height="$height" controls="controls" autoplay="$autostart">
  <source src="$mediaFileUrl" type="video/mp4" />
  <object type="application/x-shockwave-flash" width="$width" height="$height" data="$mediaFileUrl">
    <param name="movie" value="$mediaFileUrl" />
    <param name="wmode" value="transparent" />
    <!--[if lte IE 6 ]>
      <embed src="$mediaFileUrl" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="$width" height="$height"></embed>
    <![endif]-->
    <p>You should use a more recent browser to view the video.</p>
  </object>
</video>
HEREDOC;
    }

    return($str);
  }

  // Render a Flash video player
  function renderFlashVideoPlayer() {
    global $gPlayerUrl;
    global $gSwfPlayerUrl;

    $str = '';

    $fileUrl = $gSwfPlayerUrl . '/FlvPlayer.swf';
    $mediaFileUrl = $this->mediaFileUrl;

    if ($mediaFileUrl) {
      if ($this->autostart == true) {
        $autostart = "true";
      } else if ($this->autostart == false) {
        $autostart = "false";
      }

      $width = 480;
      $height = 270;
      $str = <<<HEREDOC
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" 
  codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" 
  id="FlvPlayer" 
  width='$width'
  height='$height'>
<param name="allowScriptAccess" value="sameDomain" />
<param name="allowFullScreen" value="true" />
<param name="movie" value="$fileUrl" />
<param name="quality" value="high" />
<param name="FlashVars" value="flvpFolderLocation=$gPlayerUrl/&flvpVideoSource=$mediaFileUrl&flvpWidth=$width&flvpHeight=$height&autostart=$autostart&flvpInitVolume=50&flvpTurnOnCorners=true" />
<embed src="$fileUrl" flashvars="flvpFolderLocation=$gPlayerUrl/&flvpVideoSource=$mediaFileUrl&flvpWidth=$width&flvpHeight=$height&flvpInitVolume=50&flvpTurnOnCorners=true" quality="high" width="$width" height="$height" name="FlvPlayer" align="middle" allowScriptAccess="sameDomain" allowFullScreen="true" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer" />
</object>
HEREDOC;
    }

    return($str);
  }

  // Check if the file can be streamed by the Real Player
  function isRPFile($mediaFileUrl) {
    $isMedia = false;

    $bits = explode(".", $mediaFileUrl);

    if (count($bits) > 1) {
      $suffix = strtolower($bits[count($bits)-1]);
      if ($suffix == "rm" || $suffix == "ram" || $suffix == "rp") {
        $isMedia = true;
      }
    }

    return($isMedia);
  }

  // Check if the file can be streamed by the Windows Media Player
  function isWMPFile($mediaFileUrl) {
    $isMedia = false;

    $bits = explode(".", $mediaFileUrl);

    if (count($bits) > 1) {
      $suffix = strtolower($bits[count($bits)-1]);
      if ($suffix == "wmv" || $suffix == "asf" || $suffix == "asx" || $suffix == "wma" || $suffix == "wm") {
        $isMedia = true;
      }
    }

    return($isMedia);
  }

  // Check if the file can be streamed by Quicktime
  function isQTFile($mediaFileUrl) {
    $isMedia = false;

    $bits = explode(".", $mediaFileUrl);

    if (count($bits) > 1) {
      $suffix = strtolower($bits[count($bits)-1]);
      if ($suffix == "mov" || $suffix == "qt") {
        $isMedia = true;
      }
    }

    return($isMedia);
  }

  // Check if the file can be streamed by a Flash script
  function isFlashAudioFile($mediaFileUrl) {
    $isMedia = false;

    $bits = explode(".", $mediaFileUrl);

    if (count($bits) > 1) {
      $suffix = strtolower($bits[count($bits)-1]);
      if ($suffix == 'mp3' || $suffix == 'm4a') {
        $isMedia = true;
      }
    }

    return($isMedia);
  }

  // Check if the file can be streamed by HTML5 
  function isVideoFile($mediaFileUrl) {
    $isMedia = false;

    $bits = explode(".", $mediaFileUrl);

    if (count($bits) > 1) {
      $suffix = strtolower($bits[count($bits)-1]);
      $allowedVideoTypes = array('avi', 'ogg', '264', 'mkv', 'mp4', 'm4v', 'ogv');
      if (in_array($suffix, $allowedVideoTypes)) {
        $isMedia = true;
      }
    }

    return($isMedia);
  }

  // Render a youtube video url
  // Check if the file can be streamed by a Flash FLV script
  function isFlashVideoFile($mediaFileUrl) {
    $isMedia = false;

    $bits = explode(".", $mediaFileUrl);

    if (count($bits) > 1) {
      $suffix = strtolower($bits[count($bits)-1]);
      if ($suffix == 'flv') {
        $isMedia = true;
      }
    }

    return($isMedia);
  }

  // Render a youtube video url
  function renderYouTubeVideoUrl($youtubeVideoId) {
    $url = 'http://www.youtube.com/embed/' . $youtubeVideoId;

    return($url);
  }

  // Render a vimeo video url
  function renderVimeoVideoUrl($vimeoVideoId) {
    $url = 'http://player.vimeo.com/video/' . $vimeoVideoId;

    return($url);
  }

  function renderVideoFromUrl($url) {
    $str = '';

    if (strstr($url, "youtube")) {
      $result = preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $url, $matches);
      if ($result) {
        $youtubeVideoId = $matches[0];
        $playerUrl = LibStreaming::renderYouTubeVideoUrl($youtubeVideoId);
        $str = "<iframe name='video' src='$playerUrl' type='text/html' frameborder='0' width='370' height='300'></iframe>";
      }
    } else if (strstr($url, "vimeo")) {
      $result = preg_match('/(\d+)/', $url, $matches);
      if ($result) {
        $vimeoVideoId = $matches[0];
        $playerUrl = LibStreaming::renderVimeoVideoUrl($vimeoVideoId);
        $str = "<iframe name='video' src='$playerUrl' type='text/html' frameborder='0' width='370' height='300'></iframe>";
      }
    }

    return($str);
  }

}

?>
