<?php

class Template {

  var $pageTitle;
  var $pageContent;
  var $facebookXMLNS;

  function Template($id = '') {
  }

  function getPageTitle() {
    return($this->pageTitle);
  }

  function getPageContent() {
    return($this->pageContent);
  }

  function getFacebookXMLNS() {
    return($this->facebookXMLNS);
  }

  function setPageTitle($pageTitle) {
    $this->pageTitle = $pageTitle;
  }

  function setPageContent($pageContent) {
    $this->pageContent = $pageContent;
  }

  function setFacebookXMLNS($facebookXMLNS) {
    $this->facebookXMLNS = $facebookXMLNS;
  }

}

?>
