<?

class PreferenceUtils extends PreferenceDB {

  var $mlText;
  var $websiteText;

  var $preferences;

  var $parentMenuUrl;

  var $languageUtils;

  function PreferenceUtils() {
    $this->PreferenceDB();
  }

  function init($preferences, $parentMenuUrl = '') {
    $this->preferences = $preferences;

    $this->parentMenuUrl = $parentMenuUrl;
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  // Create the preferences
  function createPreferences() {
    if (is_array($this->preferences) && count($this->preferences) > 0) {
      foreach ($this->preferences as $name => $preferenceData) {
        $defaultValue = $this->getDefaultValue($name);
        $defaultValue = LibString::escapeQuotes($defaultValue);

        // Create the preferences that do not yet exist
        if (!$this->isMlText($name)) {
          if (!$preference = $this->selectByName($name)) {
            $preference = new Preference();
            $preference->setName($name);
            $preference->setValue($defaultValue);
            $preference->setType($this->getTypeCode($name));
            $this->insert($preference);
          }
        } else {
          if (!$preference = $this->selectByName($name)) {
            $preference = new Preference();
            $preference->setName($name);
            $preference->setType($this->getTypeCode($name));
            $this->insert($preference);
          }
        }
      }
    }
  }

  function getTypeCode($name) {
    $typeCode = '';

    if ($this->isBoolean($name)) {
      $typeCode = PREFERENCE_TYPE_BOOLEAN;
    } else if ($this->isText($name)) {
      $typeCode = PREFERENCE_TYPE_TEXT;
    } else if ($this->isMlText($name)) {
      $typeCode = PREFERENCE_TYPE_MLTEXT;
    } else if ($this->isTextarea($name)) {
      $typeCode = PREFERENCE_TYPE_TEXTAREA;
    } else if ($this->isRawContent($name)) {
      $typeCode = PREFERENCE_TYPE_RAW_CONTENT;
    } else if ($this->isSelect($name)) {
      $typeCode = PREFERENCE_TYPE_SELECT;
    } else if ($this->isRange($name)) {
      $typeCode = PREFERENCE_TYPE_RANGE;
    } else if ($this->isColor($name)) {
      $typeCode = PREFERENCE_TYPE_COLOR;
    } else if ($this->isUrl($name)) {
      $typeCode = PREFERENCE_TYPE_URL;
    }

    return($typeCode);
  }

  // Reload the text resources for a language
  function reloadLanguageTexts($languageCode) {
    $this->mlText = $this->languageUtils->getText(__FILE__, $languageCode);
  }

  function reset($preferenceId) {
    if ($preference = $this->selectById($preferenceId)) {
      $name = $preference->getName();
      $values = $preference->getValue();
      $defaultValue = $this->getDefaultValue($name);
      $languageCode = $this->languageUtils->getCurrentAdminLanguageCode();
      $values = $this->languageUtils->setTextForLanguage($values, $languageCode, $defaultValue);
      $preference->setValue($values);
      $this->update($preference);
      return($defaultValue);
    }
  }

  // Return the url to the reset page for the module
  function getResetUrl($url) {
    $resetUrl = str_replace('preference.php', 'reset_preference.php', $url);

    return($resetUrl);
  }

  // Get the default value of a preference for a language
  function getLanguageDefaultValue($name, $language) {
    $defaultValue = '';

    $mlText = $this->languageUtils->getLanguageText(__FILE__, $language);

    return($defaultValue);
  }

  // Get the default value of a preference
  function getDefaultValue($name) {
    $defaultValue = '';

    $preference = $this->getPreference($name);
    if ($this->isText($name) || $this->isRawContent($name) || $this->isTextarea($name) || $this->isMlText($name)) {
      $defaultValue = $preference["defaultValue"];
    } else if ($this->isSelect($name)) {
      if (is_array($preference["defaultValue"])) {
        $keys = array_keys($preference["defaultValue"]);
        $defaultValue = $keys[0];
      }
    } else if ($this->isSelect($name) || $this->isRange($name)) {
      $defaultValue = $this->getRangeDefault($name);
    } else if ($this->isBoolean($name)) {
      $defaultValue = $preference["defaultValue"];
    }

    return($defaultValue);
  }

  // Get the value of a preference
  function getValue($name) {
    $value = '';

    // If the pair exists then return its value
    if ($name) {
      if ($preference = $this->selectByName($name)) {
        if ($preference->getType() == PREFERENCE_TYPE_MLTEXT) {
          $values = $preference->getValue();
          $languageCode = $this->languageUtils->getCurrentLanguageCode();
          $value = $this->languageUtils->getTextForLanguage($values, $languageCode);
          // If none is found then try to get one for the default language
          if (!$value) {
            $languageCode = $this->languageUtils->getDefaultLanguageCode();
            $value = $this->languageUtils->getTextForLanguage($values, $languageCode);
            // If none is found then get one for no specific language
            if (!$value) {
              $value = $this->languageUtils->getTextForLanguage($values, '');
            }
          }
        } else if ($preference->getType() == PREFERENCE_TYPE_RAW_CONTENT) {
          $value = $preference->getValue();
        } else {
          $value = $preference->getValue();
          $value = nl2br($value);
        }
      }
    }

    return($value);
  }

  // Set the value of a preference
  function setValue($name, $value) {
    // If the pair exists then set its value
    if ($name) {
      if ($preference = $this->selectByName($name)) {
        $preference->setValue($value);
        $this->update($preference);
      }
    }
  }

  // Check if the type of a preference is a boolean value
  function isBoolean($name) {
    if ($this->getType($name) == PREFERENCE_TYPE_BOOLEAN) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if the type of a preference is a character string
  function isText($name) {
    if ($this->getType($name) == PREFERENCE_TYPE_TEXT) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if the type of a preference is a block of raw content 
  // that should not be formatted as it might not be text
  function isRawContent($name) {
    if ($this->getType($name) == PREFERENCE_TYPE_RAW_CONTENT) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if the type of a preference is a block of text
  function isTextarea($name) {
    if ($this->getType($name) == PREFERENCE_TYPE_TEXTAREA) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if the type of a preference is a multi language content
  function isMlText($name) {
    if ($this->getType($name) == PREFERENCE_TYPE_MLTEXT) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if the type of a preference is a selection in a list
  function isSelect($name) {
    if ($this->getType($name) == PREFERENCE_TYPE_SELECT) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if the type of a preference is a range of numbers
  function isRange($name) {
    if ($this->getType($name) == PREFERENCE_TYPE_RANGE) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if the type of a preference is a color
  function isColor($name) {
    if ($this->getType($name) == PREFERENCE_TYPE_COLOR) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if the type of a preference is a url
  function isUrl($name) {
    if ($this->getType($name) == PREFERENCE_TYPE_URL) {
      return(true);
    } else {
      return(false);
    }
  }

  // Get the preference
  function getPreference($name) {
    $preference = array();

    $description = '';
    $help = '';
    $type = '';
    $defaultValue = '';

    if ($name && isset($this->preferences[$name])) {
      $preferenceData = $this->preferences[$name];
      if (count($preferenceData) == 4) {
        list($description, $help, $type, $defaultValue) = $preferenceData;
      } else if (count($preferenceData) == 3) {
        list($description, $help, $type) = $preferenceData;
        $defaultValue = '';
      }
    }

    $preference = array(
      "name" => "$name",
      "description" => "$description",
      "help" => "$help",
      "type" => "$type",
      "defaultValue" => $defaultValue,
    );

    return($preference);
  }

  // Get the type of a preference
  function getType($name) {
    $preference = $this->getPreference($name);
    if (isset($preference["type"])) {
      $type = $preference["type"];
    } else {
      $type = '';
    }

    return($type);
  }

  // Get the description of a preference
  function getDescription($name) {
    $preference = $this->getPreference($name);
    $description = $preference["description"];

    return($description);
  }

  // Get the help text of a preference
  function getHelp($name) {
    $preference = $this->getPreference($name);
    $help = $preference["help"];

    return($help);
  }

  // Get the select default values of a preference
  function getSelectOptions($name) {
    $preference = $this->getPreference($name);
    $defaultValue = $preference["defaultValue"];

    return($defaultValue);
  }

  // Get the select range of a preference
  // A range is defined of a min value and a max value and possibly a default value
  function getRange($name) {
    $selectRange = array();

    $preference = $this->getPreference($name);
    $defaultValue = $preference["defaultValue"];

    if (count($defaultValue) > 1) {
      $mini = $defaultValue[0];
      $maxi = $defaultValue[1];
      if (is_numeric($mini) && is_numeric($maxi)) {
        for ($i = $mini; $i <= $maxi; $i++) {
          $selectRange[$i] = $i;
        }
      }
    }

    return($selectRange);
  }

  // Get the default value, if any, of the select range of a preference
  function getRangeDefault($name) {
    $default = '';

    $preference = $this->getPreference($name);
    $defaultValue = $preference["defaultValue"];

    if (count($defaultValue) == 3) {
      $default = $defaultValue[2];

      return($default);
    }
  }

  // Select the module preferences
  function selectPreferences() {
    $this->loadLanguageTexts();

    $this->createPreferences();

    $this->syncPreferences();

    $preferences = array();
    foreach ($this->preferences as $name => $preferenceData) {
      if ($preference = $this->selectByName($name)) {
        array_push($preferences, $preference);
      }
    }

    return($preferences);
  }

  // Sync the module preferences constants with the database table records
  function syncPreferences() {
    foreach ($this->preferences as $name => $preferenceData) {
      if (!$preference = $this->selectByName($name)) {
        $preference = new Preference();
        $preference->setName($name);
        $preference->setType($this->getTypeCode($name));
        $this->insert($preference);
      }
    }
  }

}

?>
