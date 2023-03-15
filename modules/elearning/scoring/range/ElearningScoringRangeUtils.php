<?

class ElearningScoringRangeUtils extends ElearningScoringRangeDB {

  function __construct() {
    parent::__construct();
  }

  function deleteScoringRange($elearningScoringRangeId) {
    $this->delete($elearningScoringRangeId);
  }

}

?>
