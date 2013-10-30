<?

class ElearningScoringRangeUtils extends ElearningScoringRangeDB {

  function ElearningScoringRangeUtils() {
    $this->ElearningScoringRangeDB();
  }

  function deleteScoringRange($elearningScoringRangeId) {
    $this->delete($elearningScoringRangeId);
  }

}

?>
