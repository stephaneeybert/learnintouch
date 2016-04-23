<?php

class ElearningCourseDao extends Dao {

  var $tableName;

  function ElearningCourseDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
name varchar(100) not null,
unique(name),
description varchar(255),
image varchar(255),
instant_correction boolean not null,
instant_congratulation boolean not null,
instant_solution boolean not null,
importable boolean not null,
locked boolean not null,
secured boolean not null,
free_samples int unsigned,
auto_subscription boolean not null,
auto_unsubscription boolean not null,
interrupt_timed_out_exercise boolean not null,
reset_exercise_answers boolean not null,
exercise_only_once boolean not null,
exercise_any_order boolean not null,
save_result_option varchar(50),
shuffle_questions boolean not null,
shuffle_answers boolean not null,
matter_id int unsigned not null,
index (matter_id), foreign key (matter_id) references elearning_matter(id),
user_account_id int unsigned,
index (user_account_id), foreign key (user_account_id) references user(id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($name, $description, $image, $instantCorrection, $instantCongratulation, $instantSolution, $importable, $locked, $secured, $freeSamples, $autoSubscription, $autoUnsubscription, $interruptTimedOutExercise, $resetExerciseAnswers, $exerciseOnlyOnce, $exerciseAnyOrder, $saveResultOption, $shuffleQuestions, $shuffleAnswers, $matterId, $userId) {
    $userId = LibString::emptyToNULL($userId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$name', '$description', '$image', '$instantCorrection', '$instantCongratulation', '$instantSolution', '$importable', '$locked', '$secured', '$freeSamples', '$autoSubscription', '$autoUnsubscription', '$interruptTimedOutExercise', '$resetExerciseAnswers', '$exerciseOnlyOnce', '$exerciseAnyOrder', '$saveResultOption', '$shuffleQuestions', '$shuffleAnswers', '$matterId', $userId)";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $name, $description, $image, $instantCorrection, $instantCongratulation, $instantSolution, $importable, $locked, $secured, $freeSamples, $autoSubscription, $autoUnsubscription, $interruptTimedOutExercise, $resetExerciseAnswers, $exerciseOnlyOnce, $exerciseAnyOrder, $saveResultOption, $shuffleQuestions, $shuffleAnswers, $matterId, $userId) {
    $userId = LibString::emptyToNULL($userId);
    $sqlStatement = "UPDATE $this->tableName SET name = '$name', description = '$description', image = '$image', instant_correction = '$instantCorrection', instant_congratulation = '$instantCongratulation', instant_solution = '$instantSolution', importable = '$importable', locked = '$locked', secured = '$secured', free_samples = '$freeSamples', auto_subscription = '$autoSubscription', auto_unsubscription = '$autoUnsubscription', interrupt_timed_out_exercise = '$interruptTimedOutExercise', reset_exercise_answers = '$resetExerciseAnswers', exercise_only_once = '$exerciseOnlyOnce', exercise_any_order = '$exerciseAnyOrder', save_result_option = '$saveResultOption', shuffle_questions = '$shuffleQuestions', shuffle_answers = '$shuffleAnswers', matter_id = '$matterId', user_account_id = $userId WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectAll($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName ORDER BY name";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  // Count the number of rows of the last select statement
  // ignoring the LIMIT keyword if any
  // The SQL_CALC_FOUND_ROWS clause tells MySQL to calculate how many rows there would be
  // in the result set, disregarding any LIMIT clause with the number of rows later
  // retrieved using the SELECT FOUND_ROWS() statement
  function countFoundRows() {
    $sqlStatement = "SELECT FOUND_ROWS() as count";
    return($this->querySelect($sqlStatement));
  }

  function selectByName($name) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE name = '$name' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByImage($image) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE image = '$image'";
    return($this->querySelect($sqlStatement));
  }

  function selectImportable() {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE importable = '1' ORDER BY name";
    return($this->querySelect($sqlStatement));
  }

  function selectAutoSubscription() {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE auto_subscription = '1' ORDER BY name";
    return($this->querySelect($sqlStatement));
  }

  function selectBySessionId($elearningSessionId) {
    $sqlStatement = "SELECT c.* FROM $this->tableName c, " . DB_TABLE_ELEARNING_SESSION_COURSE . " sc WHERE c.id = sc.elearning_course_id AND sc.elearning_session_id = '$elearningSessionId' ORDER BY c.name";
    return($this->querySelect($sqlStatement));
  }

  function selectBySessionIdAndAutoSubscription($elearningSessionId) {
    $sqlStatement = "SELECT c.* FROM $this->tableName c, " . DB_TABLE_ELEARNING_SESSION_COURSE . " sc WHERE c.id = sc.elearning_course_id AND sc.elearning_session_id = '$elearningSessionId' AND c.auto_subscription = '1' ORDER BY c.name";
    return($this->querySelect($sqlStatement));
  }

  function selectByMatterId($matterId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE matter_id = '$matterId' ORDER BY name";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  function selectByUserId($userId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE user_account_id = '$userId' ORDER BY name";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  function selectLikePattern($searchPattern, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE (lower(name) LIKE lower('%$searchPattern%') OR lower(description) LIKE lower('%$searchPattern%')) OR id = '$searchPattern' ORDER BY name";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  function selectLikePatternAndSessionId($searchPattern, $elearningSessionId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS c.* FROM $this->tableName c, " . DB_TABLE_ELEARNING_SESSION_COURSE . " sc WHERE c.id = sc.elearning_course_id AND sc.elearning_session_id = '$elearningSessionId') AND ((lower(c.name) LIKE lower('%$searchPattern%') OR lower(c.description) LIKE lower('%$searchPattern%')) OR c.id = '$searchPattern') ORDER BY c.name";
    if ($rows) {
      if (!$start) {
        $start = 0;
      }
      $sqlStatement .= " LIMIT " . $start . ", " . $rows;
    } else if ($start) {
      $sqlStatement .= " LIMIT " . $start;
    }
    return($this->querySelect($sqlStatement));
  }

  function selectBySubscriptionWithTeacherId($teacherId) {
    $sqlStatement = "SELECT ec.* FROM $this->tableName ec, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " es WHERE es.teacher_id = '$teacherId' AND es.course_id = ec.id ORDER BY ec.name";
    return($this->querySelect($sqlStatement));
  }

}

?>
