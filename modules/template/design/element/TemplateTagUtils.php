<?php

class TemplateTagUtils extends TemplateTagDB {

  var $mlText;

  var $properties;

  var $languageUtils;
  var $templateElementUtils;
  var $templatePropertySetUtils;
  var $profileUtils;

  function __construct() {
    parent::__construct();
  }

  function getTagNames() {
    $this->loadLanguageTexts();

    $tagNames = array(
      'template_current_page' => $this->mlText[58],
      'template_element' => $this->mlText[69],
      'container' => $this->mlText[12],
      'navlink' => $this->mlText[0],
      'navlink_image' => $this->mlText[43],
      'navbar' => $this->mlText[1],
      'navbar_item' => $this->mlText[2],
      'navbar_item_first' => $this->mlText[56],
      'navbar_item_last' => $this->mlText[57],
      'navbar_item_image' => $this->mlText[42],
      'navmenu' => $this->mlText[3],
      'menuBar' => $this->mlText[4],
      'menuBarItem' => $this->mlText[53],
      'menuBarItemFirst' => $this->mlText[61],
      'menuBarItemLast' => $this->mlText[62],
      'menuBarItemImg' => $this->mlText[54],
      'menu' => $this->mlText[5],
      'menuTitle' => $this->mlText[65],
      'menuItem' => $this->mlText[6],
      'menuItemImg' => $this->mlText[55],
      'menuItemSep' => $this->mlText[7],
      'menuItemHdr' => $this->mlText[8],
      'dynpage' => $this->mlText[9],
      'dynpage_breadcrumbs' => $this->mlText[10],
      'dynpage_link_tree' => $this->mlText[11],
      'clock_date' => $this->mlText[14],
      'clock_time' => $this->mlText[15],
      'flash' => $this->mlText[16],
      'language' => $this->mlText[17],
      'language_item' => $this->mlText[18],
      'language_item_img' => $this->mlText[44],
      'user_login' => $this->mlText[19],
      'user_login_name' => $this->mlText[20],
      'user_login_input' => $this->mlText[46],
      'user_login_password' => $this->mlText[21],
      'user_login_okay' => $this->mlText[22],
      'user_register_link' => $this->mlText[23],
      'user_login_link' => $this->mlText[24],
      'user_edit_profile_link' => $this->mlText[25],
      'user_change_password_link' => $this->mlText[26],
      'user_logout_link' => $this->mlText[27],
      'mail_subscription' => $this->mlText[28],
      'mail_subscription_comment' => $this->mlText[30],
      'mail_subscription_label' => $this->mlText[31],
      'mail_subscription_field' => $this->mlText[32],
      'mail_subscription_input' => $this->mlText[46],
      'mail_subscription_okay_button' => $this->mlText[22],
      'last_update' => $this->mlText[33],
      'news_feed' => $this->mlText[34],
      'news_feed_title' => $this->mlText[35],
      'news_feed_image' => $this->mlText[36],
      'news_feed_image_file' => $this->mlText[45],
      'news_feed_event' => $this->mlText[77],
      'news_feed_event_title' => $this->mlText[70],
      'news_feed_event_label' => $this->mlText[71],
      'news_feed_event_radio' => $this->mlText[72],
      'news_feed_event_list' => $this->mlText[79],
      'news_feed_event_field' => $this->mlText[73],
      'news_feed_event_field_input' => $this->mlText[46],
      'news_feed_event_period' => $this->mlText[74],
      'news_feed_event_message' => $this->mlText[78],
      'news_feed_newsstories' => $this->mlText[75],
      'news_feed_event_search' => $this->mlText[76],
      'news_feed_newsstory' => $this->mlText[13],
      'news_feed_headline' => $this->mlText[37],
      'news_feed_excerpt' => $this->mlText[82],
      'news_feed_read_next' => $this->mlText[81],
      'news_feed_story_image' => $this->mlText[63],
      'news_feed_story_image_file' => $this->mlText[64],
      'news_feed_release' => $this->mlText[38],
      'news_feed_rss' => $this->mlText[39],
      'news_feed_inline_datepicker' => $this->mlText[83],
      'rss_feed' => $this->mlText[49],
      'rss_feed_title' => $this->mlText[60],
      'rss_feed_newsstory' => $this->mlText[52],
      'rss_feed_headline' => $this->mlText[59],
      'profile_address' => $this->mlText[40],
      'profile_telephone' => $this->mlText[47],
      'profile_fax' => $this->mlText[67],
      'profile_copyright' => $this->mlText[68],
      'template_page' => $this->mlText[41],
      'search' => $this->mlText[48],
      'search_field' => $this->mlText[50],
      'search_input' => $this->mlText[51],
      'lexicon_search' => $this->mlText[80],
      'lexicon_search_title' => $this->mlText[35],
      'lexicon_search_field' => $this->mlText[32],
      'lexicon_search_input' => $this->mlText[46],
      'client_cycle' => $this->mlText[29],
      'client_cycle_image' => $this->mlText[66],
      'link_cycle' => $this->mlText[29],
      'link_cycle_image' => $this->mlText[66],
      'photo_cycle' => $this->mlText[29],
      'photo_cycle_image' => $this->mlText[66],
    );

    return($tagNames);
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  // Delete a tag and all its properties
  function deleteTemplateTag($templateTagId) {
    if ($templateTagId) {
      $this->delete($templateTagId);

      if ($templateTag = $this->selectById($templateTagId)) {
        $templatePropertySetId = $templateTag->getTemplatePropertySetId();
        if ($templatePropertySetId) {
          $this->templatePropertySetUtils->deleteTemplatePropertySet($templatePropertySetId);
        }
      }
    }
  }

  // Duplicate a tag
  function duplicate($templateTag, $templateElementId) {
    $templateTag->setTemplateElementId($templateElementId);
    $lastInsertTemplatePropertySetId = $this->templatePropertySetUtils->duplicate($templateTag->getTemplatePropertySetId());
    $templateTag->setTemplatePropertySetId($lastInsertTemplatePropertySetId);
    $this->insert($templateTag);
  }

  // Export a tag
  function exportXML($xmlNode, $templateTagId) {
    if ($templateTag = $this->selectById($templateTagId)) {
      $tagID = $templateTag->getTagID();

      $xmlChildNode = $xmlNode->addChild(TEMPLATE_TAG);
      $attributes = array("tagID" => $tagID);
      if (is_array($attributes)) {
        foreach ($attributes as $aName => $aValue) {
          $xmlChildNode->addAttribute($aName, $aValue);
        }
      }

      $templatePropertySetId = $templateTag->getTemplatePropertySetId();
      $this->templatePropertySetUtils->exportXML($xmlChildNode, $templatePropertySetId);
    }
  }

  // Import a tag
  function importXML($xmlNode, $lastInsertTemplateElementId) {
    $tagID = $xmlNode->attributes()["tagID"];

    $templateTag = new TemplateTag();
    $templateTag->setTagID($tagID);
    $templateTag->setTemplateElementId($lastInsertTemplateElementId);
    $this->insert($templateTag);
    $lastInsertTemplateTagId = $this->getLastInsertId();

    $xmlChildNodes = $xmlNode->children();
    foreach ($xmlChildNodes as $xmlChildNode) {
      $name = $xmlChildNode->getName();
      if ($name == TEMPLATE_PROPERTY_SET) {
        $lastInsertTemplatePropertySetId = $this->templatePropertySetUtils->importXML($xmlChildNode);

        $templateTag->setId($lastInsertTemplateTagId);
        $templateTag->setTemplatePropertySetId($lastInsertTemplatePropertySetId);
        $this->update($templateTag);
      }
    }
  }

  function getTemplateModelId($templateTagId) {
    $templateModelId = '';

    if ($templateTag = $this->selectById($templateTagId)) {
      $templateElementId = $templateTag->getTemplateElementId();
      $templateModelId = $this->templateElementUtils->getTemplateModelId($templateElementId);
    }

    return($templateModelId);
  }

  // Get the name of a tag
  function getTagName($tagID) {
    $name = '';
    if ($tagID) {
      $tagNames = $this->getTagNames();
      if (array_key_exists($tagID, $tagNames)) {
        $name = $tagNames[$tagID];
      } else {
        $websiteName = $this->profileUtils->getProfileValue("website.name");
        $webmasterEmail = $this->profileUtils->getProfileValue("webmaster.email");
        emailError("Missing name for the element tag $tagID on the website $websiteName Contact $webmasterEmail");
      }
    }

    return($name);
  }

  // Render the tag properties for an html output
  function renderHtmlProperties($templateTagId, $absolutTagID = false) {
    $str = '';

    if ($templateTagId) {
      if ($templateTag = $this->selectById($templateTagId)) {
        $templatePropertySetId = $templateTag->getTemplatePropertySetId();
        $tagID = $templateTag->getTagID();

        $strProperties = $this->templatePropertySetUtils->renderHtmlProperties($templatePropertySetId);
        if ($strProperties) {
          $strTag = '.' . $this->renderTagID($templateTagId, $absolutTagID);
          $str .= $strTag . ' { ' . $strProperties .' }';

          $strTextProperties = $this->templatePropertySetUtils->renderHtmlTextProperties($templatePropertySetId);
          if (trim($strTextProperties)) {
            $str .= ' ' . $strTag . ' a { ' . $strTextProperties . ' }';
          }

          $str .= ' ' . $this->templatePropertySetUtils->renderHtmlLinkProperties($strTag, $templatePropertySetId);

          // Add the property set id to allow for the editing of the line of properties
          // by the property editor
          $str .= ' /* TPS_ID_' . $templatePropertySetId . ' */';
        }
      }
    }

    return($str);
  }

  // Render the tag id of the element.s tag
  function renderTagID($templateTagId, $absolutTagID) {
    $str = '';

    if ($templateTag = $this->selectById($templateTagId)) {
      $tagID = $templateTag->getTagID();

      // Check if the parent ids must be rendered
      // They are not rendered when editing an element
      // But they are rendered when creating the model output
      if ($absolutTagID) {
        $elementTagID = $this->renderElementTagID($templateTagId);

        $str = $elementTagID . ' .' . $tagID;
      } else {
        $str = $tagID;
      }
    }

    return($str);
  }

  // Render the tag id of the element
  function renderElementTagID($templateTagId) {
    $str = '';

    if ($templateTagId) {
      if ($templateTag = $this->selectById($templateTagId)) {
        $templateElementId = $templateTag->getTemplateElementId();
        $str = $this->templateElementUtils->renderTagID($templateElementId);
      }
    }

    return($str);
  }

}

?>
