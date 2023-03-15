<?php

class TemplatePropertySetUtils extends TemplatePropertySetDB {

  var $mlText;

  // The property types
  var $propertyTypes;

  // The property types currently being edited
  var $currentPropertyTypes;

  // The id of the current model
  var $currentTemplateModelId;

  // The id of the current set
  var $currentTemplatePropertySetId;

  // The DOM id of the current set
  var $currentTagID;

  var $languageUtils;
  var $templatePropertyUtils;

  function __construct() {
    parent::__construct();
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  // The css property types
  function loadPropertyTypes() {
    $this->loadLanguageTexts();

    $this->propertyTypes = array(
      'ALIGNMENT' =>
      array('text-align', $this->mlText[1], $this->mlText[2], TEMPLATE_PROPERTY_TYPE_SELECT, array('' => '', "left" => $this->mlText[3], "center" => $this->mlText[4], "right" => $this->mlText[5], "justify" => $this->mlText[165])),
        'VERTICAL_ALIGNMENT' =>
        array('vertical-align', $this->mlText[60], $this->mlText[61], TEMPLATE_PROPERTY_TYPE_SELECT, array('' => '', "top" => $this->mlText[50], "middle" => $this->mlText[49], "bottom" => $this->mlText[48])),
          'BACKGROUND_COLOR' =>
          array('background-color', $this->mlText[6], $this->mlText[7], TEMPLATE_PROPERTY_TYPE_COLOR),
            'BACKGROUND_IMAGE' =>
            array('background-image', $this->mlText[8], $this->mlText[9], TEMPLATE_PROPERTY_TYPE_IMAGE),
              'BACKGROUND_REPEAT' =>
              array('background-repeat', $this->mlText[99], $this->mlText[100], TEMPLATE_PROPERTY_TYPE_SELECT, array('' => '', "repeat" => $this->mlText[102], "repeat-x" => $this->mlText[103], "repeat-y" => $this->mlText[104], "no-repeat" => $this->mlText[101])),
                'BACKGROUND_POSITION' =>
                array('background-position', $this->mlText[105], $this->mlText[106], TEMPLATE_PROPERTY_TYPE_SELECT, array('' => '', "top left" => $this->mlText[107], "top center" => $this->mlText[108], "top right" => $this->mlText[109], "center left" => $this->mlText[110], "center center" => $this->mlText[111], "center right" => $this->mlText[112], "bottom left" => $this->mlText[113], "bottom center" => $this->mlText[114], "bottom right" => $this->mlText[115])),
                  'BACKGROUND_ATTACHMENT' =>
                  array('background-attachment', $this->mlText[10], $this->mlText[11], TEMPLATE_PROPERTY_TYPE_SELECT, array('' => '', "fixed" => $this->mlText[97], "scroll" => $this->mlText[98])),
                    'BORDER_STYLE' =>
                    array('border-style', $this->mlText[12], $this->mlText[13], TEMPLATE_PROPERTY_TYPE_SELECT, array('' => '', "none" => $this->mlText[46], "solid" => $this->mlText[70], "dotted" => $this->mlText[71], "dashed" => $this->mlText[72], "double" => $this->mlText[92], "groove" => $this->mlText[93], "ridge" => $this->mlText[94], "inset" => $this->mlText[95], "outset" => $this->mlText[96])),
                      'FLOAT' =>
                      array('float', $this->mlText[62], $this->mlText[65], TEMPLATE_PROPERTY_TYPE_SELECT, array('' => '', "none" => $this->mlText[46], "left" => $this->mlText[3], "right" => $this->mlText[5], "inherit" => $this->mlText[59])),
                        'CLEAR' =>
                        array('clear', $this->mlText[66], $this->mlText[68], TEMPLATE_PROPERTY_TYPE_SELECT, array('' => '', "none" => $this->mlText[46], "left" => $this->mlText[3], "right" => $this->mlText[5], "inherit" => $this->mlText[59], "both" => $this->mlText[67])),
                          'BORDER_POSITION' =>
                          array('border-position', $this->mlText[130], $this->mlText[131], TEMPLATE_PROPERTY_TYPE_SELECT, array('' => '', "top left bottom right" => $this->mlText[132], "top left bottom" => $this->mlText[161], "left bottom right" => $this->mlText[162], "bottom right top" => $this->mlText[163], "right top left" => $this->mlText[164], "top bottom" => $this->mlText[133], "left right" => $this->mlText[134], "top right" => $this->mlText[135], "right bottom" => $this->mlText[136], "bottom left" => $this->mlText[137], "left top" => $this->mlText[138], "top" => $this->mlText[139], "right" => $this->mlText[140], "bottom" => $this->mlText[141], "left" => $this->mlText[142], "right bottom left" => $this->mlText[144], "bottom left top" => $this->mlText[149], "left top right" => $this->mlText[150], "top right bottom" => $this->mlText[143])),
                            'BORDER_COLOR' =>
                            array('border-color', $this->mlText[14], $this->mlText[15], TEMPLATE_PROPERTY_TYPE_COLOR),
                              'BORDER_SIZE' =>
                              array('border-width', $this->mlText[16], $this->mlText[17], TEMPLATE_PROPERTY_TYPE_RANGE, array(0, 50)),
                                'ROUND_CORNER' =>
                                array('', $this->mlText[0], $this->mlText[69], TEMPLATE_PROPERTY_TYPE_SELECT, array('' => '', 'none' => $this->mlText[46], "roundCorner" => $this->mlText[152], "roundCornerLeft" => $this->mlText[153], "roundCornerTop" => $this->mlText[154], "roundCornerBottom" => $this->mlText[155], "roundCornerRight" => $this->mlText[156], "roundCornerTopLeft" => $this->mlText[157], "roundCornerTopRight" => $this->mlText[158], "roundCornerBottomRight" => $this->mlText[159], "roundCornerBottomLeft" => $this->mlText[160])),
                                  'WIDTH' =>
                                  array('width', $this->mlText[18], $this->mlText[19], TEMPLATE_PROPERTY_TYPE_TEXT),
                                    'HEIGHT' =>
                                    array('height', $this->mlText[147], $this->mlText[148], TEMPLATE_PROPERTY_TYPE_TEXT),
                                      'MARGIN' =>
                                      array('margin', $this->mlText[20], $this->mlText[21], TEMPLATE_PROPERTY_TYPE_RANGE, array(0, 200)),
                                        'MARGIN_POSITION' =>
                                        array('margin-position', $this->mlText[151], $this->mlText[146], TEMPLATE_PROPERTY_TYPE_SELECT, array('' => '', "top left bottom right" => $this->mlText[132], "top left bottom" => $this->mlText[161], "left bottom right" => $this->mlText[162], "bottom right top" => $this->mlText[163], "right top left" => $this->mlText[164], "top bottom" => $this->mlText[133], "left right" => $this->mlText[134], "top right" => $this->mlText[135], "right bottom" => $this->mlText[136], "bottom left" => $this->mlText[137], "left top" => $this->mlText[138], "top" => $this->mlText[139], "right" => $this->mlText[140], "bottom" => $this->mlText[141], "left" => $this->mlText[142])),
                                          'PADDING' =>
                                          array('padding', $this->mlText[22], $this->mlText[23], TEMPLATE_PROPERTY_TYPE_RANGE, array(0, 200)),
                                            'PADDING_POSITION' =>
                                            array('padding-position', $this->mlText[145], $this->mlText[146], TEMPLATE_PROPERTY_TYPE_SELECT, array('' => '', "top left bottom right" => $this->mlText[132], "top left bottom" => $this->mlText[161], "left bottom right" => $this->mlText[162], "bottom right top" => $this->mlText[163], "right top left" => $this->mlText[164], "top bottom" => $this->mlText[133], "left right" => $this->mlText[134], "top right" => $this->mlText[135], "right bottom" => $this->mlText[136], "bottom left" => $this->mlText[137], "left top" => $this->mlText[138], "top" => $this->mlText[139], "right" => $this->mlText[140], "bottom" => $this->mlText[141], "left" => $this->mlText[142])),
                                              'TEXT_ALIGNMENT' =>
                                              array('text-align', $this->mlText[36], $this->mlText[37], TEMPLATE_PROPERTY_TYPE_SELECT, array('' => '', "left" => $this->mlText[38], "center" => $this->mlText[39], "right" => $this->mlText[40], "justify" => $this->mlText[41])),
                                                'FONT_COLOR' =>
                                                array('color', $this->mlText[24], $this->mlText[25], TEMPLATE_PROPERTY_TYPE_COLOR),
                                                  'FONT_TYPE' =>
                                                  array('font-family', $this->mlText[26], $this->mlText[27], TEMPLATE_PROPERTY_TYPE_SELECT, array('' => '', "Arial" => "Arial", "Arial Black" => "Arial Black", "Arial Narrow" => "Arial Narrow", "Helvetica" => "Helvetica", "Helvetica Light" => "Helvetica Light", "Comic Sans MS" => "Comic Sans MS", "Chunk Five Regular" => "ChunkFiveRegular", "Verdana" => "Verdana", "Courrier" => "Courrier", "Courier New" => "Courier New", "System" => "System", "Trebuchet MS" => "Trebuchet MS", "Impact" => "Impact", "Tahoma" => "Tahoma", "Lucida" => "Lucida", "Times New Roman" => "Times New Roman", "Palatino" => "Palatino", "Garamond" => "Garamond", "Bookman" => "Bookman", "Avant Garde" => "Avant Garde", "Georgia" => "Georgia", "Ms Reference Sans Serif" => "Ms Reference Sans Serif")),
                                                    'FONT_SIZE' =>
                                                    array('font-size', $this->mlText[28], $this->mlText[29], TEMPLATE_PROPERTY_TYPE_RANGE, array(5, 100, '')),
                                                      'FONT_WEIGHT' =>
                                                      array('font-weight', $this->mlText[30], $this->mlText[31], TEMPLATE_PROPERTY_TYPE_SELECT, array('' => '', "normal" => $this->mlText[73], "bold" => $this->mlText[74])),
                                                        'LINE_HEIGHT' =>
                                                        array('line-height', $this->mlText[128], $this->mlText[129], TEMPLATE_PROPERTY_TYPE_RANGE, array(0, 30, '')),
                                                          'WORD_SPACING' =>
                                                          array('word-spacing', $this->mlText[122], $this->mlText[123], TEMPLATE_PROPERTY_TYPE_RANGE, array(0, 30, '')),
                                                            'LETTER_SPACING' =>
                                                            array('letter-spacing', $this->mlText[75], $this->mlText[76], TEMPLATE_PROPERTY_TYPE_RANGE, array(0, 30, '')),
                                                              'WHITE_SPACE' =>
                                                              array('white-space', $this->mlText[117], $this->mlText[118], TEMPLATE_PROPERTY_TYPE_SELECT, array('' => '', "normal" => $this->mlText[119], "pre" => $this->mlText[120], "nowrap" => $this->mlText[121])),
                                                                'DIRECTION' =>
                                                                array('direction', $this->mlText[124], $this->mlText[125], TEMPLATE_PROPERTY_TYPE_SELECT, array('' => '', "ltr" => $this->mlText[126], "rtl" => $this->mlText[127])),
                                                                  'FONT_STYLE' =>
                                                                  array('font-style', $this->mlText[32], $this->mlText[33], TEMPLATE_PROPERTY_TYPE_SELECT, array('' => '', "normal" => $this->mlText[89], "italic" => $this->mlText[90], "oblique" => $this->mlText[91])),
                                                                    'TEXT_DECORATION' =>
                                                                    array('text-decoration', $this->mlText[34], $this->mlText[35], TEMPLATE_PROPERTY_TYPE_SELECT, array('' => '', "none" => $this->mlText[77], "underline" => $this->mlText[78], "line-through" => $this->mlText[79], "overline" => $this->mlText[80])),
                                                                      'TEXT_INDENT' =>
                                                                      array('text-indent', $this->mlText[81], $this->mlText[82], TEMPLATE_PROPERTY_TYPE_RANGE, array(0, 200, '')),
                                                                        'TEXT_TRANSFORM' =>
                                                                        array('text-transform', $this->mlText[83], $this->mlText[84], TEMPLATE_PROPERTY_TYPE_SELECT, array('' => '', "none" => $this->mlText[85], "uppercase" => $this->mlText[86], "lowercase" => $this->mlText[87], "capitalize" => $this->mlText[88])),
                                                                          'LINK_COLOR' =>
                                                                          array('color', $this->mlText[42], $this->mlText[43], TEMPLATE_PROPERTY_TYPE_COLOR),
                                                                            'LINK_TEXT_DECORATION' =>
                                                                            array('text-decoration', $this->mlText[44], $this->mlText[45], TEMPLATE_PROPERTY_TYPE_SELECT, array('' => '', "none" => $this->mlText[46], "underline" => $this->mlText[47])),
                                                                              'LINK_HOVER_COLOR' =>
                                                                              array('color', $this->mlText[51], $this->mlText[52], TEMPLATE_PROPERTY_TYPE_COLOR),
                                                                                'LINK_HOVER_TEXT_DECORATION' =>
                                                                                array('text-decoration', $this->mlText[53], $this->mlText[54], TEMPLATE_PROPERTY_TYPE_SELECT, array('' => '', "none" => $this->mlText[46], "underline" => $this->mlText[47])),
                                                                                  'LINK_USED_COLOR' =>
                                                                                  array('color', $this->mlText[55], $this->mlText[56], TEMPLATE_PROPERTY_TYPE_COLOR),
                                                                                    'LINK_HOVER_BACKGROUND_COLOR' =>
                                                                                    array('background-color', $this->mlText[57], $this->mlText[58], TEMPLATE_PROPERTY_TYPE_COLOR),
                                                                                      'IMAGE_BACKGROUND_COLOR' =>
                                                                                      array('image-background-color', $this->mlText[63], $this->mlText[64], TEMPLATE_PROPERTY_TYPE_COLOR),
                                                                                      );
  }

  // Setup for the property types being edited
  function setCurrentPropertyTypes($currentPropertyTypes) {
    $this->currentPropertyTypes = $currentPropertyTypes;
  }

  function setCurrentPropertySetId($currentTemplatePropertySetId) {
    $this->currentTemplatePropertySetId = $currentTemplatePropertySetId;
  }

  function setCurrentModelId($currentTemplateModelId) {
    $this->currentTemplateModelId = $currentTemplateModelId;
  }

  function setCurrentTagId($currentTagID) {
    $this->currentTagID = $currentTagID;
  }

  // Get the value of a property
  function getValue($name) {
    $value = '';

    // If the pair exists then return its value
    if ($name) {
      if ($templateProperty = $this->templatePropertyUtils->selectByTemplatePropertySetIdAndName($this->currentTemplatePropertySetId, $name)) {
        $value = $templateProperty->getValue();
      }
    }

    return($value);
  }

  // Set the value of a property
  function setValue($name, $value) {
    // If the pair exists then set its value
    if ($name) {
      if ($templateProperty = $this->templatePropertyUtils->selectByTemplatePropertySetIdAndName($this->currentTemplatePropertySetId, $name)) {
        $templateProperty->setValue($value);
        $this->update($templateProperty);
      }
    }
  }

  // Check if the type of a property is a boolean value
  function isBoolean($name) {
    if ($this->getType($name) == TEMPLATE_PROPERTY_TYPE_BOOLEAN) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if the type of a property is a character string
  function isText($name) {
    if ($this->getType($name) == TEMPLATE_PROPERTY_TYPE_TEXT) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if the type of a property is a block of text
  function isTextarea($name) {
    if ($this->getType($name) == TEMPLATE_PROPERTY_TYPE_TEXTAREA) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if the type of a property is an image
  function isImage($name) {
    if ($this->getType($name) == TEMPLATE_PROPERTY_TYPE_IMAGE) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if the type of a property is a color
  function isColor($name) {
    if ($this->getType($name) == TEMPLATE_PROPERTY_TYPE_COLOR) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if the type of a property is a range of numbers
  function isRange($name) {
    if ($this->getType($name) == TEMPLATE_PROPERTY_TYPE_RANGE) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if the type of a property is a selection in a list
  function isSelect($name) {
    if ($this->getType($name) == TEMPLATE_PROPERTY_TYPE_SELECT) {
      return(true);
    } else {
      return(false);
    }
  }

  // Get the property
  function getProperty($name) {
    $property = array();

    // If the pair exists then return its value
    if ($name) {
      $css = '';
      $description = '';
      $help = '';
      $type = '';
      $selectOptions = '';

      if ($this->propertyTypes) {
        $properties = $this->propertyTypes[$name];
        if (count($properties) == 5) {
          list($css, $description, $help, $type, $selectOptions) = $properties;
        } else if (count($properties) == 4) {
          list($css, $description, $help, $type) = $properties;
          $selectOptions = '';
        }
      }

      $property = array(
        "css" => $css,
        "name" => $name,
        "description" => $description,
        "help" => $help,
        "type" => $type,
        "selectOptions" => $selectOptions,
      );
    }

    return($property);
  }

  // Get the css name of a property
  function getCss($name) {
    $css = '';

    $properties = $this->getProperty($name);
    if (isset($properties["css"])) {
      $css = $properties["css"];
    }

    return($css);
  }

  // Get the type of a property
  function getType($name) {
    $property = $this->getProperty($name);
    $type = $property["type"];

    return($type);
  }

  // Get the description of a property
  function getDescription($name) {
    $property = $this->getProperty($name);
    $description = $property["description"];

    return($description);
  }

  // Get the help text of a property
  function getHelp($name) {
    $property = $this->getProperty($name);
    $help = $property["help"];

    return($help);
  }

  // Get the select options of a property
  function getSelectOptions($name) {
    $property = $this->getProperty($name);
    $selectOptions = $property["selectOptions"];

    return($selectOptions);
  }

  // Get the default value, if any, of the select range of a property
  function getRangeDefault($name) {
    $default = '';

    $property = $this->getProperty($name);
    $selectOptions = $property["selectOptions"];

    if (count($selectOptions) == 3) {
      $default = $selectOptions[2];

      return($default);
    }
  }

  // Get the select range of a property
  // A range is defined of a min value and a max value
  // and possibly a default value
  // The default value if any is the third element
  function getRange($name) {
    $selectRange = array('' => '');

    $property = $this->getProperty($name);
    $selectOptions = $property["selectOptions"];

    if (count($selectOptions) > 1) {
      $mini = $selectOptions[0];
      $maxi = $selectOptions[1];
      if (is_numeric($mini) && is_numeric($maxi)) {
        for ($i = $mini; $i <= $maxi; $i++) {
          $selectRange[$i] = $i;
        }
      }

      if (count($selectOptions) == 3) {
        $default = array('' => $selectOptions[2]);
        $selectRange = LibUtils::arrayMerge($selectRange, $default);
      }
    }

    return($selectRange);
  }

  // Create a property set
  function createPropertySet() {
    $templatePropertySet = new TemplatePropertySet();
    $this->insert($templatePropertySet);
    $templatePropertySetId = $this->getLastInsertId();

    return($templatePropertySetId);
  }

  // Duplicate a property set
  function duplicate($templatePropertySetId) {
    if ($templatePropertySet = $this->selectById($templatePropertySetId)) {
      $this->insert($templatePropertySet);
      $lastInsertTemplatePropertySetId = $this->getLastInsertId();

      // Duplicate the properties
      $templateProperties = $this->templatePropertyUtils->selectByTemplatePropertySetId($templatePropertySetId);
      foreach ($templateProperties as $templateProperty) {
        $templatePropertyId = $templateProperty->getId();
        $this->templatePropertyUtils->duplicate($templatePropertyId, $lastInsertTemplatePropertySetId);
      }

      return($lastInsertTemplatePropertySetId);
    }
  }

  // Export a property set
  function exportXML($xmlNode, $templatePropertySetId, $attributes = '') {
    if ($templatePropertySet = $this->selectById($templatePropertySetId)) {
      $xmlChildNode = $xmlNode->addChild(TEMPLATE_PROPERTY_SET);
      if (is_array($attributes)) {
        foreach ($attributes as $aName => $aValue) {
          $xmlChildNode->addAttribute($aName, $aValue);
        }
      }

      // Export the properties
      $templateProperties = $this->templatePropertyUtils->selectByTemplatePropertySetId($templatePropertySetId);
      foreach ($templateProperties as $templateProperty) {
        $templatePropertyId = $templateProperty->getId();
        $this->templatePropertyUtils->exportXML($xmlChildNode, $templatePropertyId);
      }
    }
  }

  // Import a property set
  function importXML($xmlNode) {
    global $gTemplateDataPath;

    // Create the property set
    $templatePropertySet = new TemplatePropertySet();
    $this->insert($templatePropertySet);
    $lastInsertTemplatePropertySetId = $this->getLastInsertId();

    $xmlChildNodes = $xmlNode->children();
    foreach ($xmlChildNodes as $xmlChildNode) {
      $name = $xmlChildNode->attributes()["name"];
      $value = $xmlChildNode->attributes()["value"];

      // Create the property
      $templateProperty = new TemplateProperty();
      $templateProperty->setName($name);
      $templateProperty->setValue($value);
      $templateProperty->setTemplatePropertySetId($lastInsertTemplatePropertySetId);
      $this->templatePropertyUtils->insert($templateProperty);

      // Copy the images if any
      if ($name == 'BACKGROUND_IMAGE') {
        if (is_file($gTemplateDataPath . "export/image/$value")) {
          copy($gTemplateDataPath . "export/image/$value", $this->templatePropertyUtils->imagePath . $value);
        }
      }
    }

    return($lastInsertTemplatePropertySetId);
  }

  // Delete the properties of a property set
  function deleteTemplatePropertySet($templatePropertySetId) {
    if ($templateProperties = $this->templatePropertyUtils->selectByTemplatePropertySetId($templatePropertySetId)) {
      foreach ($templateProperties as $templateProperty) {
        $this->templatePropertyUtils->delete($templateProperty->getId());
      }
    }

    $this->delete($templatePropertySetId);
  }

  // Render the text properties
  function renderHtmlTextProperties($templatePropertySetId) {
    $str = '';

    $linkPropertyNames = array('FONT_TYPE', 'FONT_SIZE', 'FONT_WEIGHT', 'FONT_COLOR', 'FONT_STYLE', 'TEXT_DECORATION', 'TEXT_TRANSFORM', 'TEXT_INDENT', 'LINE_HEIGHT', 'WORD_SPACING', 'LETTER_SPACING', 'WHITE_SPACE', 'DIRECTION');

    if ($templatePropertySetId) {
      $properties = $this->getProperties($templatePropertySetId, $linkPropertyNames);

      foreach ($properties as $name => $value) {
        // Get the css name
        $cssName = $this->getCssName($name);

        // Get the css value
        $cssValue = $this->getCssValue($name, $value, $templatePropertySetId);

        if ($cssName && $cssValue) {
          $str .= "$cssName: $cssValue; ";
        }
      }
    }

    $str = trim($str);

    return($str);
  }

  // Render the link properties
  function renderHtmlLinkProperties($strTag, $templatePropertySetId) {
    $str = '';

    $linkPropertyNames = array('LINK_COLOR', 'LINK_TEXT_DECORATION');

    if ($templatePropertySetId) {
      $properties = $this->getProperties($templatePropertySetId, $linkPropertyNames);

      foreach ($properties as $name => $value) {
        // Get the css name
        $cssName = $this->getCssName($name);

        // Get the css value
        $cssValue = $this->getCssValue($name, $value, $templatePropertySetId);

        if ($cssName && $cssValue) {
          $str .= "$cssName: $cssValue; ";
        }
      }
    }

    $str = trim($str);

    if ($str) {
      $str = " $strTag a { $str }";
    }

    $str .= $this->renderHtmlLinkVisitedProperties($strTag, $templatePropertySetId);

    $str .= $this->renderHtmlLinkHoverProperties($strTag, $templatePropertySetId);

    return($str);
  }

  // Render the link hover properties
  function renderHtmlLinkHoverProperties($strTag, $templatePropertySetId) {
    $str = '';

    $linkPropertyNames = array('LINK_HOVER_COLOR', 'LINK_HOVER_TEXT_DECORATION', 'LINK_HOVER_BACKGROUND_COLOR');

    if ($templatePropertySetId) {
      $properties = $this->getProperties($templatePropertySetId, $linkPropertyNames);

      foreach ($properties as $name => $value) {
        // Get the css name
        $cssName = $this->getCssName($name);

        // Get the css value
        $cssValue = $this->getCssValue($name, $value, $templatePropertySetId);

        if ($cssName && $cssValue) {
          $str .= "$cssName: $cssValue; ";
        }
      }
    }

    $str = trim($str);

    if ($str) {
      $str = " $strTag a:hover { $str }";
    }

    return($str);
  }

  // Render the link visited properties
  function renderHtmlLinkVisitedProperties($strTag, $templatePropertySetId) {
    $str = '';

    $linkPropertyNames = array('LINK_USED_COLOR');

    if ($templatePropertySetId) {
      $properties = $this->getProperties($templatePropertySetId, $linkPropertyNames);

      foreach ($properties as $name => $value) {
        // Get the css name
        $cssName = $this->getCssName($name);

        // Get the css value
        $cssValue = $this->getCssValue($name, $value, $templatePropertySetId);

        if ($cssName && $cssValue) {
          $str .= "$cssName: $cssValue; ";
        }
      }
    }

    $str = trim($str);

    if ($str) {
      $str = " $strTag a:visited { $str }";
    }

    return($str);
  }

  // Render the set of properties for an html output
  function renderHtmlProperties($templatePropertySetId) {
    $str = '';

    if ($templatePropertySetId) {
      $properties = $this->getProperties($templatePropertySetId);

      foreach ($properties as $name => $value) {
        // Do not render the link specific properties
        if (strstr($name, 'LINK_')) {
          continue;
        }

        // Get the css name
        $cssName = $this->getCssName($name);

        // Get the css value
        $cssValue = $this->getCssValue($name, $value, $templatePropertySetId);

        // Have an extensible height with a minimum value. This is so that a container heigth will extend with its content. It is specially important if the container has a graphical frame of background images. Note that the min-height and height auto important must be rendered before the height.
        if ($name == 'HEIGHT') {
          $str .= 'min-height: ' . $cssValue . '; height: auto !important; ';
        }

        if ($cssName && $cssValue) {
          $str .= "$cssName: $cssValue; ";
        }

      }
    }

    $str = trim($str);

    return($str);
  }

  // Get the css name for a property
  function getCssName($name) {
    $cssName = strtolower($this->getCss($name));

    return($cssName);
  }

  // Get the css value for a property
  function getCssValue($name, $value, $templatePropertySetId) {
    // Do not display 0px values except for some properties
    if (!in_array($name, array('MARGIN', 'PADDING', 'BORDER_SIZE', 'FONT_SIZE')) && $value == '0') {
      $value = '';
    }

    // In the most common case, nothing is to be done
    $cssValue = $value;

    // Add a hash before a color code if none
    if (strstr($name, 'COLOR') && substr($value, 0, 1) != '#') {
      $cssValue = '#' . $cssValue;
    }

    // Add a pixel unit except on a font weight numeric value
    if (is_numeric($value) && $name != 'FONT_WEIGHT') {
      $cssValue .= 'px';
    }

    // IE5/6 hack to work around the box model bug
    // IE5.5 or IE6 in quirks mode considers the width or heigth to contain
    // the border and padding, when it should not
    if (($name == 'HEIGHT' || $name == 'WIDTH') && !strstr($value, '%')) {
      // Calculate the value fit for IE5/6
      $IE6Value = $value;
      if ($otherTemplateProperty = $this->templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, "BORDER")) {
        $otherValue = $otherTemplateProperty->getValue();
        $IE6Value += ($otherValue * 2);
      }
      if ($otherTemplateProperty = $this->templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, "PADDING")) {
        $otherValue = $otherTemplateProperty->getValue();
        $IE6Value += ($otherValue * 2);
      }

      // The first property "width:" is for browsers like Firefox, Mozilla and Opera that render correctly as they choke on the escape character (\) and therefore ignore the second and third properties. The second property "\width:" is for IE 5 and 6/quirks mode. The third property "w\idth:" will be read by escape friendly browsers (including IE 6 in non-quirks mode) and set the width back to the first value.
      if ($IE6Value != $value) {
        if ($name == 'WIDTH') {
          $cssValue .= '; \width: ' . $IE6Value . 'px; w\idth: ' . $value . 'px';
        } else {
          $cssValue .= '; \height: ' . $IE6Value . 'px; he\ight: ' . $value . 'px';
        }
      }
    }

    // The hack to have a div align on the left, center, right
    if ($name == 'MARGIN') {
      // The external margin cannot be handled if an alignment is specified on the
      // element because it is already used to align the content left, center or right
      if ($otherTemplateProperty = $this->templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, "ALIGNMENT")) {
        $otherValue = $otherTemplateProperty->getValue();
        if ($otherValue == 'left') {
        } else if ($otherValue == 'center') {
          $cssValue .= " auto";
        } else if ($otherValue == 'right') {
          $cssValue .= " $cssValue $cssValue auto";
        }
      } else {
        if (!$value) {
          $value = '0';
        }
        if ($otherTemplateProperty = $this->templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, "MARGIN_POSITION")) {
          $otherValue = $otherTemplateProperty->getValue();
          $cssValue = $this->renderPosition($value, $otherValue);
        }
      }
    }

    // The external margin position is only used in conjunction with another property
    if ($name == 'MARGIN_POSITION') {
      $cssValue = '';
    }

    // Get the position of the border
    if ($name == 'BORDER_SIZE') {
      if (!$value) {
        $value = '0';
      }
      if ($otherTemplateProperty = $this->templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, "BORDER_POSITION")) {
        $otherValue = $otherTemplateProperty->getValue();
        $cssValue = $this->renderPosition($value, $otherValue);
      }
    }

    // The border position is only used in conjunction with another property
    if ($name == 'BORDER_POSITION') {
      $cssValue = '';
    }

    // Get the position of the internal margin
    if ($name == 'PADDING') {
      if (!$value) {
        $value = '0';
      }
      if ($otherTemplateProperty = $this->templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, "PADDING_POSITION")) {
        $otherValue = $otherTemplateProperty->getValue();
        $cssValue = $this->renderPosition($value, $otherValue);
      }
    }

    // The internal margin position is only used in conjunction with another property
    if ($name == 'PADDING_POSITION') {
      $cssValue = '';
    }

    // Add an alternative font family
    if ($name == 'FONT_TYPE') {
      $cssValue = "'" . $cssValue . "'";
      $cssValue .= ", Verdana";
    }

    // Render the image url for the background image property
    if ($name == 'BACKGROUND_IMAGE') {
      $cssValue = 'url(' . $this->templatePropertyUtils->imageUrl . '/' .  $cssValue . ')';
      if (!$this->templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, "BACKGROUND_REPEAT")) {
        $cssValue .= '; background-repeat: no-repeat';
      }
    }

    return($cssValue);
  }

