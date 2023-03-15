<?

class ElearningTeacherUtils extends ElearningTeacherDB {

  var $websiteText;

  var $userUtils;
  var $languageUtils;
  var $profileUtils;
  var $socialUserUtils;
  var $elearningSubscriptionUtils;

  function __construct() {
    parent::__construct();
  }

  function loadLanguageTexts() {
    $this->websiteText = $this->languageUtils->getWebsiteText(__FILE__);
  }

  // Get the firstname of a teacher
  function getFirstname($elearningTeacherId) {
    $firstname = '';

    if ($teacher = $this->selectById($elearningTeacherId)) {
      $userId = $teacher->getUserId();
      if ($user = $this->userUtils->selectById($userId)) {
        $firstname = $user->getFirstname();
      }
    }

    return($firstname);
  }

  // Get the user id of a teacher
  function getUserId($elearningTeacherId) {
    $userId = '';

    if ($teacher = $this->selectById($elearningTeacherId)) {
      $userId = $teacher->getUserId();
    }

    return($userId);
  }

  // Get the lastname of a teacher
  function getLastname($elearningTeacherId) {
    $lastname = '';

    if ($teacher = $this->selectById($elearningTeacherId)) {
      $userId = $teacher->getUserId();
      if ($user = $this->userUtils->selectById($userId)) {
        $lastname = $user->getLastname();
      }
    }

    return($lastname);
  }

  // Get the email of a teacher
  function getEmail($elearningTeacherId) {
    $email = '';

    if ($teacher = $this->selectById($elearningTeacherId)) {
      $userId = $teacher->getUserId();
      if ($user = $this->userUtils->selectById($userId)) {
        $email = $user->getEmail();
      }
    }

    return($email);
  }

  // Get the list of teachers
  function getTeacherList() {
    $elearningTeachers = $this->selectAll();

    return($elearningTeachers);
  }

  // Delete a teacher
  function deleteTeacher($id) {
    // Remove the teacher from any subscription
    if ($elearningSubscriptions = $this->elearningSubscriptionUtils->selectByTeacherId($id)) {
      foreach ($elearningSubscriptions as $elearningSubscription) {
        $elearningSubscription->setTeacherId('');
        $this->elearningSubscriptionUtils->update($elearningSubscription);
      }
    }

    $this->delete($id);
  }

  // Render the email
  function renderEmail($email) {
    if ($email) {
      $str = "<a href='mailto:$email'>$email</a>";
    } else {
      $str = '';
    }

    return($str);
  }

  // Publish on the social networks a message about the newly registered teacher
  function publishSocialNotification() {
    global $gElearningUrl;
    global $gContactUrl;

    $this->loadLanguageTexts();

    $websiteName = $this->profileUtils->getProfileValue("website.name");

    $message = $this->websiteText[1] . ' ' . $websiteName;
    $url = "$gElearningUrl/teacher/register.php";

    $caption = $this->websiteText[2];

    $actionLinks = array(array($this->websiteText[3], "$gElearningUrl/teacher/register.php"));

    $str = $this->socialUserUtils->publishNotification($message, $url, $caption, $actionLinks);

    return($str);
  }

  // Render the styling elements for the editing of the css style properties
  function renderStylingElementsForList() {
    $str = "\n<div class='elearning_teacher_list'>The list of teachers"
      . "<div class='elearning_teacher_name'>The name of a teacher</div>"
      . "<div class='elearning_teacher_email'>The email address of a teacher</div>"
      . "</div>";

    return($str);
  }

}

?>
