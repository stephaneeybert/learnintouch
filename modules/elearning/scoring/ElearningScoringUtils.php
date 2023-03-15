<?

class ElearningScoringUtils extends ElearningScoringDB {

  var $elearningExerciseUtils;
  var $elearningScoringRangeUtils;

  function __construct() {
    parent::__construct();
  }

  // Delete a scoring
  function deleteScoring($scoringId) {
    if (!$elearningExercises = $this->elearningExerciseUtils->selectByScoringId($scoringId)) {
      if ($elearningScoringRanges = $this->elearningScoringRangeUtils->selectByScoringId($scoringId)) {
        foreach ($elearningScoringRanges as $elearningScoringRange) {
          $elearningScoringRangeId = $elearningScoringRange->getId();
          $this->elearningScoringRangeUtils->deleteScoringRange($elearningScoringRangeId);
        }
      }

      $this->delete($scoringId);
    }
  }

  // Get the range matching a score percentage result
  function getScoringMatch($scoringId, $resultScore) {
    $max = 0;
    $elearningScoringRanges = $this->elearningScoringRangeUtils->selectByScoringId($scoringId);
    foreach ($elearningScoringRanges as $elearningScoringRange) {
      // The minimum value for the range is the maximum value of the preceding range
      $min = $max;
      $max = $elearningScoringRange->getUpperRange();
      if ($min <= $resultScore && $resultScore <= $max) {
        return($elearningScoringRange);
      }
    }
  }

}

?>
