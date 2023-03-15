<?

class FormValidUtils extends FormValidDB {

  function __construct() {
    parent::__construct();
  }

  // Delete a form validator
  function deleteFormValidator($formValidId) {
    $this->delete($formValidId);
  }

}

?>
