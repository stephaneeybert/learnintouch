<?php

class LibString {

  // Get the character encoding of the string
  static function getCharacterEncoding($str) {
    $rx = "/<?xml.*encoding=['\"](.*?)['\"].*?>/m";

    if (preg_match($rx, $str, $m)) {
      $encoding = strtoupper($m[1]);
    } else {
      $encoding = "UTF-8";
    }

    return($encoding);
  }

  // Replace a string only once
  static function replaceOnce($search, $replace, $content) {
    $pos = strpos($content, $search);
    if ($pos === false) {
      return($content);
    } else {
      return(substr($content, 0, $pos) . $replace . substr($content, $pos + strlen($search)));
    }
  }

  // Get all the urls of a string
  // This includes src, href and url.
  static function getAllUrls($string, $type = '') {
    $matchList = array();

    if ($type) {
      $types = array($type);
    } else {
      $types = array("href", "src", "url");
    }

    foreach ($types as $type) {
      preg_match_all("|$type\=\"?'?`?([[:alnum:]:?=&@/._-]+)\"?'?`?|i", $string, $matches);

      $matchList[$type] = $matches[1];
    }

    return($matchList);
  }

  // Get the most similar word
  static function getMostSimilarWord($input, $words) {

    // Check for an exact match
    if (in_array($input, $words)) {
      return(false);
    }

    // Nothing close
    $closest = false;

    // No distance to begin with
    $shortest = -1;

    // Find the most similar word
    foreach ($words as $word) {

      // Get the distance between the words
      $distance = levenshtein($input, $word);

      // Check for an exact match
      if ($distance == 0) {
        $closest = $word;
        $shortest = 0;
        break;
      }

      // Check if the distance is less than the next distance
      // or if the next closest word has not yet been found
      if ($distance <= $shortest || $shortest < 0) {
        $closest  = $word;
        $shortest = $distance;
      }
    }

    // Check for an exact match
    if ($shortest == 0) {
      return(false);
    } else {
      return($closest);
    }

  }

  // Test if the string is empty
  // The PHP empty() function is not to be used
  // because it returns true if the string contains the value 0;
  static function isEmpty($str) {
    if (!$str && $str != '0') {
      return(true);
    } else {
      return(false);
    }
  }

  // Return the NULL string if the value is empty
  static function emptyToNULL($str) {
    return((!$str) ? 'NULL' : $str);
  }

  // Enclose with single quotes if the value is NULL
  static function addSingleQuotesIfNotNULL($str) {
    return(($str == 'NULL') ? $str : "'$str'");
  }

  // Count the number of words, that is, excluding punctuation
  static function countNbRealWords($str) {
    $nb = 0;

    $str = str_replace(',', ' ', $str);
    $str = str_replace(';', ' ', $str);
    $str = str_replace('.', ' ', $str);

    $str = LibString::stripMultipleSpaces($str);

    $bits = explode(' ', $str);

    for ($i = 0; $i < count($bits); $i++) {
      if (preg_match("/[0-9A-Za-zÀ-ÖØ-öø-ÿ]/", $bits[$i])) {
        $nb++;
        }
      }

    return($nb);
  }

  // Get a string containing the first words of a string
  static function wordSubtract($str, $max) {
    // Get the words
    $words = explode(' ', $str);

    // Check the maximun number of words
    if ($max > count($words)) {
      return(ltrim($str));
    }

    // Compose the new string
    $strWords = '';
    for ($i = 0; $i < $max; $i++) {
      $word = $words[$i];
      $strWords .= " $word";
    }

    return(ltrim($strWords));
  }

  // Test if the string is a name (alphanumeric and no fancy characters)
  static function isAlphaNum($str) {
    if (preg_match('/^[a-z0-9]*$/i', $str)) {
      return(true);
    } else {
      return(false);
    }
  }

  // Clean a string that is allowed html tags
  static function cleanHtmlString($str) {
    // A wysiwyg editor may silently insert markup on an empty content
    // Some editors add some html markup by default, meaning an empty string can
    // end up containing some html markup like a <br/> for example
    if (trim($str) == '<br />') {
      $str = '';
    }

    return($str);
  }

  // Clean a string by removing its opening and trailling blank spaces
  // and translating its html tags and quotes
  // Note the option ENT_QUOTES translates both the single and double quotes
  static function cleanString($str) {
    $str = htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
    $str = preg_replace('/&amp;#(x[a-f0-9]+|[0-9]+);/i', '&#$1;', $str);

    return($str);
  }

