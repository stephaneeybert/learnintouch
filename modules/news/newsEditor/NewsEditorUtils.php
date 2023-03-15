<?

class NewsEditorUtils extends NewsEditorDB {

  var $adminUtils;
  var $newsStoryUtils;

  function __construct() {
    parent::__construct();
  }

  // Get the firstname of an editor
  function getFirstname($newsEditorId) {
    $firstname = '';

    if ($editor = $this->selectById($newsEditorId)) {
      $adminId = $editor->getAdminId();
      if ($admin = $this->adminUtils->selectById($adminId)) {
        $firstname = $admin->getFirstname();
      }
    }

    return($firstname);
  }

  // Get the lastname of an editor
  function getLastname($newsEditorId) {
    $lastname = '';

    if ($editor = $this->selectById($newsEditorId)) {
      $adminId = $editor->getAdminId();
      if ($admin = $this->adminUtils->selectById($adminId)) {
        $lastname = $admin->getLastname();
      }
    }

    return($lastname);
  }

  // Get the email of an editor
  function getEmail($newsEditorId) {
    $email = '';

    if ($editor = $this->selectById($newsEditorId)) {
      $adminId = $editor->getAdminId();
      if ($admin = $this->adminUtils->selectById($adminId)) {
        $email = $admin->getEmail();
      }
    }

    return($email);
  }

  // Get the profile of an editor
  function getProfile($newsEditorId) {
    $profile = '';

    if ($editor = $this->selectById($newsEditorId)) {
      $adminId = $editor->getAdminId();
      if ($admin = $this->adminUtils->selectById($adminId)) {
        $profile = $admin->getProfile();
      }
    }

    return($profile);
  }

  // Delete an editor
  function deleteEditor($id) {
    if ($elearningSubscriptions = $this->newsStoryUtils->selectByNewsEditor($id)) {
      foreach ($newsStories as $newsStory) {
        $newsStory->setNewsEditor('');
        $this->newsStoryUtils->update($newsStory);
      }
    }

    return($this->delete($id));
  }

}

?>
