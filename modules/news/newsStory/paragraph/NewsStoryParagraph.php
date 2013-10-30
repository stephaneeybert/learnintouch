<?php

class NewsStoryParagraph {

  var $id;
  var $header;
  var $body;
  var $footer;
  var $newsStoryId;

  function NewsStoryParagraph($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getHeader() {
    return($this->header) ;
  }

  function getBody() {
    return($this->body) ;
  }

  function getFooter() {
    return($this->footer) ;
  }

  function getNewsStoryId() {
    return($this->newsStoryId);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setHeader($header) {
    $this->header = $header;
  }

  function setBody($body) {
    $this->body = $body;
  }

  function setFooter($footer) {
    $this->footer = $footer;
  }

  function setNewsStoryId($newsStoryId) {
    $this->newsStoryId = $newsStoryId;
  }

}

?>
