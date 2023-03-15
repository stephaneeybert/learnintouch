<?php

class ElearningScoringRange {

  var $id;
  var $upperRange;
  var $score;
  var $advice;
  var $proposal;
  var $linkText;
  var $linkUrl;
  var $scoringId;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getUpperRange() {
    return($this->upperRange);
  }

  function getScore() {
    return($this->score) ;
  }

  function getAdvice() {
    return($this->advice) ;
  }

  function getProposal() {
    return($this->proposal) ;
  }

  function getLinkText() {
    return($this->linkText) ;
  }

  function getLinkUrl() {
    return($this->linkUrl) ;
  }

  function getScoringId() {
    return($this->scoringId) ;
  }

  function setId($id) {
    $this->id = $id;
  }

  function setUpperRange($upperRange) {
    $this->upperRange = $upperRange;
  }

  function setScore($score) {
    $this->score = $score;
  }

  function setAdvice($advice) {
    $this->advice = $advice;
  }

  function setProposal($proposal) {
    $this->proposal = $proposal;
  }

  function setLinkText($linkText) {
    $this->linkText = $linkText;
  }

  function setLinkUrl($linkUrl) {
    $this->linkUrl = $linkUrl;
  }

  function setScoringId($scoringId) {
    $this->scoringId = $scoringId;
  }

}

?>