  static function decodeHtmlspecialchars($string, $style = ENT_QUOTES) {
    $string = html_entity_decode($string, $style);

    if ($style === ENT_QUOTES) {
      $translation['&#146;'] = '\'';
      $translation['&amp;'] = '&';
      $translation['&quot;'] = '"';
      $translation['&#039;'] = '\'';
      $translation['&rsquo;'] = '\'';
      $translation['&apos;'] = '\'';
      $translation['&lt;'] = '<';
      $translation['&gt;'] = '>';
    }

    $string = strtr($string, $translation);

    return($string);
  }

  // Remove all non alpha numerical characters from a string
  static function stringToAlphanum($str) {
    return(preg_replace("/[^[:alnum:]+]/i", '', $str));
  }

  // Get the domain name of the url
  static function getDomainName($url) {
    return parse_url($url, PHP_URL_HOST);
  }

  // Strip the trailling slash
  static function stripTraillingSlash($str) {
    $str = preg_replace("!/$!iU", '', $str);
    return($str);
  }

  // Add a trailling slash if none
  static function addTraillingSlash($str) {
    if (substr($str, -1) != "/") {
      $str .= "/";
    }

    return($str);
  }

  // Add a trailling character if none
  static function addTraillingChar($str, $char) {
    if (substr($str, -1) != $char) {
      $str .= $char;
    }

    return($str);
  }

  // Strip all script tags, like PHP tags and Javascript tags
  static function stripJavascriptTags($str) {
    $str = preg_replace("<script[^>]*>.*</script>", '', $str);

    return($str);
  }

/*
  // Strip all script tags, like PHP tags and Javascript tags
  static function stripScriptTags($str) {
  $str = preg_replace("<\?php.*\?>", '', $str);
  $str = preg_replace("<script[^>]*>.*</script>", '', $str);
  return($str);
  }
*/

  // Strip all HTML and PHP tags from a string
  // Also remove the html encoded characters like &amp;
  static function stripTags($str, $allowed_tags = '') {
    $str = strip_tags($str, $allowed_tags);
    $str = preg_replace('/&\w;/', ' ', $str);

    return($str);
  }

  // Normalize lines breaks
  static function normalizeLinebreaks($str) {
    $str = str_replace("\r\n", "\n", $str); /* win -> un*x */
    $str = str_replace("\r", "\n", $str); /* mac -> un*x */

    return($str);
  }

  // Strip html line breaks
  static function stripBR($str) {
    return(preg_replace('!<br.*>!iU', '', $str));
  }

  // Json requires escape line breaks
  static function jsonEscapeLinebreak($str) {
    // Clean up any funky line breaks
    $str = str_replace("\r\n", "\n", $str);
    $str = str_replace("\r", "\n", $str);

    // JSON requires new line characters to be escaped
    $str = str_replace("\n", "\\n", $str);

    return($str);
  }

  // Check if the string contains html line breaks
  static function containsHtmlLineBreak($str) {
    if (strstr($str, "<br>") || strstr($str, "<br />") || strstr($str, "<p>")) {
      return(true);
    } else {
      return(false);
    }
  }

  // Convert html line breaks into ascii line breaks
  static function br2nl($str) {
    return(preg_replace('!<br.*>!iU', "\n", $str));
  }

  // Convert html paragraphs into ascii line breaks
  static function p2nl($str, $closing = false) {
    $str = preg_replace('!<p.*>!iU', "\n", $str);

    if ($closing) {
      $str = preg_replace('!</p.*>!iU', "\n", $str);
    }

    return(preg_replace('!<p.*>!iU', "\n", $str));
  }

  // Strip all characters that are not to be used in a text string
  static function stripNonTextChar($str) {
    return(preg_replace('/^[a-zA-Z0-9][[:punct:]][\s]/iU', '', $str));
  }

  // Strip all non alpha numerical and non dot and non underscore characters from the string
  static function stripNonFilenameChar($str) {
    return(preg_replace('/[[:^print:]]/iU', '', $str));
  }

  /*
  // Strip the slash before the quotes only if magic quotes are used
  // If magic quotes are enabled on the php configuration server then single and double
  // quotes as well as escape characters, are escaped
  // The magic quotes feature SHOULD be disabled!!
  // The name of the feature is misleading, it should read "automatic string escaping"
  // This configuration feature cannot be changed at run time
  // Do not misunderstand the use of the set_magic_quotes_runtime() function,
  // as it does NOT affect the magic quotes configuration
  static function stripSlashMagicQuotes($str) {
  if (get_magic_quotes_gpc() == true) {
  $str = stripslashes($str);
  }

  return($str);
  }
   */

