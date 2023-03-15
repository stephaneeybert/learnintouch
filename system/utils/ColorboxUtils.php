<?

class ColorboxUtils {

  var $mlText;
  var $websiteText;

  var $languageUtils;

  function __construct() {
  }

  function loadLanguageTexts() {
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  // Render the javascript for the colorbox feature
  function renderJsColorbox() {
    global $gJsUrl;
    global $gIsPhoneClient;

    $this->loadLanguageTexts();

    $slideshowStart = $this->websiteText[45];
    $slideshowStop = $this->websiteText[46];
    $previous = $this->websiteText[2];
    $next = $this->websiteText[3];
    $close = $this->websiteText[44];
    $of = $this->websiteText[47];

    if ($gIsPhoneClient) {
      $width = 'width:"100%",';
      $height = 'height:"100%",';
    } else {
      $width = 'width:"70%",';
      $height = 'height:"70%",';
    }

    $jsColorbox = <<<HEREDOC
<link rel='stylesheet' type='text/css' href='$gJsUrl/jquery/colorbox/colorbox/colorbox.css' />
<script type='text/javascript' src='$gJsUrl/jquery/colorbox/colorbox/jquery.colorbox-min.js'></script>
<script type="text/javascript">
$(document).ready(function() {
  $("a[rel='no_style_colorbox']").colorbox({
    slideshowStart:"$slideshowStart",
    slideshowStop:"$slideshowStop",
    previous:"$previous",
    next:"$next",
    close:"$close",
    $width
    $height
    current:"{current} $of {total}"
  });
});
</script>
HEREDOC;

    return($jsColorbox);
  }

  // Render the colorbox on the admin panel
  function renderAdminColorbox() {
    $jsColorbox = <<<HEREDOC
<script type="text/javascript">
$(document).ready(function() {
  $("a[rel='no_style_colorbox']").colorbox({
    transition:"none",
    width:"75%",
    height:"75%"
  });
});
</script>
HEREDOC;

    return($jsColorbox);
  }

  // Render the colorbox on the website
  function renderWebsiteColorbox($slideshowSpeed = 5) {
    if ($slideshowSpeed > 0) {
      $slideShow = 'true';
      $slideshowSpeed = $slideshowSpeed * 1000;
    } else {
      $slideShow = 'false';
    }

    $jsColorbox = <<<HEREDOC
<script type="text/javascript">
$(document).ready(function() {
  $("a[rel='no_style_colorbox']").colorbox({
    transition:"fade",
    slideshowSpeed:$slideshowSpeed,
    slideshow:$slideShow
  });
});
</script>
HEREDOC;

    return($jsColorbox);
  }

}

?>
