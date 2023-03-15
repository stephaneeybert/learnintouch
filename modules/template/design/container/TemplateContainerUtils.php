<?php

class TemplateContainerUtils extends TemplateContainerDB {

  var $languageUtils;
  var $templatePropertySetUtils;
  var $templateModelUtils;
  var $templateElementUtils;

  function __construct() {
    parent::__construct();
  }

  function getPropertyTypes() {
    $propertyTypes = array(
      'ALIGNMENT',
      'VERTICAL_ALIGNMENT',
      'WIDTH',
      'HEIGHT',
      'MARGIN',
      'MARGIN_POSITION',
      'PADDING',
      'PADDING_POSITION',
      'BORDER_STYLE',
      'BORDER_SIZE',
      'BORDER_COLOR',
      'BORDER_POSITION',
      'ROUND_CORNER',
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

  // Get the number of rows
  function getNbRows($templateModelId) {
    $nbRows = $this->getNextRowNumber($templateModelId);

    return($nbRows);
  }

  // Get the next available row number
  function getNextRowNumber($templateModelId) {
    $rowNumber = 0;

    if ($templateContainers = $this->selectByTemplateModelId($templateModelId)) {
      $nbCount = count($templateContainers);
      if ($nbCount > 0) {
        $templateContainer = $templateContainers[$nbCount - 1];
        $rowNumber = $templateContainer->getRow() + 1;
      }
    }

    return($rowNumber);
  }

  // Get the number of cells in a row
  function getNbCells($templateModelId, $row) {
    $nbCells = $this->getNextCellNumber($templateModelId, $row);

    return($nbCells);
  }

  // Get the next available cell number for a row
  function getNextCellNumber($templateModelId, $row) {
    $cellNumber = 0;

    if ($templateContainers = $this->selectByModelIdAndRow($templateModelId, $row)) {
      $nbCount = count($templateContainers);
      if ($nbCount > 0) {
        $templateContainer = $templateContainers[$nbCount - 1];
        $cellNumber = $templateContainer->getCell() + 1;
      }
    }

    return($cellNumber);
  }

  // Reset the cell numbers of a row of containers
  function NOT_USED_resetContainerRowCells($templateModelId, $row) {
    if ($templateContainers = $this->selectByModelIdAndRow($templateModelId, $row)) {
      if (count($templateContainers) > 0) {
        $cell = 0;
        foreach ($templateContainers as $templateContainer) {
          $templateContainer->setCell($cell);
          $this->update($templateContainer);
          $cell = $cell + 1;
        }
      }
    }
  }

  // Reset the row numbers of the rows of containers
  function resetContainerRowAndCellNumbers($templateModelId) {
    if ($templateContainers = $this->selectByTemplateModelId($templateModelId)) {
      $row = -1;
      $newRow = 0;
      $newCell = 0;
      foreach ($templateContainers as $templateContainer) {
        $previousRow = $row;
        $row = $templateContainer->getRow();
        if ($row > $previousRow) {
          $newRow = $newRow + 1;
          $newCell = 0;
        }
        $templateContainer->setRow($newRow);
        $templateContainer->setCell($newCell);
        $this->update($templateContainer);
        $newCell = $newCell + 1;
      }
    }
  }

  // Get the next container
  function selectNext($id) {
    if ($templateContainer = $this->selectById($id)) {
      $row = $templateContainer->getRow();
      $cell = $templateContainer->getCell();
      $templateModelId = $templateContainer->getTemplateModelId();
      if ($templateContainer = $this->selectByNextCell($templateModelId, $row, $cell)) {
        return($templateContainer);
      }
    }
  }

  // Get the previous container
  function selectPrevious($id) {
    if ($templateContainer = $this->selectById($id)) {
      $row = $templateContainer->getRow();
      $cell = $templateContainer->getCell();
      $templateModelId = $templateContainer->getTemplateModelId();
      if ($templateContainer = $this->selectByPreviousCell($templateModelId, $row, $cell)) {
        return($templateContainer);
      }
    }
  }

  // Swap the curent container with the next one
  function swapWithNext($id) {
    $currentObject = $this->selectById($id);
    $currentRow = $currentObject->getRow();
    $currentCell = $currentObject->getCell();

    // Get the next object and its list order
    if (!$nextObject = $this->selectNext($id)) {
      return;
    }
    $nextRow = $nextObject->getRow();
    $nextCell = $nextObject->getCell();

    // Update the list orders
    $currentObject->setRow($nextRow);
    $currentObject->setCell($nextCell);
    $this->update($currentObject);
    $nextObject->setRow($currentRow);
    $nextObject->setCell($currentCell);
    $this->update($nextObject);
  }

  // Swap the curent container with the previous one
  function swapWithPrevious($id) {
    $currentObject = $this->selectById($id);
    $currentRow = $currentObject->getRow();
    $currentCell = $currentObject->getCell();

    // Get the previous object and its list order
    if (!$previousObject = $this->selectPrevious($id)) {
      return;
    }
    $previousRow = $previousObject->getRow();
    $previousCell = $previousObject->getCell();

    // Update the list orders
    $currentObject->setRow($previousRow);
    $currentObject->setCell($previousCell);
    $this->update($currentObject);
    $previousObject->setRow($currentRow);
    $previousObject->setCell($currentCell);
    $this->update($previousObject);
  }

  // Add a container to a row
  function addTemplateContainer($templateContainerId) {
    if ($templateContainer = $this->selectById($templateContainerId)) {
      $templateModelId = $templateContainer->getTemplateModelId();
      $row = $templateContainer->getRow();
      $this->templateModelUtils->addTemplateContainer($templateModelId, $row);
    }
  }

  // Delete a container and all its elements and properties
  function deleteTemplateContainer($templateContainerId) {
    if ($templateContainerId) {
      // Delete all the container elements
      if ($templateElements = $this->templateElementUtils->selectByTemplateContainerId($templateContainerId)) {
        foreach ($templateElements as $templateElement) {
          $templateElementId = $templateElement->getId();
          $this->templateElementUtils->deleteElement($templateElementId);
        }
      }

      if ($templateContainer = $this->selectById($templateContainerId)) {
        $templateModelId = $templateContainer->getTemplateModelId();
      } else {
        $templateModelId = '';
      }

      // Delete the container
      $this->delete($templateContainerId);

      // Delete the properties
      if ($templateContainer = $this->selectById($templateContainerId)) {
        $templatePropertySetId = $templateContainer->getTemplatePropertySetId();
        if ($templatePropertySetId) {
          $this->templatePropertySetUtils->deleteTemplatePropertySet($templatePropertySetId);
        }
      }
    }

    $this->resetContainerRowAndCellNumbers($templateModelId);
  }

  // Duplicate a container
  function duplicate($templateContainer, $templateModelId) {
    $templateContainer->setTemplateModelId($templateModelId);
    $lastInsertTemplatePropertySetId = $this->templatePropertySetUtils->duplicate($templateContainer->getTemplatePropertySetId());
    $templateContainer->setTemplatePropertySetId($lastInsertTemplatePropertySetId);
    $this->insert($templateContainer);
    $lastInsertTemplateContainerId = $this->getLastInsertId();

    // Duplicate the elements
    $templateElements = $this->templateElementUtils->selectByTemplateContainerId($templateContainer->getId());
    foreach ($templateElements as $templateElement) {
      $this->templateElementUtils->duplicate($templateElement, $lastInsertTemplateContainerId);
    }
  }

  // Export a container
  function exportXML($xmlNode, $templateContainerId) {
    if ($templateContainer = $this->selectById($templateContainerId)) {
      $row = $templateContainer->getRow();
      $cell = $templateContainer->getCell();

      $xmlChildNode = $xmlNode->addChild(TEMPLATE_CONTAINER);
      $attributes = array("row" => $row, "cell" => $cell);
      if (is_array($attributes)) {
        foreach ($attributes as $aName => $aValue) {
          $xmlChildNode->addAttribute($aName, $aValue);
        }
      }

      // Export the property set
      $templatePropertySetId = $templateContainer->getTemplatePropertySetId();
      $this->templatePropertySetUtils->exportXML($xmlChildNode, $templatePropertySetId);

      // Export the elements
      $templateElements = $this->templateElementUtils->selectByTemplateContainerId($templateContainerId);
      foreach ($templateElements as $templateElement) {
        $templateElementId = $templateElement->getId();
        $this->templateElementUtils->exportXML($xmlChildNode, $templateElementId);
      }
    }
  }

  // Import a container
  function importXML($xmlNode, $lastInsertTemplateModelId) {

    $row = $xmlNode->attributes()["row"];
    $cell = $xmlNode->attributes()["cell"];

    // Create the container
    $templateContainer = new TemplateContainer();
    $templateContainer->setRow($row);
    $templateContainer->setCell($cell);
    $templateContainer->setTemplateModelId($lastInsertTemplateModelId);
    $this->insert($templateContainer);
    $lastInsertTemplateContainerId = $this->getLastInsertId();

    $xmlChildNodes = $xmlNode->children();
    foreach ($xmlChildNodes as $xmlChildNode) {
      $name = $xmlChildNode->getName();
      if ($name == TEMPLATE_PROPERTY_SET) {
        // Create the property set
        $lastInsertTemplatePropertySetId = $this->templatePropertySetUtils->importXML($xmlChildNode);

        // Link the container and the property set
        $templateContainer->setId($lastInsertTemplateContainerId);
        $templateContainer->setTemplatePropertySetId($lastInsertTemplatePropertySetId);
        $this->update($templateContainer);
      } else if ($name == TEMPLATE_ELEMENT) {
        // Create the element
        $this->templateElementUtils->importXML($xmlChildNode, $lastInsertTemplateContainerId);
      }
    }
  }

  // Get the alignment of the container
  function getAlignment($templateContainerId) {
    $alignment = '';

    if ($templateContainer = $this->selectById($templateContainerId)) {
      $templatePropertySetId = $templateContainer->getTemplatePropertySetId();

      if ($templatePropertySet = $this->templatePropertySetUtils->selectById($templatePropertySetId)) {
        if ($templateProperty = $this->templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, 'ALIGNMENT')) {
          $alignment = $templateProperty->getValue();
        }
      }
    }

    return($alignment);
  }

  // Get the total width of the left and right borders if any
  function getLeftAndRightBorderWidth($templateContainerId) {
    $width = '';

    if ($templateContainer = $this->selectById($templateContainerId)) {
      $templatePropertySetId = $templateContainer->getTemplatePropertySetId();

      if ($templatePropertySet = $this->templatePropertySetUtils->selectById($templatePropertySetId)) {
        if ($templateProperty = $this->templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, 'BORDER_SIZE')) {
          $borderWidth = $templateProperty->getValue();
          if ($borderWidth) {
            if ($otherTemplateProperty = $this->templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, "BORDER_POSITION")) {
              $position = $otherTemplateProperty->getValue();
              if (!$position) {
                $width += $borderWidth * 2;
              } else if (strstr($position, 'left')) {
                $width += $borderWidth;
              } else if (strstr($position, 'right')) {
                $width += $borderWidth;
              }
            } else {
              $width += $borderWidth * 2;
            }
          }
        }
      }
    }

