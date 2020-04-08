<?

class CommonUtils {

  var $profileUtils;

  // Change the width of a video so as to fit on a smartphone
  function adjustVideoWidthToPhone($video) {
    $video = preg_replace('/width=\"([0-9]+)\"/i', 'width="' . TEMPLATE_PHONE_CONTENT_WIDTH . '"', $video);

    return($video);
  }

  // Render the markup for an image cycle
  function renderImageCycle($cycleDomId, $items, $nextOnClick, $timeout) {
    global $gJsUrl;

    $str = '';

    // The jquery cycle is available at http://jquery.malsup.com/cycle/download.html
    // The images must be displayed before the script so as to avoid the cycle overlapping
    // above the content sitting below
    $str .= "<div id='$cycleDomId'>";
    for ($i = 0; $i < count($items); $i++) {
      $item = $items[$i];
      $str .= $item;
    }
    $str .= "</div>";

    $str .= <<<HEREDOC
<style type="text/css">
#$cycleDomId { text-align:center; }
#$cycleDomId { margin:auto; }
#$cycleDomId img { display:none; }
</style>
HEREDOC;

    if ($nextOnClick) {
      $strNextOnClick = "next:'#$cycleDomId',";
    } else {
      $strNextOnClick = '';
    }

    if ($timeout) {
      $timeout = $timeout * 1000;
      $strTimeout = "timeout:$timeout,";
    } else {
      $strTimeout = '';
    }

    $str .= <<<HEREDOC
<script type="text/javascript">
$(document).ready(function() {
  $('#$cycleDomId img').show();
  $('#$cycleDomId').cycle({
    pause:1,
    $strTimeout
    $strNextOnClick
    fx:'fade'
  });
});
</script>
HEREDOC;

    return($str);
  }

  function getGoogleLanguageCode($languageCode) {
    if ($languageCode == 'se') {
      $languageCode = 'sv';
    }

    return($languageCode);
  }

  function getGoogleTextTranslationUrl($text, $fromLanguageCode, $toLanguageCode) {
    $toLanguageCode = $this->getGoogleLanguageCode($toLanguageCode);

    $text = urlencode($text);
    $fromLanguageCode = urlencode($fromLanguageCode);
    $toLanguageCode = urlencode($toLanguageCode);
    $googleAPIKey = "AIzaSyDJKmkHs_FS3JscsRQLrfcbtP0nxGSVaRw";

// TODO The Google Translate API v2 is now a paid service
    $url = "https://www.googleapis.com/language/translate/v2?key=$googleAPIKey&source=$fromLanguageCode&target=$toLanguageCode&q=$text";

    return($url);
  }

  // Get the url for a geo location of an IP address
  function mapIP($IP) {
//    $url = "http://www.localiser-ip.com?ip=$IP";
    $url = "http://geomaplookup.net/?ip=$IP";
  
    return($url);
  }

  // Get a text translation from Google
  function getGoogleTextTranslation($text, $fromLanguageCode, $toLanguageCode) {
    $translation = '';

    if ($text && $fromLanguageCode && $toLanguageCode) {
      $url = $this->getGoogleTextTranslationUrl($text, $fromLanguageCode, $toLanguageCode);

      $jsonResponse = LibFile::curlGetFileContent($url);

      $objectResponse = json_decode($jsonResponse);
      if ($objectResponse->responseStatus == 200 || !$objectResponse->responseStatus) {
        $translation = $objectResponse->data->translations[0]->translatedText;
        if ($translation) {
          $translation = LibString::br2nl($translation);
          // For whatever reason Google leaves a blank space after the line break
          $translation = str_replace("\n ", "\n", $translation);
        }
      } else {
        error_log("Google Translation responded with the status : " . $objectResponse->responseStatus);
        error_log("and the data : " . $objectResponse->responseData->translatedText);
      }
    }

    return($translation);
  }

