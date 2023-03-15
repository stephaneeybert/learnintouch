<?php

class ElearningAssignment {

	var $id;
	var $elearningSubscriptionId;
	var $elearningExerciseId;
	var $elearningResultId;
	var $onlyOnce;
	var $openingDate;
	var $closingDate;

	function __construct($id = '') {
	}

	function getId() {
		return ($this->id);
	}

	function getElearningSubscriptionId() {
		return ($this->elearningSubscriptionId);
	}

	function getElearningExerciseId() {
		return ($this->elearningExerciseId);
	}

	function getElearningResultId() {
		return ($this->elearningResultId);
	}

	function getOnlyOnce() {
		return ($this->onlyOnce);
	}

	function getOpeningDate() {
		return ($this->openingDate);
	}

	function getClosingDate() {
		return ($this->closingDate);
	}

	function setId($id) {
		$this->id = $id;
	}

	function setElearningSubscriptionId($elearningSubscriptionId) {
		$this->elearningSubscriptionId = $elearningSubscriptionId;
	}

	function setElearningExerciseId($elearningExerciseId) {
		$this->elearningExerciseId = $elearningExerciseId;
	}

	function setElearningResultId($elearningResultId) {
		$this->elearningResultId = $elearningResultId;
	}

	function setOnlyOnce($onlyOnce) {
		$this->onlyOnce = $onlyOnce;
	}

	function setOpeningDate($openingDate) {
		$this->openingDate = $openingDate;
	}

	function setClosingDate($closingDate) {
		$this->closingDate = $closingDate;
	}

}

?>