  // Render a position
  function renderPosition($value, $otherValue) {
    // Do not apply the position to a zero value
    if (!$otherValue || $value == '0') {
      $cssValue = $value . "px";
    } else if ($otherValue == 'top left bottom right') {
      $cssValue = $value . "px";
    } else if ($otherValue == 'top left bottom') {
      $cssValue = $value . "px 0px " . $value . "px " . $value . "px";
    } else if ($otherValue == 'left bottom right') {
      $cssValue = "0px " . $value . "px " . $value . "px " . $value . "px";
    } else if ($otherValue == 'bottom right top') {
      $cssValue = $value . "px " . $value . "px " . $value . "px 0px";
    } else if ($otherValue == 'right top left') {
      $cssValue = $value . "px " . $value . "px 0px " . $value . "px";
    } else if ($otherValue == 'top bottom') {
      $cssValue = $value . "px 0px " . $value . "px 0px";
    } else if ($otherValue == 'left right') {
      $cssValue = "0px " . $value . "px 0px " . $value . "px";
    } else if ($otherValue == 'top right') {
      $cssValue = $value . "px " . $value . "px 0px 0px";
    } else if ($otherValue == 'right bottom') {
      $cssValue = "0px " . $value . "px " . $value . "px 0px";
    } else if ($otherValue == 'bottom left') {
      $cssValue = "0px 0px " . $value . "px " . $value . "px";
    } else if ($otherValue == 'left top') {
      $cssValue = $value . "px 0px 0px " . $value . "px";
    } else if ($otherValue == 'top') {
      $cssValue = $value . "px 0px 0px 0px";
    } else if ($otherValue == 'right') {
      $cssValue = "0px " . $value . "px 0px 0px";
    } else if ($otherValue == 'bottom') {
      $cssValue = "0px 0px " . $value . "px 0px";
    } else if ($otherValue == 'left') {
      $cssValue = "0px 0px 0px " . $value . "px";
    } else if ($otherValue == 'top right bottom') {
      $cssValue = $value . "px " . $value . "px " . $value . "px 0px";
    } else if ($otherValue == 'right bottom left') {
      $cssValue = "0px " . $value . "px " . $value . "px " . $value . "px";
    } else if ($otherValue == 'bottom left top') {
      $cssValue = $value . "px 0px " . $value . "px " . $value . "px";
    } else if ($otherValue == 'left top right') {
      $cssValue = $value . "px " . $value . "px 0px " . $value . "px";
    }

    return($cssValue);
  }

  // Get all the properties of a property set
  function getProperties($templatePropertySetId, $subsetPropertyNames = '') {
    $properties = array();

    if ($templatePropertySetId) {
      if ($templatePropertySet = $this->selectById($templatePropertySetId)) {
        $templateProperties = $this->templatePropertyUtils->selectByTemplatePropertySetId($templatePropertySetId);
        foreach ($templateProperties as $templateProperty) {
          $name = $templateProperty->getName();
          $value = $templateProperty->getValue();

          // If a subset of properties is specified
          // then check that the property is part of it
          if ($subsetPropertyNames && !in_array($name, $subsetPropertyNames)) {
            continue;
          }

          $properties[$name] = $value;
        }
      }
    }

    return($properties);
  }

}

?>