  function renderGoogleSearch($search, $message, $label, $domainName) {
    $str = <<<HEREDOC
<style type="text/css">
div.gsc-control  { width: 90%; }
div.gsc-search-box  { width: 90%; }
</style>

<script src="http://www.google.com/jsapi" type="text/javascript"></script>
<script type="text/javascript">
//<![CDATA[
google.load('search', '1.0');

function OnLoad() {
  // Create a search control
  var searchControl = new google.search.SearchControl();

  // Add in a searcher
  var webSearch = new google.search.WebSearch();
  webSearch.setUserDefinedLabel("$label");
  webSearch.setSiteRestriction("$domainName");

  options = new google.search.SearcherOptions();
  options.setExpandMode(google.search.SearchControl.EXPAND_MODE_OPEN);
  searchControl.addSearcher(webSearch, options);

  searchControl.setResultSetSize(google.search.Search.LARGE_RESULTSET)

  // Tell the searcher to draw itself and tell it where to attach
  searchControl.draw(document.getElementById('googleSearchResult'));

  // Execute an inital search
  searchControl.execute("$search");
}

google.setOnLoadCallback(OnLoad, true);

//]]>
</script>
<div id='googleSearchResult'>$message</div>
HEREDOC;

    return($str);
  }

  // Render the social networks buttons
  function renderSocialNetworksButtons($name, $url) {
    global $gCommonImagesUrl;
    global $gImageLinkedInShare;
    global $gSetupWebsiteUrl;

    $facebookApplicationId = $this->profileUtils->getFacebookApplicationId();
    $linkedinApiKey = $this->profileUtils->getLinkedinApiKey();

    $name = urlencode($name);

    $str = '';

    if ($facebookApplicationId) {
      $str .= " <fb:like href='$url' send='false' layout='button_count' show_faces='false' action='like' colorscheme='light' style='top:-4px;'></fb:like>";
    }

    if ($linkedinApiKey) {
      $encodedUrl = urlencode($url);
      $str .= " <span style='position:relative; top:5px;'><script type='in/share' data-url='$encodedUrl' data-name='$name'></script></span>";
      //      $str .= " <a href='http://www.linkedin.com/shareArticle?mini=true&url=$encodedUrl&title=$name&source=$gSetupWebsiteUrl' target='_blank'><img border='0' src='$gCommonImagesUrl/$gImageLinkedInShare' title='LinkedIn'></a>";
    }

    $str .= " <g:plusone href='$url' size='medium' count='false'></g:plusone>";

    return($str);
  }

  // updated by javascript is displayed by the browser
  function preventPageCaching() {
    header('Cache-Control: private, no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Pragma: no-cache');
  }

  // Render a search field
  function renderMiniSearch($title) {
    global $gUtilsUrl;

    $str = '';

    $str .= "<div class='search'>";

    $pattern = LibEnv::getEnvHttpPOST("pattern");

    $str .= "<form action='$gUtilsUrl/google_search.php' method='post' name='search_mini_form'>";

    $str .= "<div style='white-space:nowrap;'>"
      . "<span class='search_field'>"
      . "<input class='search_input' type='text' name='pattern' value='$title' onfocus=\"if (this.value == '$title') { this.value = '';}\" onblur=\"if (this.value == '') { this.value = '$title';}\" />"
      . "</span>"
      . "</div>";

    $str .= "</form>";

    $str .= "</div>";

    return($str);
  }

  function renderServerPing() {
    global $gUtilsUrl;

    $str = <<<HEREDOC
<script type='text/javascript'>
// Ping the server every 20 minutes to avoid a session time out
window.setInterval("ajaxAsynchronousRequest('$gUtilsUrl/ping.php', '')", 1200000);
</script>
HEREDOC;

    return($str);
  }

  function ajaxAutocompleteForList($url, $textFieldId, $hiddenFieldId) {
    $str = $this->ajaxAutocomplete($url, $textFieldId, $hiddenFieldId, 50, 2, true, false);

    return($str);
  }

  function ajaxAutocomplete($url, $textFieldId, $hiddenFieldId, $max = 50, $minChar = 2, $matchContains = true, $emptyDefault = true) {
    if ($emptyDefault) {
      $emptyValue = '';
    } else {
      $emptyValue = '-1';
    }

    $strJsSuggest = <<<HEREDOC
<script type='text/javascript'>
$(document).ready(function() {

  $("#$textFieldId").keyup(function() {
    if (!this.value) {
      $("#$hiddenFieldId").attr("value", '$emptyValue');
      $("#$hiddenFieldId").change();
      $("#$textFieldId").attr("value", '');
      $("#$textFieldId").change();
    }
  });

  $("#$textFieldId").autocomplete({
    source: "$url",
    minLength: $minChar,
    html: true,
    select: function(event, ui) {
      if (ui.item) {
        $("#$hiddenFieldId").attr("value", ui.item.id);
        $("#$hiddenFieldId").change();
      }
    }
  });

});
</script>
HEREDOC;

    return($strJsSuggest);
  }

