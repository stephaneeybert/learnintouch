<?php

class TemplateModelUtils extends TemplateModelDB {

  var $mlText;

  var $languageUtils;
  var $templatePropertySetUtils;
  var $templateContainerUtils;
  var $templateElementUtils;
  var $templatePropertyUtils;
  var $templatePageUtils;
  var $templateUtils;
  var $lexiconEntryUtils;
  var $profileUtils;
  var $facebookUtils;
  var $linkedinUtils;
  var $navlinkItemUtils;
  var $navbarItemUtils;
  var $navmenuItemUtils;
  var $documentUtils;

  function TemplateModelUtils() {
    $this->TemplateModelDB();
  }

  function getPropertyTypes() {
    $propertyTypes = array(
        'ALIGNMENT',
        'VERTICAL_ALIGNMENT',
        'MARGIN',
        'PADDING',
        'PADDING_POSITION',
        'BORDER_STYLE',
        'BORDER_SIZE',
        'BORDER_COLOR',
        'BORDER_POSITION',
        'BACKGROUND_COLOR',
        'BACKGROUND_IMAGE',
        'BACKGROUND_REPEAT',
        'BACKGROUND_ATTACHMENT',
        'BACKGROUND_POSITION',
        'FONT_TYPE',
        'FONT_SIZE',
        'FONT_WEIGHT',
        'FONT_COLOR',
        'FONT_STYLE',
        'TEXT_DECORATION',
        'TEXT_TRANSFORM',
        'TEXT_INDENT',
        'LINE_HEIGHT',
        'WORD_SPACING',
        'LETTER_SPACING',
        'WHITE_SPACE',
        'DIRECTION',
        'IMAGE_BACKGROUND_COLOR',
        'LINK_COLOR',
        'LINK_TEXT_DECORATION',
        'LINK_HOVER_COLOR',
        'LINK_HOVER_TEXT_DECORATION',
        'LINK_HOVER_BACKGROUND_COLOR',
        'LINK_USED_COLOR'
          );

    return($propertyTypes);
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  // Get the list of model types
  function getModelTypes() {
    $this->loadLanguageTexts();

    // Each model type describes the content positioning in terms of building blocks
    // Each block is seen as a table cell
    $modelTypes = array(
        'NINE_CELLS' => array( "Nine cells", $this->mlText[3], array(3, 3, 3)),
        'FIVE_CELLS' => array( "Five cells", $this->mlText[2], array(1, 3, 1)),
        'THREE_COLUMNS' => array( "Three columns", $this->mlText[1], array(3)),
        'THREE_ROWS' => array( "Three rows", $this->mlText[4], array(1, 1, 1)),
        'TWO_ROWS' => array( "Two rows", $this->mlText[5], array(1, 1)),
        'TWO_COLUMNS' => array( "Two columns", $this->mlText[6], array(2)),
        'ONE_CELL' => array( "One cell", $this->mlText[7], array(1)),
        );

    return($modelTypes);
  }

  // Get the list of model type names
  function getModelTypeNames() {
    $listModelTypes = array();

    $modelTypes = $this->getModelTypes();

    foreach ($modelTypes as $modelTypeId => $modelType) {
      $listModelTypes[$modelTypeId] = $modelType[1];
    }

    return($listModelTypes);
  }

  // Get a model type name
  function getModelTypeName($modelTypeId) {
    $name = '';

    if ($modelTypeId) {
      $modelTypes = $this->getModelTypes();

      $modelType = $modelTypes[$modelTypeId];

      $name = $modelType[0];
    }

    return($name);
  }

  // Get a model type description
  function getModelTypeDescription($modelTypeId) {
    $description = '';

    if ($modelTypeId) {
      $modelTypes = $this->getModelTypes();

      $modelType = $modelTypes[$modelTypeId];

      $description = $modelType[1];
    }

    return($description);
  }

  // Get a model containers from the model type
  // This is used only when creating a model
  // The model type is a template for the number of containers per row
  function getModelTypeContainers($modelTypeId) {
    $modelContainers = array();

    if ($modelTypeId) {
      $modelTypes = $this->getModelTypes();

      $modelType = $modelTypes[$modelTypeId];

      $modelContainers = $modelType[2];
    }

    return($modelContainers);
  }

  // Add a container to a row
  function addTemplateContainer($templateModelId, $row) {
    $cell = $this->templateContainerUtils->getNextCellNumber($templateModelId, $row);
    if (!$templateContainer = $this->templateContainerUtils->selectByModelIdAndRowAndCell($templateModelId, $row, $cell)) {
      $this->createContainer($templateModelId, $row, $cell);
    }
  }

  // Swap a row of containers with the next row
  function swapRowContainersWithNext($templateModelId, $currentRow) {
    $nextRow = $currentRow + 1;
    if ($nextRow > $currentRow && $nextTemplateContainers = $this->templateContainerUtils->selectByModelIdAndRow($templateModelId, $nextRow)) {
      if ($currentTemplateContainers = $this->templateContainerUtils->selectByModelIdAndRow($templateModelId, $currentRow)) {
        foreach ($currentTemplateContainers as $currentTemplateContainer) {
          $currentTemplateContainer->setRow($nextRow);
          $this->templateContainerUtils->update($currentTemplateContainer);
        }
        foreach ($nextTemplateContainers as $nextTemplateContainer) {
          $nextTemplateContainer->setRow($currentRow);
          $this->templateContainerUtils->update($nextTemplateContainer);
        }
      }
    }
  }

  // Swap a row of containers with the previous row
  function swapRowContainersWithPrevious($templateModelId, $currentRow) {
    if ($currentRow > 0) {
      $previousRow = $currentRow - 1;
    } else {
      $previousRow = 0;
    }
    if ($previousRow < $currentRow && $previousTemplateContainers = $this->templateContainerUtils->selectByModelIdAndRow($templateModelId, $previousRow)) {
      if ($currentTemplateContainers = $this->templateContainerUtils->selectByModelIdAndRow($templateModelId, $currentRow)) {
        foreach ($currentTemplateContainers as $currentTemplateContainer) {
          $currentTemplateContainer->setRow($previousRow);
          $this->templateContainerUtils->update($currentTemplateContainer);
        }
        foreach ($previousTemplateContainers as $previousTemplateContainer) {
          $previousTemplateContainer->setRow($currentRow);
          $this->templateContainerUtils->update($previousTemplateContainer);
        }
      }
    }
  }

  // Move a container into the next row
  function moveContainerIntoNextRow($templateContainerId) {
    if ($templateContainer = $this->templateContainerUtils->selectById($templateContainerId)) {
      $row = $templateContainer->getRow();
      $row++;
      $templateModelId = $templateContainer->getTemplateModelId();
      $cell = $this->templateContainerUtils->getNextCellNumber($templateModelId, $row);
      if ($cell) {
        if (!$wTemplateContainer = $this->templateContainerUtils->selectByModelIdAndRowAndCell($templateModelId, $row, $cell)) {
          $templateContainer->setRow($row);
          $templateContainer->setCell($cell);
          $this->templateContainerUtils->update($templateContainer);
        }
      }
    }
  }

  // Move a container into the previous row
  function moveContainerIntoPreviousRow($templateContainerId) {
    if ($templateContainer = $this->templateContainerUtils->selectById($templateContainerId)) {
      $row = $templateContainer->getRow();
      if ($row > 0) {
        $row--;
      }
      $templateModelId = $templateContainer->getTemplateModelId();
      $cell = $this->templateContainerUtils->getNextCellNumber($templateModelId, $row);
      if (!$wTemplateContainer = $this->templateContainerUtils->selectByModelIdAndRowAndCell($templateModelId, $row, $cell)) {
        $templateContainer->setRow($row);
        $templateContainer->setCell($cell);
        $this->templateContainerUtils->update($templateContainer);
      }
    }
  }

  // Add a row with one container
  function addRow($templateModelId) {
    $row = $this->templateContainerUtils->getNextRowNumber($templateModelId);
    if ($row) {
      $this->addTemplateContainer($templateModelId, $row);
    }
  }

  // Get the list of models
  function getAllModels() {
    $modelNames = array(' ' => '');

    $templateModels = $this->selectAll();
    foreach ($templateModels as $templateModel) {
      $templateModelId = $templateModel->getId();
      $name = $templateModel->getName();
      $modelNames[$templateModelId] = $name;
    }

    return($modelNames);
  }

  // Create the containers for a model
  function createContainers($templateModelId, $parentTemplateModelId) {
    if ($templateModelId) {
      if ($templateModel = $this->selectById($templateModelId)) {
        if ($parentTemplateModel = $this->selectById($parentTemplateModelId)) {
          if ($parentTemplateContainers = $this->templateContainerUtils->selectByTemplateModelId($parentTemplateModelId)) {
            foreach ($parentTemplateContainers as $parentTemplateContainer) {
              $row = $parentTemplateContainer->getRow();
              $cell = $parentTemplateContainer->getCell();
              $this->createContainer($templateModelId, $row, $cell);
            }
          }
        } else {
          $modelType = $templateModel->getModelType();
          $modelContainers = $this->getModelTypeContainers($modelType);
          foreach ($modelContainers as $row => $cells) {
            for ($cell = 0; $cell < $cells; $cell++) {
              if (!$templateContainer = $this->templateContainerUtils->selectByModelIdAndRowAndCell($templateModelId, $row, $cell)) {
                $this->createContainer($templateModelId, $row, $cell);
              }
            }
          }
        }
      }
    }
  }

  // Create a container for a model
  function createContainer($templateModelId, $row, $cell) {
    if ($templateModelId) {
      $templatePropertySetId = $this->templatePropertySetUtils->createPropertySet();

      // The margin property is required if centering the container content,
      // as the centering is done with a margin: 0px auto; syntax
      if ($templatePropertySet = $this->templatePropertySetUtils->selectById($templatePropertySetId)) {
        $templateProperty = new TemplateProperty();
        $templateProperty->setName('MARGIN');
        $templateProperty->setValue(0);
        $templateProperty->setTemplatePropertySetId($templatePropertySetId);
        $this->templatePropertyUtils->insert($templateProperty);
      }

      $templateContainer = new TemplateContainer();
      $templateContainer->setTemplateModelId($templateModelId);
      $templateContainer->setRow($row);
      $templateContainer->setCell($cell);
      $templateContainer->setTemplatePropertySetId($templatePropertySetId);
      $this->templateContainerUtils->insert($templateContainer);
    }
  }

  // Delete a model and all its containers, elements, property sets and properties
  function deleteTemplateModel($templateModelId) {
    if ($templateModelId) {
      // Delete all the model containers
      if ($templateContainers = $this->templateContainerUtils->selectByTemplateModelId($templateModelId)) {
        foreach ($templateContainers as $templateContainer) {
          $templateContainerId = $templateContainer->getId();
          $this->templateContainerUtils->deleteTemplateContainer($templateContainerId);
        }
      }

      // Delete all the system pages
      if ($templatePages = $this->templatePageUtils->selectByTemplateModelId($templateModelId)) {
        foreach ($templatePages as $templatePage) {
          $templatePageId = $templatePage->getId();
          $this->templatePageUtils->deleteTemplatePage($templatePageId);
        }
      }

      // Reset the model references in the navigation elements if any
      // that is, in elements belonging to other models
      $this->navlinkItemUtils->resetNavigationModelReferences($templateModelId);
      $this->navbarItemUtils->resetNavigationModelReferences($templateModelId);
      $this->navmenuItemUtils->resetNavigationModelReferences($templateModelId);

      // Delete the properties of the model
      if ($templateModel = $this->selectById($templateModelId)) {
        // The model must be deleted before its properties
        $this->delete($templateModelId);

        $templatePropertySetId = $templateModel->getTemplatePropertySetId();
        if ($templatePropertySetId) {
          $this->templatePropertySetUtils->deleteTemplatePropertySet($templatePropertySetId);
        }
      }
    }
  }

  // Duplicate a model
  function duplicate($templateModelId, $name, $description) {
    if ($templateModel = $this->selectById($templateModelId)) {
      $templateModel->setName($name);
      $templateModel->setDescription($description);
      $lastInsertTemplatePropertySetId = $this->templatePropertySetUtils->duplicate($templateModel->getTemplatePropertySetId());
      $templateModel->setTemplatePropertySetId($lastInsertTemplatePropertySetId);
      $lastInsertInnerTemplatePropertySetId = $this->templatePropertySetUtils->duplicate($templateModel->getInnerTemplatePropertySetId());
      $templateModel->setInnerTemplatePropertySetId($lastInsertInnerTemplatePropertySetId);
      $this->insert($templateModel);
      $lastInsertTemplateModelId = $this->getLastInsertId();

      // Duplicate the containers
      if ($templateContainers = $this->templateContainerUtils->selectByTemplateModelId($templateModelId)) {
        foreach ($templateContainers as $templateContainer) {
          $this->templateContainerUtils->duplicate($templateContainer, $lastInsertTemplateModelId);
        }
      }

      // Duplicate the system pages
      if ($templatePages = $this->templatePageUtils->selectByTemplateModelId($templateModelId)) {
        foreach ($templatePages as $templatePage) {
          $this->templatePageUtils->duplicate($templatePage, $lastInsertTemplateModelId);
        }
      }
    }

    return($lastInsertTemplateModelId);
  }

  // Export a model
  function exportXML($templateModelId) {
    global $gTemplateDataPath;

    $xmlDocument  = new XML_Tree();
    // The reference character & is required by the xml library
    $xmlNode =& $xmlDocument->addRoot("template");

    if ($templateModel = $this->selectById($templateModelId)) {
      $name = $templateModel->getName();
      $description = $templateModel->getDescription();
      $modelType = $templateModel->getModelType();

      $name = LibString::stripNonFilenameChar($name);

      // If a model is already exported under the same name
      // alter the name of the model to be exported
      $filename = $gTemplateDataPath . "export/xml/$name";
      if (is_file($filename)) {
        $randomNumber = LibUtils::generateUniqueId();
        $name = $name . TEMPLATE_DUPLICATA . '_' . $randomNumber;
        $templateModel->setName($name);
      }

      $attributes = array("name" => $name, "description" => $description, "modelType" => $modelType);
      $xmlChildNode =& $xmlNode->addChild(TEMPLATE_MODEL, '', $attributes);

      // Export the property set
      $templatePropertySetId = $templateModel->getTemplatePropertySetId();
      $this->templatePropertySetUtils->exportXML($xmlChildNode, $templatePropertySetId, array("inner" => "false"));

      // Export the inner property set
      $innerTemplatePropertySetId = $templateModel->getInnerTemplatePropertySetId();
      $this->templatePropertySetUtils->exportXML($xmlChildNode, $innerTemplatePropertySetId, array("inner" => "true"));

      // Export the containers
      if ($templateContainers = $this->templateContainerUtils->selectByTemplateModelId($templateModelId)) {
        foreach ($templateContainers as $templateContainer) {
          $templateContainerId = $templateContainer->getId();
          $this->templateContainerUtils->exportXML($xmlChildNode, $templateContainerId);
        }
      }

      // Export the system pages
      if ($templatePages = $this->templatePageUtils->selectByTemplateModelId($templateModelId)) {
        foreach ($templatePages as $templatePage) {
          $templatePageId = $templatePage->getId();
          $this->templatePageUtils->exportXML($xmlChildNode, $templatePageId);
        }
      }

      $filename = $gTemplateDataPath . "export/xml/$name";
      $str = $xmlDocument->get();

      LibFile::writeString($filename, $str);
    }
  }

  // Export a model
  function exportWddx($templateModelId) {
    global $gTemplateDataPath;

    if ($templateModel = $this->selectById($templateModelId)) {
      $name = $templateModel->getName();
      $description = $templateModel->getDescription();
      $modelType = $templateModel->getModelType();

      $name = LibString::stripNonFilenameChar($name);

      // If a model is already exported under the same name
      // alter the name of the model to be exported
      $filename = $gTemplateDataPath . "export/xml/$name";
      if (is_file($filename)) {
        $randomNumber = LibUtils::generateUniqueId();
        $name = $name . TEMPLATE_DUPLICATA . '_' . $randomNumber;
        $templateModel->setName($name);
      }

      $attributes = array("name" => $name, "description" => $description, "modelType" => $modelType);
      $xmlChildNode =& $xmlNode->addChild(TEMPLATE_MODEL, '', $attributes);

      // Export the property set
      $templatePropertySetId = $templateModel->getTemplatePropertySetId();
      $this->templatePropertySetUtils->exportXML($xmlChildNode, $templatePropertySetId, array("inner" => "false"));

      // Export the inner property set
      $innerTemplatePropertySetId = $templateModel->getInnerTemplatePropertySetId();
      $this->templatePropertySetUtils->exportXML($xmlChildNode, $innerTemplatePropertySetId, array("inner" => "true"));

      // Export the containers
      if ($templateContainers = $this->templateContainerUtils->selectByTemplateModelId($templateModelId)) {
        foreach ($templateContainers as $templateContainer) {
          $templateContainerId = $templateContainer->getId();
          $this->templateContainerUtils->exportXML($xmlChildNode, $templateContainerId);
        }
      }

      // Export the system pages
      if ($templatePages = $this->templatePageUtils->selectByTemplateModelId($templateModelId)) {
        foreach ($templatePages as $templatePage) {
          $templatePageId = $templatePage->getId();
          $this->templatePageUtils->exportXML($xmlChildNode, $templatePageId);
        }
      }

      $filename = $gTemplateDataPath . "export/xml/$name";
      $wddxPacketId = wddx_packet_start($name);
      wddx_add_vars($wddxPacketId, 'modelArray');
      $wddxPacket = wddx_packet_end($wddxPacketId);
      LibFile::writeString($filename, $wddxPacket);
    }
  }

  // Import a model
  function importXML($name) {
    global $gTemplateDataPath;

    $lastInsertTemplateModelId = '';

    // Get the xml file content
    $str = LibFile::readIntoString($gTemplateDataPath . "export/xml/$name");

    $xmlDocument  = new XML_Tree();
    $xmlNode = $xmlDocument->getTreeFromString($str);

    $templateModelNodes =& $xmlNode->children;
    foreach ($templateModelNodes as $templateModelNode) {
      $elementName = $templateModelNode->name;

      if ($elementName == TEMPLATE_MODEL) {
        $name = $templateModelNode->attributes["name"];
        $description = $templateModelNode->attributes["description"];
        $modelType = $templateModelNode->attributes["modelType"];

        // Check if a model with the same name already exists
        if ($wTemplateModel = $this->selectByName($name)) {
          $randomNumber = LibUtils::generateUniqueId();
          $name = $name . TEMPLATE_DUPLICATA . '_' . $randomNumber;
        }

        // Delete the model if any
        if ($wTemplateModel = $this->selectByName($name)) {
          $templateModelId = $wTemplateModel->getId();
          $this->deleteTemplateModel($templateModelId);
        }

        // Create the model
        $templateModel = new TemplateModel();
        $templateModel->setName($name);
        $templateModel->setDescription($description);
        $templateModel->setModelType($modelType);
        $this->insert($templateModel);
        $lastInsertTemplateModelId = $this->getLastInsertId();

        $templateModelChildNodes = $templateModelNode->children;
        foreach ($templateModelChildNodes as $templateModelChildNode) {
          $name = $templateModelChildNode->name;

          if ($name == TEMPLATE_PROPERTY_SET) {
            // Create the property set
            $lastInsertTemplatePropertySetId = $this->templatePropertySetUtils->importXML($templateModelChildNode);

            // Link the model and the property set
            $templateModel->setId($lastInsertTemplateModelId);
            $inner = $templateModelChildNode->attributes["inner"];
            if ($inner == "true") {
              $templateModel->setInnerTemplatePropertySetId($lastInsertTemplatePropertySetId);
            } else {
              $templateModel->setTemplatePropertySetId($lastInsertTemplatePropertySetId);
            }
            $this->update($templateModel);
          } else if ($name == TEMPLATE_CONTAINER) {
            // Import the container properties
            $this->templateContainerUtils->importXML($templateModelChildNode, $lastInsertTemplateModelId);
          } else if ($name == TEMPLATE_PAGE) {
            // Import the page properties
            $this->templatePageUtils->importXML($templateModelChildNode, $lastInsertTemplateModelId);
          }
        }
      }
    }

    return($lastInsertTemplateModelId);
  }

  // Render the tag id of the whole model
  // That is the whole page
  // The tag id must be unique for each model
  function renderTagID($templateModelId) {
    $str = '';

    if ($templateModelId) {
      if ($templateModel = $this->selectById($templateModelId)) {
        $templatePropertySetId = $templateModel->getTemplatePropertySetId();

        // A tag id must start with a letter
        $str = "ID_M_"
          . $templateModelId
          . "_P_"
          . $templatePropertySetId;
      }
    }

    return($str);
  }

  // Render the tag id of the inner part of the model
  // That is the region defined by the containers
  function renderInnerTagID($templateModelId) {
    $str = '';

    if ($templateModelId) {
      if ($templateModel = $this->selectById($templateModelId)) {
        $innerTemplatePropertySetId = $templateModel->getInnerTemplatePropertySetId();

        // A tag id must start with a letter
        $str = "ID_M_"
          . $templateModelId
          . "_P_"
          . $innerTemplatePropertySetId;
      }
    }

    return($str);
  }

  // Get the alignment properties of the model
  function getAlignHtmlProperties($templateModelId) {
    $str = '';

    if ($templateModelId) {
      if ($templateModel = $this->selectById($templateModelId)) {
        $templateModelId = $templateModel->getId();
        $templatePropertySetId = $templateModel->getTemplatePropertySetId();

        // Get the name and value of the property
        $properties = $this->templatePropertySetUtils->getProperties($templatePropertySetId);
        $name = "ALIGNMENT";
        if (isset($properties[$name])) {
          $value = $properties[$name];
          $cssValue = 'text-align:' . $this->templatePropertySetUtils->getCssValue($name, $value, $templatePropertySetId) . ';';
          $str .= $cssValue;
        }

        $name = "MARGIN";
        if (isset($properties[$name])) {
          $value = $properties[$name];
          $cssValue = ' margin:' . $this->templatePropertySetUtils->getCssValue($name, $value, $templatePropertySetId) . ';';
          $str .= $cssValue;
        }
      }
    }

    return($str);
  }

  // Render the model properties for an html output
  function renderHtmlProperties($templateModelId) {
    $str = '';

    if ($templateModelId) {
      if ($templateModel = $this->selectById($templateModelId)) {
        $templateModelId = $templateModel->getId();
        $templatePropertySetId = $templateModel->getTemplatePropertySetId();
        $innerTemplatePropertySetId = $templateModel->getInnerTemplatePropertySetId();

        // The first style is sometimes not seen by the browsers
        // Specially when the styles are stored in a remote file
        // Therefore define a dummy one */
        $str = "\n.dummy { }";

        // Render the properties for the model
        $strProperties = $this->templatePropertySetUtils->renderHtmlProperties($templatePropertySetId);
        if ($strProperties) {
          $tagID = $this->renderTagID($templateModelId);
          $str .= "\n" . '.' . $tagID . ' { ' . $strProperties . ' }';

          // Apply the normal text properties to the links
          $strTextProperties = $this->templatePropertySetUtils->renderHtmlTextProperties($templatePropertySetId);
          if (trim($strTextProperties)) {
            $str .= ' .' . $tagID . ' a { ' . $strTextProperties . ' }';
          }

          // Apply the link specific properties to the links
          $str .= ' ' . $this->templatePropertySetUtils->renderHtmlLinkProperties(".$tagID", $templatePropertySetId);

          // Add the property set id to allow for the editing of the line of properties
          // by the property editor
          $str .= ' /* TPS_ID_' . $templatePropertySetId . ' */';
        }

        // Render the properties for the inner model
        $strProperties = $this->templatePropertySetUtils->renderHtmlProperties($innerTemplatePropertySetId);
        if ($strProperties) {
          $innerTagID = $this->renderInnerTagID($templateModelId);
          $str .= "\n" . '.' . $innerTagID . ' { ' . $strProperties . ' }';

          // Apply the normal text properties to the links
          $strTextProperties = $this->templatePropertySetUtils->renderHtmlTextProperties($innerTemplatePropertySetId);
          if (trim($strTextProperties)) {
            $str .= ' .' . $innerTagID . ' a { ' . $strTextProperties . ' }';
          }

          // Apply the link specific properties to the links
          $str .= ' ' . $this->templatePropertySetUtils->renderHtmlLinkProperties(".$innerTagID", $innerTemplatePropertySetId);

          // Add the property set id to allow for the editing of the line of properties
          // by the property editor
          $str .= ' /* TPS_ID_' . $innerTemplatePropertySetId . ' */';
        }
      }
    }

    return($str);
  }

  // Render the properties of the model, its containers and their elements
  function renderAllHtmlProperties($templateModelId) {
    $str = '';

    $str .= $this->renderHtmlProperties($templateModelId);

    if ($templateContainers = $this->templateContainerUtils->selectByTemplateModelId($templateModelId)) {
      foreach ($templateContainers as $templateContainer) {
        $templateContainerId = $templateContainer->getId();
        $str .= $this->templateContainerUtils->renderHtmlProperties($templateContainerId);

        if ($templateElements = $this->templateElementUtils->selectByTemplateContainerId($templateContainerId)) {
          foreach ($templateElements as $templateElement) {
            $templateElementId = $templateElement->getId();
            $str .= $this->templateElementUtils->renderHtmlProperties($templateElementId, true);
          }
        }
      }
    }

    return($str);
  }

  // Render a model
  function renderWebsiteModel($templateModelId) {
    $str = $this->render($templateModelId);

    return($str);
  }

  // Cache the css properties file
  function cacheCssFile($templateModelId) {
    global $gDataPath;

    // Get the default model if none is specified
    if (!$templateModelId) {
      $templateModelId = $this->templateUtils->getComputerDefault();
    }

    if ($templateModelId) {
      $this->templatePropertySetUtils->loadPropertyTypes();

      $properties = $this->renderAllHtmlProperties($templateModelId);

      $properties .= $this->templatePageUtils->renderAllHtmlProperties($templateModelId);

      $filename = $this->templateUtils->getModelCssPath($templateModelId);

      if ($filename) {
        LibFile::writeString($filename, $properties);

        // Save the same properties file to be used by the popup windows
        // There is one popup cache css file for all the models
        // and it must not be cached when caching models other than the default one
        $defaultModelId = $this->templateUtils->getComputerDefault();
        if ($templateModelId == $defaultModelId) {
          LibFile::writeString($filename, $properties);
        }
      }
    }
  }

  // Check that all containers have their width in the same unit,
  // either all in pixels or all in percentage
  function sameContainersWidthUnit($templateModelId) {
    $inPixel = false;
    $inPercentage = false;

    if ($templateContainers = $this->templateContainerUtils->selectByTemplateModelId($templateModelId)) {
      foreach ($templateContainers as $templateContainer) {
        $templatePropertySetId = $templateContainer->getTemplatePropertySetId();
        if ($templatePropertySet = $this->templatePropertySetUtils->selectById($templatePropertySetId)) {
          if ($templateProperty = $this->templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, 'WIDTH')) {
            $cellWidth = $templateProperty->getValue();
            if ($cellWidth) {
              if (strstr($cellWidth, '%')) {
                $inPercentage = true;
              } else {
                $inPixel = true;
              }
            }
          }
        }
      }
    }

    if ($inPixel && $inPercentage) {
      return(false);
    } else {
      return(true);
    }
  }

