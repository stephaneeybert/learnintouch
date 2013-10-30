<?

class FormValidUtils extends FormValidDB {

  function FormValidUtils() {
    $this->FormValidDB();
  }

  // Delete a form validator
  function deleteFormValidator($formValidId) {
    $this->delete($formValidId);
  }

}

?>
