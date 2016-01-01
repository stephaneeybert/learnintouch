<?

class LexiconEntryUtils extends LexiconEntryDB {

  var $mlText;
  var $websiteText;

  var $imageFileSize;
  var $imageFilePath;
  var $imageFileUrl;

  var $preferences;

  var $languageUtils;
  var $preferenceUtils;
  var $commonUtils;
  var $userUtils;

  function LexiconEntryUtils() {
    $this->LexiconEntryDB();

    $this->init();
  }

  function init() {
    global $gDataPath;
    global $gDataUrl;

    $this->imageFileSize = 200000;
    $this->imageFilePath = $gDataPath . 'lexicon/image/';
    $this->imageFileUrl = $gDataUrl . '/lexicon/image';
  }

  function createDirectories() {
    global $gDataPath;
    global $gDataUrl;

    if (!is_dir($this->imageFilePath)) {
      if (!is_dir($gDataPath . 'lexicon')) {
        mkdir($gDataPath . 'lexicon');
      }
      mkdir($this->imageFilePath);
      chmod($this->imageFilePath, 0755);
    }
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  function loadPreferences() {
    $this->loadLanguageTexts();

    $this->preferences = array(
      "LEXICON_SUGGEST_DEFINITIONS" =>
      array($this->mlText[0], $this->mlText[1], PREFERENCE_TYPE_BOOLEAN, ''),
        "LEXICON_USER_LOGIN_REQUIRED" =>
        array($this->mlText[2], $this->mlText[3], PREFERENCE_TYPE_BOOLEAN, ''),
          "LEXIKON_IMAGE_WIDTH" =>
          array($this->mlText[21], $this->mlText[22], PREFERENCE_TYPE_TEXT, 300),
            "LEXIKON_PHONE_IMAGE_WIDTH" =>
            array($this->mlText[23], $this->mlText[24], PREFERENCE_TYPE_TEXT, 140),
            );

    $this->preferenceUtils->init($this->preferences);
  }

  // Get the width of the image
  function getImageWidth() {
    global $gIsPhoneClient;

    if ($gIsPhoneClient) {
      $width = $this->preferenceUtils->getValue("LEXIKON_PHONE_IMAGE_WIDTH");
    } else {
      $width = $this->preferenceUtils->getValue("LEXIKON_IMAGE_WIDTH");
    }

    return($width);
  }

  // Check if some definitions are to be suggested from an external dictionary
  // when adding a new lexicon entry
  function suggestDefinitions() {
    $suggest = $this->preferenceUtils->getValue("LEXICON_SUGGEST_DEFINITIONS");

    return($suggest);
  }

  // Check if the lexicon explanations are displayed only to the users who have logged in
  function userLoginIsRequired() {
    $required = $this->preferenceUtils->getValue("LEXICON_USER_LOGIN_REQUIRED");

    return($required);
  }

  // Remove the non referenced files from the directory
  function deleteUnusedImageFiles() {
    $handle = opendir($this->imageFilePath);
    while ($oneFile = readdir($handle)) {
      if ($oneFile != "." && $oneFile != ".." && !strstr($oneFile, '*')) {
        if (!$this->imageIsUsed($oneFile)) {
          $oneFile = str_replace(" ", "\\ ", $oneFile);
          if (file_exists($this->imageFilePath . $oneFile)) {
            unlink($this->imageFilePath . $oneFile);
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

    if ($result = $this->dao->selectByImage($image)) {
      if ($result->getRowCount() < 1) {
        $isUsed = false;
      }
    }

    return($isUsed);
  }

  // Render an entry
  function renderImage($image) {
    $str = '';

    if ($image) {
      $imageUrl = $this->imageFileUrl;
      $str = "<img src='$imageUrl/$image' border='0' title='' href=''>";
    }

    return($str);
  }

  // Get the explanation of a lexicon entry
  function NOT_USED_getEntryExplanation($name) {
    $explanation = '';

    // If the pair exists then return its value
    if ($name) {
      if ($lexiconEntry = $this->selectByName($name)) {
        $explanation = $lexiconEntry->getExplanation();
        $image = $lexiconEntry->getImage();
        if ($image) {
          $explanation .= $this->renderImage($image);
        }
      }
    }

    return($explanation);
  }

  function renderLexiconTooltipsForPrintFromContent($content) {
    $tooltips = $this->getLexiconTooltipsFromContent($content);

    $str = '';

    foreach ($tooltips as $tooltip) {
      list($lexiconEntryId, $name, $explanation, $image) = $tooltip;
      if ($explanation) {
        $str .= " <span class='lexicon_entry_list_item'><span class='lexicon_entry_list_item_name'>$name</span> : <span class='lexicon_entry_list_item_explanation'>$explanation</span>";
        $str .= "</span>";
      }
    }

    return($str);
  }

  function getLexiconTooltipsFromContent($content) {
    $tooltips = array();

    $ids = $this->getIdsFromContent($content);

    if (count($ids) > 0) {
      foreach ($ids as $couple) {
        list($lexiconEntryId, $lexiconEntryDomId) = $couple;
        if ($lexiconEntry = $this->selectById($lexiconEntryId)) {
          $name = $lexiconEntry->getName();
          $explanation = $lexiconEntry->getExplanation();
          $image = $lexiconEntry->getImage();
          array_push($tooltips, array($lexiconEntryId, $name, $explanation, $image));
        }
      }
    }

    return($tooltips);
  }

  function renderLexiconJsLibrary() {
    global $gJsUrl;
    global $gLexiconUrl;
    global $gHomeUrl;
    global $REQUEST_URI;

    $str = '';

    $isLoggedIn = $this->userUtils->isLoggedIn();

    if (!$this->userLoginIsRequired() || $isLoggedIn) {
      $str .= <<<HEREDOC
<script type='text/javascript' src='$gJsUrl/jquery/wtooltip.min.js'></script>
HEREDOC;

      $tooltipClassName = LEXICON_ENTRY_CLASS_NAME;

      $str .= <<<HEREDOC
<script type='text/javascript'>
$(document).ready(function() {

  $(".$tooltipClassName").wTooltip({
    follow: false,
    auto: false,
    fadeIn: 300,
    fadeOut: 500,
    delay: 200,
    timeout: 1200,
    content: true, 
    callBefore: function(tooltip, node, settings) {
      $.ajax({
        url: "$gLexiconUrl/get_lexicon_entry.php?lexiconEntryId="+$(node).attr("lexicon_entry_id"),
        async: false,
        context: document.body,
        success: function(data){
          $(tooltip).html(data); 
          $(tooltip).fadeIn();
          setTimeout(function() {
            $(tooltip).fadeOut();
          }, 10000);
        }
      });

      // Warn about any missing entry from the lexicon
      var retrievedContent = $(tooltip).html();
      if (retrievedContent.length == 0) {
        var pageUrl = encodeURIComponent("$gHomeUrl$REQUEST_URI");
        var url = '$gLexiconUrl/missing_lexicon_entry.php?lexiconEntryId="+$(node).attr("lexicon_entry_id")+"&pageUrl='+pageUrl;
        ajaxAsynchronousRequest(url, doNothing);
      }
    },
    style: {
      width: "500px", // Required to avoid the tooltip being displayed off the right
      background: "#ffffff",
      color: "#000"
    }
  });

  function doNothing() {
  }

});
</script>
HEREDOC;
    }

    return($str);
  }

  // Get the lexicon entry ids from the content
  function getIdsFromContent($content) {
    $ids = array();

    $pattern = "/(".LEXICON_ENTRY_DOM_ID_PREFIX.")([0-9]*)(_)([a-z,A-Z,0-9]*)/";
    if (strstr($content, LEXICON_ENTRY_DOM_ID_PREFIX) && preg_match_all($pattern, $content, $matches)) {
      $lexiconEntryId = $matches[2];
      $randoms = $matches[4];
      $domIds = array();
      for ($i = 0; $i < count($lexiconEntryId); $i++) {
        $lexiconEntryId[$i] = str_replace(LEXICON_ENTRY_DOM_ID_PREFIX, '', $lexiconEntryId[$i]);
        $domIds[$i] = LEXICON_ENTRY_DOM_ID_PREFIX . $lexiconEntryId[$i] . '_' . $randoms[$i];
        $couple = array($lexiconEntryId[$i], $domIds[$i]);
        array_push($ids, $couple);
      }
    }

    return($ids);
  }

  // Render the id of the DOM element to display a lexicon entry
  // Avoid DOM elements with equal ids for identical tooltips on the same page
  function renderDomId($lexiconEntryId) {
    $tooltipDomId = LEXICON_ENTRY_DOM_ID_PREFIX . $lexiconEntryId . '_' . LibUtils::generateUniqueId(3);

    return($tooltipDomId);
  }

  // Replace the id of the DOM element for a lexicon entry, by another id
  function replaceDomId($lexiconEntryId, $lastInsertLexiconEntryId, $content) {
    $content = str_replace(LEXICON_ENTRY_DOM_ID_PREFIX . $lexiconEntryId . '_', LEXICON_ENTRY_DOM_ID_PREFIX . $lastInsertLexiconEntryId . '_', $content);

    return($content);
  }

  // Get the list of definitions for a word or expression
  function getEntryDefinitions($name) {
    $url = LEXICON_WEBSERVICE_URL . '/' . $name;

    $content = LibFile::curlGetFileContent($url);

    $pattern = "/(<span class=\"tlf_cdefinition\">)(.*?)(<\/span>)/";

    if (preg_match_all($pattern, $content, $matches)) {
      $definitions = $matches[2];

      return($definitions);
    }
  }

  // Render the header for the lexicon search
  function renderHeader() {
    global $gJsUrl;
    global $gLexiconUrl;

    $str = <<<HEREDOC
<script type='text/javascript' src='$gJsUrl/jquery/jquery-autocomplete/lib/jquery.bgiframe.min.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/jquery-autocomplete/lib/jquery.ajaxQueue.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/jquery-autocomplete/lib/thickbox-compressed.js'></script>
<script type='text/javascript' src='$gJsUrl/jquery/jquery-autocomplete/jquery.autocomplete.js'></script>
<link rel='stylesheet' type='text/css' href='$gJsUrl/jquery/jquery-autocomplete/jquery.autocomplete.css' />
<link rel='stylesheet' type='text/css' href='$gJsUrl/jquery/jquery-autocomplete/lib/thickbox.css' />
HEREDOC;

    $str = <<<HEREDOC
<script type="text/javascript">
function lexiconSearch(searchText, lexiconSearchId) {
  var url = "$gLexiconUrl/search_suggest.php?searchText="+searchText+"&lexiconSearchId="+lexiconSearchId;
  ajaxAsynchronousRequest(url, lexiconSearchUpdate);
}
function lexiconSearchUpdate(responseText) {
  var response = eval('(' + responseText + ')');
  var lexiconSearchId = response.lexiconSearchId;
  var content = response.content;
/*
  var entries = response.entries;
  if (entries.length > 0) {
    for (var i in entries) {
      var lexiconEntryId = entries[i].lexiconEntryId;
      var name = entries[i].name;
      var explanation = entries[i].explanation;
    }
  }
*/
  $('#lexicon_search_'+lexiconSearchId).children('.lexicon_search_entries').children('.lexicon_entry').remove();
  $('#lexicon_search_'+lexiconSearchId).children('.lexicon_search_entries').append(content);
}
</script>
HEREDOC;

    return($str);
  }

  // Render the lexicon search
  function renderLexiconSearch() {
    global $gLexiconUrl;
    global $gJSNoStatus;

    $this->loadLanguageTexts();

    $lexiconSearchId = LibUtils::generateUniqueId();

    $str = "<div class='lexicon_search' id='lexicon_search_$lexiconSearchId'>";

    $str .= "<div class='lexicon_search_title'>" . $this->websiteText[4] . "</div>";

    $str .= "<div class='lexicon_search_field'>"
      . "<input class='lexicon_search_input' type='text' id='lexicon_search_text_$lexiconSearchId' name='lexicon_search_text_$lexiconSearchId' value='' size='10' />"
      . "</div>";

    $str .= "<div class='lexicon_search_entries'>"
      . "</div>";

    $str .= "</div>";

    $str .= <<<HEREDOC
<script type="text/javascript">
$(function(){
  $('#lexicon_search_text_$lexiconSearchId').bind("keyup onload", function (event) {
    // Update only after a return key or a blank space
    if (event.which == 13 || event.which == 32 || event.which == 188 || event.which == 190) {
      lexiconSearch(this.value, '$lexiconSearchId');
    }
  });
});
</script>
HEREDOC;

    return($str);
  }

}

?>