  // Check if a model has children models
  function hasChildren($templateModelId) {
    if ($templateModels = $this->selectByParentId($templateModelId)) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check that the two rows of containers, one of a child model and the other one of its parent model,
  // have the same total width
  function sameChildParentContainerRowsWidth($templateModelId, $parentId, $row) {
    $rowWidth = 0;
    if ($templateContainers = $this->templateContainerUtils->selectByModelIdAndRow($templateModelId, $row)) {
      foreach ($templateContainers as $templateContainer) {
        $templatePropertySetId = $templateContainer->getTemplatePropertySetId();
        if ($templatePropertySet = $this->templatePropertySetUtils->selectById($templatePropertySetId)) {
          if ($templateProperty = $this->templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, 'WIDTH')) {
            $cellWidth = $templateProperty->getValue();
            $cellWidth = $cellWidth + $this->templateContainerUtils->getLeftAndRightBorderWidth($templateContainerId);
            $rowWidth = $rowWidth + $cellWidth;
          }
        }
      }
    }

    $parentRowWidth = 0;
    if ($rowWidth) {
      if ($templateContainers = $this->templateContainerUtils->selectByModelIdAndRow($parentId, $row)) {
        foreach ($templateContainers as $templateContainer) {
          $templatePropertySetId = $templateContainer->getTemplatePropertySetId();
          if ($templatePropertySet = $this->templatePropertySetUtils->selectById($templatePropertySetId)) {
            if ($templateProperty = $this->templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, 'WIDTH')) {
              $cellWidth = $templateProperty->getValue();
              $cellWidth = $cellWidth + $this->templateContainerUtils->getLeftAndRightBorderWidth($templateContainerId);
              $parentRowWidth = $parentRowWidth + $cellWidth;
            }
          }
        }
      }
    }