  // Insert an escape character before all single quotes, double quotes and escape characters
  // By using this function before all and any database insert and update, sql injections can
  // be avoided
  // Also, the magic quotes feature of php shall not be turned on
  static function databaseEscapeQuotes($str) {
    $str = str_replace('\\', '\\\\', $str);
    $str = str_replace("'", "\'", $str);
    $str = str_replace('"', '\"', $str);

    return($str);
  }

  // Insert an escape character before all single quotes in the string
  static function escapeQuotes($str) {
    $str = str_replace("'", "\\'", $str);

    return($str);
  }

  // Insert an escape character before all double quotes in the string
  static function escapeDoubleQuotes($str) {
    $str = str_replace('"', '\\"', $str);

    return($str);
  }

  // Remove all single quotes from the string
  static function stripQuotes($str) {
    return(str_replace("'", '', $str));
  }

  // Translate the single and double quotes of a string into html entities
  static function toHtmlQuotes($str) {
    $str = str_replace("'", "&#039;", $str);
    $str = str_replace("\"", "&quot;", $str);

    return($str);
  }

  // Remove all html quotes from the string
  static function stripHtmlQuotes($str) {
    return(str_replace("&#039;", '', $str));
    return(str_replace("&quot;", '', $str));
  }

  // Strip all non-numbers from the string
  static function stripNonNumbers($str) {
    return(preg_replace('/[^[:digit:]]/iU','', $str));
  }

  // Format an amount
  static function formatAmount($str) {
    $str = preg_replace('/[^[0-9],.]/iU', '', $str);
    $str = str_replace(',', '.', $str);
    return($str);
  }

  // Remove all white spaces from the string
  static function stripSpaces($str) {
    return(str_replace(" ", '', $str));
  }

  // Strip all extra white spaces
  // Replace multiple consecutive white spaces by one white space
  static function stripMultipleSpaces($str) {
    return(preg_replace('/\s+/', ' ', $str));
  }

  // Remove all occurences of a string from the string
  static function strip($pattern, $str) {
    return(str_replace($pattern, '', $str));
  }

  // Remove all back slashes from the string
  static function stripBSlashes($str) {
    return(str_replace("\\", '', $str));
  }

  // Remove all carriage returns from the string
  static function stripHtmlLineBreaks($str, $replace = '') {
    return(preg_replace('!<br.*>!iU', '', $str));
  }

  // Remove all carriage returns from the string
  static function stripLineBreaks($str, $replace = '') {
    return(preg_replace("/(\r\n|\n|\r)/iU", $replace, $str));
  }

  // Replace line breaks by blank spaces
  static function lineBreakToSpace($str) {
    return(preg_replace("/(\r\n|\n|\r)/iU", ' ', $str));
  }

  // Trim white spaces inclusive inner ones
  static function trim($str, $innerSpaces = 1) {
    $trimmed = '';

    $words = explode(' ', $str);
    foreach ($words as $word) {
      if ($word) {
        for ($i = 0; $i < $innerSpaces; $i++) {
          $trimmed .= ' ';
        }
        $trimmed .= trim($word);
      }
    }

    $trimmed = trim($trimmed);

    return($trimmed);
  }

  static function cleanupPhoneNumber($number) {
    $number = str_replace('+', '00', $number);
    $number = preg_replace('/[^[:digit:]]/iU','', $number);

    return($number);
  }

  // Pretty print some JSON
  function jsonPrettyPrint($json, $htmlOutput = false) {
    $spacer = ' ';
    $level = 1;
    $indent = 0;
    $prettyJson = '';
    $inString = false;

    $len = strlen($json);

    for ($c = 0; $c < $len; $c++) {
      $char = $json[$c];
      switch ($char) {
      case '{':
      case '[':
        if (!$inString) {
          $indent += $level;
          $prettyJson .= $char . "\n" . str_repeat($spacer, $indent);
        } else {
          $prettyJson .= $char;
        }
        break;
      case '}':
      case ']':
        if (!$inString) {
          $indent -= $level;
          $prettyJson .= "\n" . str_repeat($spacer, $indent) . $char;
        } else {
          $prettyJson .= $char;
        }
        break;
      case ',':
        if (!$inString) {
          $prettyJson .= ",\n" . str_repeat($spacer, $indent);
        } else {
          $prettyJson .= $char;
        }
        break;
      case ':':
        if (!$inString) {
          $prettyJson .= ": ";
        } else {
          $prettyJson .= $char;
        }
        break;
      case '"':
        if ($c > 0 && $json[$c-1] != '\\') {
          $inString = !$inString;
        }
      default:
        $prettyJson .= $char;
        break;
      }
    }

    return ($htmlOutput) ?
      '<pre>' . htmlentities($prettyJson) . '</pre>' :
      $prettyJson . "\n";
  }

}

?>