  // Call a batch script asynchronuously so as to avoid a time out in the browser
  function execlCLIwget($scriptFileUrl) {
    $command = "wget \"$scriptFileUrl\" -q -O - -b";
    system($command);
  }

  function renderKeyboard($strLetters, $caption, $domId) {
    $str = <<<HEREDOC
<script type='text/javascript'>
function typeTextIntoFocusedElement(text) {
  if (focusedElement) {
    focusedElement.value += text;
  }
}

var keyboardClicked = 0;
var latestChangedField;
$('.$domId').bind("click", function (event) {
  keyboardClicked = 1;
  if (latestChangedField) {
    latestChangedField.focus();
  }
});
</script>
HEREDOC;

    if ($strLetters) {
      $letters = explode(' ', $strLetters);
      foreach ($letters as $letter) {
        $str .= "<span><a href=\"javascript:typeTextIntoFocusedElement('$letter');\" title='$caption'> $letter </a></span>";
      }
    } else {
      $str .= <<<HEREDOC
<span><a href="javascript:typeTextIntoFocusedElement('&#224;');" title='$caption'> &#224; </a></span>
<span><a href="javascript:typeTextIntoFocusedElement('&#231;');" title='$caption'> &#231; </a></span>
<span><a href="javascript:typeTextIntoFocusedElement('&#233;');" title='$caption'> &#233; </a></span>
<span><a href="javascript:typeTextIntoFocusedElement('&#234;');" title='$caption'> &#234; </a></span>
<span><a href="javascript:typeTextIntoFocusedElement('&#232;');" title='$caption'> &#232; </a></span>
<span><a href="javascript:typeTextIntoFocusedElement('&#235;');" title='$caption'> &#235; </a></span>
<span><a href="javascript:typeTextIntoFocusedElement('&#244;');" title='$caption'> &#244; </a></span>
<span><a href="javascript:typeTextIntoFocusedElement('&#238;');" title='$caption'> &#238; </a></span>
<span><a href="javascript:typeTextIntoFocusedElement('&#239;');" title='$caption'> &#239; </a></span>
<span><a href="javascript:typeTextIntoFocusedElement('&#249;');" title='$caption'> &#249; </a></span>
<span><a href="javascript:typeTextIntoFocusedElement('&#251;');" title='$caption'> &#251; </a></span>
<span><a href="javascript:typeTextIntoFocusedElement('&#252;');" title='$caption'> &#252; </a></span>
<span><a href="javascript:typeTextIntoFocusedElement('&#246;');" title='$caption'> &#246; </a></span>
<span><a href="javascript:typeTextIntoFocusedElement('&#228;');" title='$caption'> &#228; </a></span>
<span><a href="javascript:typeTextIntoFocusedElement('&#229;');" title='$caption'> &#229; </a></span>
<span><a href="javascript:typeTextIntoFocusedElement('&#248;');" title='$caption'> &#248; </a></span>
<span><a href="javascript:typeTextIntoFocusedElement('&#230;');" title='$caption'> &#230; </a></span>
<span><a href="javascript:typeTextIntoFocusedElement('&#156;');" title='$caption'> &#156; </a></span>
<span><a href="javascript:typeTextIntoFocusedElement('&#223;');" title='$caption'> &#223; </a></span>
HEREDOC;
    }

    return($str);
  }

  // Get the subsets of list items
  function getSubsets($listStep, $listNbItems) {
    global $PHP_SELF;
    global $gJSNoStatus;

    $str = '';

    if (!$listStep) {
      $listStep = 20;
    }

    $subsets = array();
    if ($listNbItems > $listStep) {
      $k = 0;
      for ($i = 0; $i < $listNbItems; $i = $i + $listStep) {
        $j = $i + $listStep - 1;
        if ($j >= $listNbItems) {
          $j = $listNbItems - 1;
        }

        $subsets[$k] = " <a href='$PHP_SELF?listIndex=$i'"
          . " $gJSNoStatus title=''>[$i-$j]</a>";
        $k++;
      }
    }

    foreach ($subsets as $subset) {
      $str .= $subset;
    }

    return($str);
  }

