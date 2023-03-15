<?

class ElearningResultRangeUtils extends ElearningResultRangeDB {

  var $elearningExerciseUtils;

  function __construct() {
    parent::__construct();
  }

  function renderAverage($nbCorrectAnswers, $nbQuestions) {
    if ($nbQuestions > 0) {
      $average = round($nbCorrectAnswers / $nbQuestions * $this->elearningExerciseUtils->resultGradeScale(), 2);
    } else {
      $average = 0;
    }

    return($average);
  }

  function getRangeMatch($result) {
    $max = 0;
    $elearningResultRanges = $this->selectAll();
    if (count($elearningResultRanges) == 0) {
      $elearningResultRanges = array();
      $elearningResultRanges[0] = new ElearningResultRange('', 20, "E");
      $elearningResultRanges[1] = new ElearningResultRange('', 40, "D");
      $elearningResultRanges[2] = new ElearningResultRange('', 60, "C");
      $elearningResultRanges[3] = new ElearningResultRange('', 80, "B");
      $elearningResultRanges[4] = new ElearningResultRange('', 100, "A");
    }
    foreach ($elearningResultRanges as $elearningResultRange) {
      // The minimum value for the range is the maximum value of the preceding range
      $min = $max;
      $max = $elearningResultRange->getUpperRange();
      if ($min <= $result && $result <= $max) {
        return($elearningResultRange->getGrade());
      }
    }
  }

  function calculateGrade($nbCorrectAnswers, $nbQuestions) {
    if ($nbQuestions > 0) {
      $average = round($nbCorrectAnswers / $nbQuestions * 100, 2);
    } else {
      $average = 0;
    }

    $grade = $this->getRangeMatch($average);

    return($grade);
  }

}

?>