    return($width);
  }

  // Get the external margin of the container
  function renderExternalMargin($templateContainerId) {
    // By default the margin is set to zero
    $margin = 0;

    if ($templateContainer = $this->selectById($templateContainerId)) {
      $templatePropertySetId = $templateContainer->getTemplatePropertySetId();

      if ($templatePropertySet = $this->templatePropertySetUtils->selectById($templatePropertySetId)) {
        if ($templateProperty = $this->templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, 'MARGIN')) {
          $margin = $templateProperty->getValue();
          if (!$margin) {
            $margin = '0';
          }
          if ($otherTemplateProperty = $this->templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, "MARGIN_POSITION")) {
            $otherValue = $otherTemplateProperty->getValue();
            $margin = $this->templatePropertySetUtils->renderPosition($margin, $otherValue);
          } else {
            $margin .= 'px';
          }
          $margin = "margin: $margin;";
        }
      }
    }

    return($margin);
  }

  // Render the internal margin of the container
  function renderInternalMargin($templateContainerId) {
    // By default the margin is set to zero
    $margin = 0;

    if ($templateContainer = $this->selectById($templateContainerId)) {
      $templatePropertySetId = $templateContainer->getTemplatePropertySetId();

      if ($templatePropertySet = $this->templatePropertySetUtils->selectById($templatePropertySetId)) {
        if ($templateProperty = $this->templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, 'PADDING')) {
          $margin = $templateProperty->getValue();
          if (!$margin) {
            $margin = '0';
          }
          if ($otherTemplateProperty = $this->templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, "PADDING_POSITION")) {
            $otherValue = $otherTemplateProperty->getValue();
            $margin = $this->templatePropertySetUtils->renderPosition($margin, $otherValue);
          } else {
            $margin .= 'px';
          }
          $margin = "margin: $margin;";
        }
      }
    }

    return($margin);
  }

  // Get the width of a container
  function getWidth($templateContainerId) {
    $width = '';

    if ($templateContainer = $this->selectById($templateContainerId)) {
      $templatePropertySetId = $templateContainer->getTemplatePropertySetId();

      if ($templatePropertySet = $this->templatePropertySetUtils->selectById($templatePropertySetId)) {
        if ($templateProperty = $this->templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, 'WIDTH')) {
          $width = $templateProperty->getValue();
          if (!strstr($width, '%')) {
            $width .= 'px';
          }
        }
      }
    }

    return($width);
  }

  // Get the height of the container
  function getHeight($templateContainerId) {
    $height = '';

    if ($templateContainer = $this->selectById($templateContainerId)) {
      $templatePropertySetId = $templateContainer->getTemplatePropertySetId();

      if ($templatePropertySet = $this->templatePropertySetUtils->selectById($templatePropertySetId)) {
        if ($templateProperty = $this->templatePropertyUtils->selectByTemplatePropertySetIdAndName($templatePropertySetId, 'HEIGHT')) {
          $height = $templateProperty->getValue();
          if (is_numeric($height)) {
            $height .= 'px';
          }
        }
      }
    }

    return($height);
  }

  // Render the tag id
  // The tag id must be unique for each model/container
  function renderTagID($templateContainerId) {
    $str = '';

    if ($templateContainerId) {
      if ($templateContainer = $this->selectById($templateContainerId)) {
        $templateModelId = $templateContainer->getTemplateModelId();
        $templatePropertySetId = $templateContainer->getTemplatePropertySetId();

        // A tag id must start with a letter
        $str = "ID_M_"
          . $templateModelId
          . "_C_"
          . $templateContainerId;
      }
    }

    return($str);
  }

  // Render the class id for some round corners if any
  function renderRoundCornerClass($templateContainerId) {
    $value = '';

    if ($templateContainerId) {
      if ($templateContainer = $this->selectById($templateContainerId)) {
        $templatePropertySetId = $templateContainer->getTemplatePropertySetId();

        $properties = $this->templatePropertySetUtils->getProperties($templatePropertySetId);
        if (isset($properties["ROUND_CORNER"])) {
          $value = $properties["ROUND_CORNER"];
        }
      }
    }

    return($value);
  }

  // Render the container properties for an html output
  function renderHtmlProperties($templateContainerId) {
    $str = '';

    if ($templateContainerId) {
      if ($templateContainer = $this->selectById($templateContainerId)) {
        $templateModelId = $templateContainer->getTemplateModelId();
        $templatePropertySetId = $templateContainer->getTemplatePropertySetId();

        $strProperties = $this->templatePropertySetUtils->renderHtmlProperties($templatePropertySetId, true);
        if ($strProperties) {
          $tagID = $this->renderTagID($templateContainerId);
          $str .= "\n" . '.' . $tagID . ' { ' . $strProperties . ' }';

          // Apply the normal text properties
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
      }
    }

    return($str);
  }

}

?>