  // Get the url for the next subset
  function getNextSubsetUrl($listIndex, $listStep) {
    global $PHP_SELF;

    $listIndex = $listIndex + $listStep;

    $str = "$PHP_SELF?listIndex=$listIndex";

    return($str);
  }

  // Get the url for the previous subset
  function getPreviousSubsetUrl($listIndex, $listStep) {
    global $PHP_SELF;

    $listIndex = $listIndex - $listStep;

    $str = "$PHP_SELF?listIndex=$listIndex";

    return($str);
  }

  // Render the warning messages
  function renderWarningMessages($warnings) {
    $str = '';

    if (count($warnings) > 0) {
      foreach ($warnings as $warning) {
        $str .= "<div class='system_warning'>" . $warning . "</div>";
      }
    }

    return($str);
  }

  // Get the names of all the content images if any
  function getContentImages($content) {
    $images = array();

    if (strstr($content, '<img') && preg_match_all("/<img.*?src *?= *?['|\"]([\S][^>][^'][^\"]*?)['|\"]/i", $content, $matches)) {
      $matches = $matches[1];
      if (count($matches) > 0) {
        foreach ($matches as $match) {
          array_push($images, basename($match));
        }
      }

      $temp = array_unique($images);
      $images = array_values($temp);
    }

    return($images);
  }

  // Set the texts into a string containing several texts specified by a separator
  // The texts are an array:
  // { a1, b1 }
  // a1||b1
  // The texts can also be an array of arrays:
  // { {a1, a2}, {b1, b2} }
  // a1|a2||b1|b2
  // { {a1, a2, a3}, {b1, b2, b3} }
  // a1|a2|a3||b1|b2|b3
  function setTexts($texts) {
    $strText = '';

    if (is_array($texts)) {
      $mainTexts = $texts;
    } else {
      $mainTexts = array($texts);
    }

    foreach ($mainTexts as $mainText) {
      if (is_array($mainText)) {
        $subTexts = $mainText;
      } else {
        $subTexts = array($mainText);
      }
      $strSubText = '';
      foreach ($subTexts as $subText) {
        // Remove any unlikely separator in the content to prevent conflict with the content separator
        $subText = str_replace(LANGUAGE_TEXT_SEPARATOR, '', $subText);

        // Make sure the content is not empty as it would lead to a confusing ||| series of separators
        if (!$subText) {
          $subText = ' ';
        }
        if ($strSubText) {
          $strSubText .= LANGUAGE_TEXT_SEPARATOR;
        }
        $strSubText .= $subText;
      }
      if ($strText) {
        $strText .= LANGUAGE_TEXT_SEPARATOR . LANGUAGE_TEXT_SEPARATOR;
      }
      $strText .= $strSubText;
    }

    return($strText);
  }

  // Get the texts of a string containing several texts specified by a separator
  function getTexts($strText) {
    if (!strstr($strText, LANGUAGE_TEXT_SEPARATOR . LANGUAGE_TEXT_SEPARATOR)) {
      if (!strstr($strText, LANGUAGE_TEXT_SEPARATOR)) {
        return(array($strText));
      } else {
        return(explode(LANGUAGE_TEXT_SEPARATOR, $strText));
      }
    }
    $texts = explode(LANGUAGE_TEXT_SEPARATOR . LANGUAGE_TEXT_SEPARATOR, $strText);
    $mainTexts = array();
    foreach ($texts as $text) {
      if (!strstr($text, LANGUAGE_TEXT_SEPARATOR)) {
        $subText = array($text);
      } else {
        $subText = explode(LANGUAGE_TEXT_SEPARATOR, $text);
      }
      array_push($mainTexts, $subText);
    }
    return($mainTexts);
  }

  // Render the powered by leanintouch logo
  function renderPoweredByLearnInTouch() {
    global $gJSNoStatus;
    global $gCommonImagesUrl;
    global $gImagePoweredByLearnInTouch;

    $protocol = LibUtils::getProtocol();

    $str = "<a href='" . $protocol . "://www.thalasoft.com' target='_blank' $gJSNoStatus>"
      . "<img src='$gCommonImagesUrl/$gImagePoweredByLearnInTouch' class='tipPopup' border='0' title='Learn, teach and keep in touch !'>"
      . "</a>";

    return($str);
  }

}

?>
