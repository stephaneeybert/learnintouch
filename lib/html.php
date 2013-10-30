<?php

class LibHtml {

  // Redirect the browser to another url
  static function urlRedirect($url, $seconds = 0) {
    sleep($seconds);

    header('Location: ' . $url) ;
  }

  // Create an html tag to redirect the browser to another url
  static function urlDisplayRedirect($url, $seconds = 0) {
    $str = "\n<script type='text/javascript'>"
      . "function redirect() { "
      . "  window.location = '". $url . "'; "
      . "}"
      . "var timerID = setTimeout('redirect()'," . ($seconds * 1000) . "); "
      . "</script> ";

    return($str);
  }

  // Redirect the parent window
  static function urlDisplayRedirectParentWindow($url, $seconds = 0) {
    $str = "\n<script type='text/javascript'>"
      . "function redirect() { "
      . "  window.opener.location = '". $url . "'; "
      . "}"
      . "var timerID = setTimeout('redirect()'," . ($seconds * 1000) . "); "
      . "</script> ";


    return($str);
  }

  // Convert special HTML entities back to characters
  static function htmlspecialchars_decode($str) {
    return strtr($str, array_flip(get_html_translation_table(HTML_SPECIALCHARS)));
  }

  // Get a radio list
  static function getRadioList($id, $list, $current, $vertical, $handler) {
    if (!is_array($list)) {
      return;
    }

    $str = '';

    foreach ($list as $key => $value) {
      if (strlen($current) > 0 && $current == $key) {
        $checked = 'checked';
      } else {
        $checked = '';
      }
      $item = "<span><input class='system_input' style='border:none 0px; vertical-align: middle;' type='radio' name='$id' id='$key' $checked value='$key' onclick=\"$handler\"><span onclick=\"clickAdjacentInputElement(this); $handler\"> $value</span></span>";

      if ($vertical) {
        if ($str) {
          $item = '<br/>' . $item;
        }
      } else {
        $item = ' (' . $item . ')';
      }

      $str .= $item;
    }

    return($str);
  }

  // Get a select list
  static function getSelectList($id, $list, $current = '', $autoSubmit = false, $size = 1, $onChange = '') {
    if (!is_array($list)) {
      return;
    }

    if ($autoSubmit === true) {
      $strOnChangeHandler = "onchange='this.form.submit();'";
    } else if ($onChange) {
      $strOnChangeHandler = "onchange=\"$onChange\"";
    } else {
      $strOnChangeHandler = '';
    }

    $str = "<select class='system_input' size='$size' name='$id' id='$id' $strOnChangeHandler>";
    foreach ($list as $key => $value) {
      if (strlen($current) > 0 && $current == $key) {
        $selected = "selected='selected'";
      } else {
        $selected = '';
      }
      $str .= " <option " . $selected . " value='$key'>$value</option>";
    }
    $str .= "</select>";
    return($str);
  }

  // Get a multi value select list
  static function getMultiSelectList($id, $list, $size, $current = '', $onChange = '') {
    if (!is_array($list)) {
      return;
    }

    if ($onChange) {
      $strOnChangeHandler = "onchange='$onChange'";
    } else {
      $strOnChangeHandler = '';
    }

    $str = "<select class='system_input' multiple size='$size' name='" . $id . "[]' id='" . $id . "[]' $strOnChangeHandler>";

    foreach ($list as $key => $value) {
      if (!is_array($current)) {
        $current = array($current);
      }
      foreach ($current as $current_key) {
        if ($current_key && $current_key == $key) {
          $selected = "selected='selected'";
        } else {
          $selected = '';
        }
      }

      $str .= " <option " . $selected . " value='$key'>$value</option>";
    }

    $str .= "</select>";

    return($str);
  }

  // Create a range of numerical select options
  static function getSelectRange($mini, $maxi) {
    $range = array();

    if (is_numeric($mini) && is_numeric($maxi) && ($maxi > $mini)) {
      for ($i = $mini; $i <= $maxi; $i++) {
        $range[$i] = $i;
      }
    }

    return($range);
  }

  // Get a list of checkbox buttons
  static function NOT_USED_getMultiCheckboxList($id, $list, $currents = '', $onClick = '') {
    if (!is_array($list)) {
      return;
    }

    if ($onClick) {
      $strOnClickHandler = "onclick='$onClick'";
    } else {
      $strOnClickHandler = '';
    }

    $str = '';
    foreach ($list as $key => $value) {
      if ($currents && is_array($currents) && array_key_exists($key, $currents) && $currents[$key] > 0) {
        $checked = 'checked';
      } else {
        $checked = '';
      }
      $str .= " ($value <input class='system_input' style='vertical-align: middle;' type='checkbox' name='$key' id='$key' " . $checked . " value='1' $strOnClickHandler>)";
    }

    return($str);
  }

  // Prevent caching from the browser
  static function preventCaching() {
    // Outdate the browser cache
    header("Content-type: text/html; charset=ISO-8859-1");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
    header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache"); // HTTP/1.0
  }

}

?>