    if ($rowWidth != $parentRowWidth) {
      return(false);
    } else {
      return(true);
    }
  }

  // Check that all container rows of a model have the same width
  function sameContainerRowsWidth($templateModelId) {
    if ($templateContainers = $this->templateContainerUtils->selectByTemplateModelId($templateModelId)) {
      $row = -1;
      $previousRowWidth = 0;
      $rowWidth = 0;
      foreach ($templateContainers as $templateContainer) {
        $templateContainerId = $templateContainer->getId();
        $previousRow = $row;
        $row = $templateContainer->getRow();
        if ($row > $previousRow) {
          if ($previousRowWidth && $rowWidth != $previousRowWidth) {
            return(false);
          }
          $previousRowWidth = $rowWidth;
          $rowWidth = 0;
        }
        $templatePropertySetId = $templateContainer->getTemplatePropertySetId();
        if ($templatePropertySet = $this->templatePropertySetUtils->selectById($templatePropertySetId)) {
          if ($templateProperty = $this->templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, 'WIDTH')) {
            $cellWidth = $templateProperty->getValue();
            $cellWidth = $cellWidth + $this->templateContainerUtils->getLeftAndRightBorderWidth($templateContainerId);
            $rowWidth = $rowWidth + $cellWidth;
          }
        }
      }

      if ($rowWidth && $previousRowWidth && $rowWidth != $previousRowWidth) {
        return(false);
      }
    }
    return(true);
  }

  // Get the total width of the model
  function getTotalWidth($templateModelId) {
    $modelWidth = 0;

    // Process the total width of each row
    if ($templateContainers = $this->templateContainerUtils->selectByTemplateModelId($templateModelId)) {
      $row = -1;
      $rowWidth = 0;
      foreach ($templateContainers as $templateContainer) {
        $previousRow = $row;
        $row = $templateContainer->getRow();
        if ($row > $previousRow) {
          $rowWidth = 0;
        }
        $templatePropertySetId = $templateContainer->getTemplatePropertySetId();
        if ($templatePropertySet = $this->templatePropertySetUtils->selectById($templatePropertySetId)) {
          if ($templateProperty = $this->templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, 'WIDTH')) {
            $cellWidth = $templateProperty->getValue();
            // The total model width is 100% if its containers widths is expressed in percentage
            if (strstr($cellWidth, '%')) {
              return('100%');
            }
            $rowWidth = $rowWidth + $cellWidth;
          }
        }
        $modelWidth = max($modelWidth, $rowWidth);
      }
    }

    if ($modelWidth) {
      $modelWidth .= 'px';
    }

    return($modelWidth);
  }

  // Get the alignment of the model
  function getInnerAlignment($templateModelId) {
    $alignment = '';

    if ($templateModel = $this->selectById($templateModelId)) {
      $templatePropertySetId = $templateModel->getInnerTemplatePropertySetId();

      if ($templatePropertySet = $this->templatePropertySetUtils->selectById($templatePropertySetId)) {
        if ($templateProperty = $this->templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, 'ALIGNMENT')) {
          $alignment = $templateProperty->getValue();
        }
      }
    }

    return($alignment);
  }

  // Render the inner alignment
  function renderInnerAlignment($templateModelId) {
    $str = '';

    $modelAlignment = $this->getInnerAlignment($templateModelId);

    if ($modelAlignment) {
      $str = "text-align:$modelAlignment;";
    }

    return($str);
  }

  // Preview a model
  function preview($templateModelId) {
    $str = $this->render($templateModelId);

    return($str);
  }

  // Preview a container
  function previewContainer($templateModelId, $templateContainerId) {
    $str = $this->renderModelHeader($templateModelId);

    $str .= $this->getContainerContent($templateContainerId);

    return($str);
  }

  // Preview an element
  function previewElement($templateModelId, $templateElementId, $forcePreview = false) {
    $str = $this->renderModelHeader($templateModelId);

    if ($templateElement = $this->templateElementUtils->selectById($templateElementId)) {
      if ($forcePreview || !$templateElement->getHide()) {
        $str .= $this->getElementContent($templateElementId);
      }
    }

    return($str);
  }

  // Render the css for the model
  function renderModelCss($templateModelId) {
    global $gTemplateDesignUrl;

    $strCssLinks = '';

    if ($templateModel = $this->selectById($templateModelId)) {
      $parentId = $templateModel->getParentId();

      // The parent css link, if any, must be rendered before the child's one
      // This is to ensure the css properties of the child model overwrite the ones of the parent model if any
      if ($parentId) {
        $strPropertyUrl = $this->templateUtils->getModelCssUrl($parentId);
        $strCssLinks .= "\n<link href='$strPropertyUrl' rel='stylesheet' type='text/css' />";
      }
      $strPropertyUrl = $this->templateUtils->getModelCssUrl($templateModelId);
      $strCssLinks .= "\n<link href='$strPropertyUrl' rel='stylesheet' type='text/css' />";
    }

    $strCssLinks .= "<link href='$gTemplateDesignUrl/data/css/default.css' rel='stylesheet' type='text/css' />";

    return($strCssLinks);
  }

  // Render the header for the model
  function renderModelHeader($templateModelId) {
    global $gHomeUrl;
    global $gTemplateDesignUrl;

    if ($templateModelId == $this->templateUtils->getPhoneEntry()) {
      $isPhoneModel = true;
    } else {
      $isPhoneModel = false;
    }

    $favicon = $this->profileUtils->renderFavicon();

    if ($isPhoneModel) {
      $iPhoneFormatting = <<<HEREDOC
<meta name="format-detection" content="telephone=no" />
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
<meta name="apple-touch-fullscreen" content="yes" />
HEREDOC;
    } else {
      $iPhoneFormatting = '';
    }

    $iPhoneicon = $this->profileUtils->renderIPhoneIcon();

    $strCommonJavascript = $this->templateUtils->renderCommonJavascripts();

    if (!$isPhoneModel) {
      $strLexiconTooltip = $this->lexiconEntryUtils->renderLexiconJsLibrary();

      $strTooltip = <<<HEREDOC
<script type="text/javascript">
$(document).ready(function() {
  $(".tooltip").wTooltip({
    follow: false,
    fadeIn: 300,
    fadeOut: 500,
    delay: 200,
    style: {
      width: "500px", // Required to avoid the tooltip being displayed off the right
      background: "#ffffff"
    }
  });
});
</script>
HEREDOC;
    } else {
      $strLexiconTooltip = '';
      $strTooltip = '';
    }

    if ($templateModel = $this->selectById($templateModelId)) {
      $parentId = $templateModel->getParentId();

      // Render the header (DHTML javascript code) of the navigation elements if any
      $strElementHeader = '';
      if ($templateContainers = $this->templateContainerUtils->selectByTemplateModelId($templateModelId)) {
        foreach ($templateContainers as $templateContainer) {
          $templateContainerId = $templateContainer->getId();
          if ($templateElements = $this->templateElementUtils->selectByTemplateContainerId($templateContainerId)) {
            foreach ($templateElements as $templateElement) {
              $elementType = $templateElement->getElementType();
              $objectId = $templateElement->getObjectId();
              $strElementHeader .= $this->templateElementUtils->renderHeader($elementType, $objectId);
            }
          }
        }
      }
      if ($templateContainers = $this->templateContainerUtils->selectByTemplateModelId($parentId)) {
        foreach ($templateContainers as $templateContainer) {
          $templateContainerId = $templateContainer->getId();
          if ($templateElements = $this->templateElementUtils->selectByTemplateContainerId($templateContainerId)) {
            foreach ($templateElements as $templateElement) {
              $elementType = $templateElement->getElementType();
              $objectId = $templateElement->getObjectId();
              $strElementHeader .= $this->templateElementUtils->renderHeader($elementType, $objectId);
            }
          }
        }
      }
    }

    $strMeta = $this->renderMetaDataTags();

    $str = <<<HEREDOC
<head>
$strMeta
$favicon
$iPhoneFormatting
$iPhoneicon
$strCommonJavascript
$strTooltip
$strLexiconTooltip
$strElementHeader
<base href='$gHomeUrl' />
</head>
HEREDOC;

    return($str);
  }

  // Render the model
  function render($templateModelId) {
    global $gCommonImagesUrl;
    global $HTTP_HOST;
    global $REQUEST_URI;

    $this->loadLanguageTexts();

    if (!$templateModel = $this->selectById($templateModelId)) {
      $websiteName = $this->profileUtils->getProfileValue("website.name");
      $siteEmail = $this->profileUtils->getProfileValue("website.email");

      $emailSubject = $this->mlText[8] . ' ' . $templateModelId . ' ' . $this->mlText[9] . ' ' . $websiteName;

      $HTTP_REFERER = LibEnv::getEnvSERVER('HTTP_REFERER');

      $emailBody = $this->mlText[10] . ' ' . $websiteName
        . " " . $this->mlText[11] . ' ' . $HTTP_HOST . $REQUEST_URI . ' ' . $this->mlText[12] . ' ' . $templateModelId
        . " " . $this->mlText[13] . ' ' . $templateModelId . ' ' . $this->mlText[14]
        . " " . $this->mlText[16] . ' ' . $templateModelId . ' ' . $this->mlText[17];

      if ($HTTP_REFERER) {
        $emailBody .= " " . $this->mlText[15] . ' ' . $HTTP_REFERER;
      }

      reportError($emailBody);

      return;
    }

    $name = $templateModel->getName();
    $modelType = $templateModel->getModelType();
    $parentId = $templateModel->getParentId();

    // Get the model tag id
    $modelTagID = $this->renderTagID($templateModelId);

    // Get the inner model tag id
    $modelInnerTagID = $this->renderInnerTagID($templateModelId);

    if ($parentId) {
      // Get the parent model tag id
      $parentModelTagID = $this->renderTagID($parentId);

      // Get the parent inner model tag id
      $parentModelInnerTagID = $this->renderInnerTagID($parentId);
    } else {
      $parentModelTagID = '';
      $parentModelInnerTagID = '';
    }

    // Get the total width for the model
    // It is the one of its parent if any
    if ($parentId) {
      $modelWidth = $this->getTotalWidth($parentId);
    } else {
      $modelWidth = $this->getTotalWidth($templateModelId);
    }
    if ($modelWidth) {
      $strModelWidth = "width:$modelWidth;";
    } else {
      $strModelWidth = '';
    }

    // Alignment of the page
    $strAlignProperty = $this->getAlignHtmlProperties($templateModelId);
    // It overwrites the one of its parent if any
    if ($parentId && !$strAlignProperty) {
      $strAlignProperty = $this->getAlignHtmlProperties($parentId);
    }

    // Get the alignment for the model
    $strModelAlignment = $this->renderInnerAlignment($templateModelId);
    // It overwrites the one of its parent if any
    if ($parentId && !$strModelAlignment) {
      $strModelAlignment = $this->renderInnerAlignment($parentId);
    }

    $strBody = "<body class='$modelTagID $parentModelTagID'>";

    $facebookApplicationId = $this->profileUtils->getFacebookApplicationId();
    if ($facebookApplicationId) {
      $strBody .= $this->facebookUtils->renderLibrary();
    }

    $linkedinApiKey = $this->profileUtils->getLinkedinApiKey();
    if ($linkedinApiKey) {
      $strBody .= $this->linkedinUtils->renderLibrary();
    }

    $strBody .= "\n<div style='$strModelWidth $strAlignProperty'>";

    $strBody .= "\n<table class='$modelInnerTagID $parentModelInnerTagID' border='0' cellspacing='0' cellpadding='0' style='$strModelAlignment $strModelWidth margin:0px; padding:0px;'><tbody><tr><td>";

    $previousRow = -1;
    $nbRows = $this->templateContainerUtils->getNbRows($templateModelId);
    for ($row = 0; $row < $nbRows; $row++) {
      $nbCells = $this->templateContainerUtils->getNbCells($templateModelId, $row);
      for ($cell = 0; $cell < $nbCells; $cell++) {
        if ($templateContainer = $this->templateContainerUtils->selectByModelIdAndRowAndCell($templateModelId, $row, $cell)) {
          if ($templateContainer) {
            if ($row > $previousRow) {
              if ($previousRow > -1) {
                $strBody .= "\n</tr></tbody></table>";
              }
              // The height is set to 100% to have all the containers of a row with the same height
              $strBody .= "\n<table border='0' cellspacing='0' cellpadding='0' style='$strModelWidth height:100%;'><tbody><tr>";
            }

            $templateContainerId = $templateContainer->getId();
            if (strstr($strModelWidth, '%')) {
              $percentageContainerWidth = "width:" . $this->templateContainerUtils->getWidth($templateContainerId) . ';';
            } else {
              $percentageContainerWidth = '';
            }
            $strBody .= "\n<td valign='top' style='$percentageContainerWidth'>";

            // Get the container's elements
            if ($parentId && $parentTemplateContainer = $this->templateContainerUtils->selectByModelIdAndRowAndCell($parentId, $row, $cell)) {
              $parentTemplateContainerId = $parentTemplateContainer->getId();
              $strBody .= $this->getContainerContent($templateContainerId, $parentTemplateContainerId);
            } else {
              $strBody .= $this->getContainerContent($templateContainerId);
            }

            $strBody .= "\n</td>";
            $previousRow = $row;
          }
        }
      }
    }

    $strBody .= "\n</td></tr></tbody></table>";
    $strBody .= "\n</div>";

    $strBody .= $this->profileUtils->getGoogleAnalytics();

    $strBody .= $this->documentUtils->getIssuuSmartlook();

    $strBody .= $this->profileUtils->getJsBodyEnd();

    $strBody .= $this->renderModelCss($templateModelId);

    $strBody .= "\n</body>";

    // Note that the first line of the page must be the doctype one
    // Otherwise IE 6 turns into quirks mode with its well known "IE box model bug"
    // So no such line as xml version="1.0" encoding="ISO-8859-1"
    // shall be the first line
    $str = <<<HEREDOC
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#" TEMPLATE_FACEBOOK_XMLNS lang="en">
HEREDOC;

    $str .= "\n" . $this->renderModelHeader($templateModelId);

    $str .= $strBody;

    $str .= "\n</html>";

    return($str);
  }

  function getContainerContent($templateContainerId, $parentTemplateContainerId = '') {

    $containerTagID = $this->templateContainerUtils->renderTagID($templateContainerId);
    if ($parentTemplateContainerId) {
      $parentContainerTagID = $this->templateContainerUtils->renderTagID($parentTemplateContainerId);
    } else {
      $parentContainerTagID = '';
    }

    $containerAlignment = $this->templateContainerUtils->getAlignment($templateContainerId);
    if ($parentTemplateContainerId && !$containerAlignment) {
      $containerAlignment = $this->templateContainerUtils->getAlignment($parentTemplateContainerId);
    }

    $containerHeight = $this->templateContainerUtils->getHeight($templateContainerId);
    if ($parentTemplateContainerId && !$containerHeight) {
      $containerHeight = $this->templateContainerUtils->getHeight($parentTemplateContainerId);
    }

    // Have an extensible height with a minimum value
    if ($containerHeight) {
      $strHeight = 'min-height: ' . $containerHeight . '; height: auto !important; height: ' . $containerHeight . ';';
    } else {
      $strHeight = '';
    }

    $containerRoundCornerClass = $this->templateContainerUtils->renderRoundCornerClass($templateContainerId);
    if ($parentTemplateContainerId && !$containerRoundCornerClass) {
      $containerRoundCornerClass = $this->templateContainerUtils->renderRoundCornerClass($parentTemplateContainerId);
    }

    $containerExternalMargin = $this->templateContainerUtils->renderExternalMargin($templateContainerId);
    if ($parentTemplateContainerId && !$containerExternalMargin) {
      $containerExternalMargin = $this->templateContainerUtils->renderExternalMargin($parentTemplateContainerId);
    }

    // The width is set in the div when specified in pixels, but it is set in the parent table cell when specified in percentage. When specified in percentage the div must have a 100% width to inherit the one from its parent cell. When specified in pixels the div must NOT have a 100% width as it would overwrite the one specified in the css.
    if (strstr($this->templateContainerUtils->getWidth($templateContainerId), '%')) {
      $strWidth = "width:100%;";
    } else {
      $strWidth = '';
    }
    // The hidden overflow is a hack to work around the margin collapse issue showing a gap
    // between adjacent enclosed div elements
    $str = "\n<div style='overflow:hidden; $strWidth $containerExternalMargin $strHeight' class='$containerTagID $parentContainerTagID $containerRoundCornerClass'>";

    // The word wrap styling is a hack to make sure IE6 does not expand
    // fixed size elements if their content does not fit
    $containerInternalMargin = $this->templateContainerUtils->renderInternalMargin($templateContainerId);
    if ($parentTemplateContainerId && !$containerInternalMargin) {
      $containerInternalMargin = $this->templateContainerUtils->renderInternalMargin($parentTemplateContainerId);
    }

    $str .= "\n<div style='$containerInternalMargin $strHeight'>";

    if ($containerAlignment) {
      $strAlignStyle = "style='text-align:$containerAlignment;'";
    } else {
      $strAlignStyle = '';
    }


    // The elements of the parent container, if any,  are displayed first
    if ($parentTemplateContainerId) {
      if ($templateElements = $this->templateElementUtils->selectByTemplateContainerId($parentTemplateContainerId)) {
        foreach ($templateElements as $templateElement) {
          $templateElementId = $templateElement->getId();
          if (!$templateElement->getHide()) {
            $str .= $this->getElementContent($templateElementId, $strAlignStyle);
          }
        }
      }
    }

    if ($templateElements = $this->templateElementUtils->selectByTemplateContainerId($templateContainerId)) {
      foreach ($templateElements as $templateElement) {
        $templateElementId = $templateElement->getId();
        if (!$templateElement->getHide()) {
          $str .= $this->getElementContent($templateElementId, $strAlignStyle);
        }
      }
    }

    $str .= "\n</div>";

    $str .= "\n</div>";

    return($str);
  }

  function getElementContent($templateElementId, $strAlignStyle = '') {
    $str = '';

    if ($templateElement = $this->templateElementUtils->selectById($templateElementId)) {
      $elementType = $templateElement->getElementType();
      $objectId = $templateElement->getObjectId();
      $elementTagId = $this->templateElementUtils->renderTagID($templateElementId);
      $templateElementContent = $this->templateElementUtils->renderContent($templateElementId, $elementType, $objectId);
      $str = "\n<div $strAlignStyle class='$elementTagId'>"
        . $templateElementContent
        . "</div>";
    }

    return($str);
  }

  // Render the meta tags
  function renderMetaDataTags() {
    $defaultLanguage = $this->languageUtils->getDefaultLanguageCode();

    $description = $this->profileUtils->getWebsiteDescription();
    $keywords = $this->profileUtils->getWebsiteKeywords();

    $facebookApplicationId = $this->profileUtils->getFacebookApplicationId();

    $str = <<<HEREDOC
<title>TEMPLATE_CONTENT_TITLE</title>
<meta http-equiv='content-type' content='text/html; charset=iso-8859-1' />
<meta http-equiv='content-script-type' content='text/javascript' />
<meta http-equiv='content-style-type' content='text/css' />
<meta http-equiv='imagetoolbar' content='false' />
<meta http-equiv='content-language' content='$defaultLanguage' />
<meta name='robots' content='ALL' />
<meta name='MSSmartTagsPreventParsing' content='true' />
<meta name='description' content='$description' />
<meta name='keywords' content='$keywords' />
<meta property="og:site_name" content="TEMPLATE_CONTENT_WEBSITE_NAME" />
<meta property="og:title" content="TEMPLATE_CONTENT_TITLE" />
<meta property="og:description" content="TEMPLATE_CONTENT_TITLE" />
<meta property="og:url" content="TEMPLATE_CONTENT_URL" />
HEREDOC;

    if ($facebookApplicationId) {
      $str .= <<<HEREDOC
\n<meta property='fb:app_id' content='$facebookApplicationId' />
HEREDOC;
    }

    return($str);
  }

}

?>
