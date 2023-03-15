<?php

class ElearningResultDao extends Dao {

  var $tableName;

  function __construct($dataSource, $tableName) {
    parent::__construct($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
elearning_exercise_id int unsigned,
index (elearning_exercise_id), foreign key (elearning_exercise_id) references elearning_exercise(id),
subscription_id int unsigned,
index (subscription_id), foreign key (subscription_id) references elearning_subscription(id),
exercise_datetime datetime,
exercise_elapsed_time int unsigned,
firstname varchar(255),
lastname varchar(255),
message text,
text_comment text,
hide_comment boolean not null,
email varchar(255),
nb_reading_questions int unsigned,
nb_correct_reading_answers int unsigned,
nb_incorrect_reading_answers int unsigned,
nb_reading_points int unsigned,
nb_writing_questions int unsigned,
nb_correct_writing_answers int unsigned,
nb_incorrect_writing_answers int unsigned,
nb_writing_points int unsigned,
nb_listening_questions int unsigned,
nb_correct_listening_answers int unsigned,
nb_incorrect_listening_answers int unsigned,
nb_listening_points int unsigned,
nb_not_answered int unsigned,
nb_incorrect_answers int unsigned,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($elearningExerciseId, $subscriptionId, $exerciseDate, $exerciseElapsedTime, $firstname, $lastname, $message, $comment, $hideComment, $email, $nbReadingQuestions, $nbCorrectReadingAnswers, $nbIncorrectReadingAnswers, $nbReadingPoints, $nbWritingQuestions, $nbCorrectWritingAnswers, $nbIncorrectWritingAnswers, $nbWritingPoints, $nbListeningQuestions, $nbCorrectListeningAnswers, $nbIncorrectListeningAnswers, $nbListeningPoints, $nbNotAnswered, $nbIncorrectAnswers) {
    $elearningExerciseId = LibString::emptyToNULL($elearningExerciseId);
    $subscriptionId = LibString::emptyToNULL($subscriptionId);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', $elearningExerciseId, $subscriptionId, '$exerciseDate', '$exerciseElapsedTime', '$firstname', '$lastname', '$message', '$comment', '$hideComment', '$email', '$nbReadingQuestions', '$nbCorrectReadingAnswers', '$nbIncorrectReadingAnswers', '$nbReadingPoints', '$nbWritingQuestions', '$nbCorrectWritingAnswers', '$nbIncorrectWritingAnswers', '$nbWritingPoints', '$nbListeningQuestions', '$nbCorrectListeningAnswers', '$nbIncorrectListeningAnswers', '$nbListeningPoints', '$nbNotAnswered', '$nbIncorrectAnswers')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $elearningExerciseId, $subscriptionId, $exerciseDate, $exerciseElapsedTime, $firstname, $lastname, $message, $comment, $hideComment, $email, $nbReadingQuestions, $nbCorrectReadingAnswers, $nbIncorrectReadingAnswers, $nbReadingPoints, $nbWritingQuestions, $nbCorrectWritingAnswers, $nbIncorrectWritingAnswers, $nbWritingPoints, $nbListeningQuestions, $nbCorrectListeningAnswers, $nbIncorrectListeningAnswers, $nbListeningPoints, $nbNotAnswered, $nbIncorrectAnswers) {
    $elearningExerciseId = LibString::emptyToNULL($elearningExerciseId);
    $subscriptionId = LibString::emptyToNULL($subscriptionId);
    $sqlStatement = "UPDATE $this->tableName SET elearning_exercise_id = $elearningExerciseId, subscription_id = $subscriptionId, exercise_datetime = '$exerciseDate', exercise_elapsed_time = '$exerciseElapsedTime', firstname = '$firstname', lastname = '$lastname', message = '$message', text_comment = '$comment', hide_comment = '$hideComment', email = '$email', nb_reading_questions = '$nbReadingQuestions', nb_correct_reading_answers = '$nbCorrectReadingAnswers', nb_incorrect_reading_answers = '$nbIncorrectReadingAnswers', nb_reading_points = '$nbReadingPoints', nb_writing_questions = '$nbWritingQuestions', nb_correct_writing_answers = '$nbCorrectWritingAnswers', nb_incorrect_writing_answers = '$nbIncorrectWritingAnswers', nb_writing_points = '$nbWritingPoints', nb_listening_questions = '$nbListeningQuestions', nb_correct_listening_answers = '$nbCorrectListeningAnswers',  nb_incorrect_listening_answers = '$nbIncorrectListeningAnswers', nb_listening_points = '$nbListeningPoints', nb_not_answered = '$nbNotAnswered', nb_incorrect_answers = '$nbIncorrectAnswers' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectByReleaseDate($sinceDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE exercise_datetime >= '$sinceDate' ORDER BY exercise_datetime DESC, firstname, lastname";
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

  function selectNonSubscriptions($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE subscription_id IS NULL ORDER BY exercise_datetime DESC, firstname, lastname";
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

  function countAll() {
    $sqlStatement = "SELECT count(*) as count FROM $this->tableName";
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

  function selectLikePattern($searchPattern, $start = false, $rows = false) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$searchPattern' OR lower(email) LIKE lower('%$searchPattern%') OR lower(firstname) LIKE lower('%$searchPattern%') OR lower(lastname) LIKE lower('%$searchPattern%') OR lower(message) LIKE lower('%$searchPattern%') OR lower(text_comment) LIKE lower('%$searchPattern%') ORDER BY exercise_datetime DESC, firstname, lastname";
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

  function selectBySubscriptionIdAndCourseId($subscriptionId, $courseId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS r.* FROM $this->tableName r, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " s, " . DB_TABLE_USER . " u WHERE r.subscription_id = '$subscriptionId' AND s.course_id = '$courseId' AND r.subscription_id = s.id AND s.user_account_id = u.id ORDER BY r.exercise_datetime DESC, u.firstname, u.lastname";
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

  function selectBySubscriptionId($subscriptionId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS r.* FROM $this->tableName r, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " s, " . DB_TABLE_USER . " u WHERE (r.subscription_id = '$subscriptionId' OR (subscription_id IS NULL AND '$subscriptionId' < '1')) AND r.subscription_id = s.id AND s.user_account_id = u.id ORDER BY r.exercise_datetime DESC, u.firstname, u.lastname";
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
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS r.* FROM $this->tableName r, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " s WHERE r.subscription_id = s.id AND s.user_account_id = '$userId' ORDER BY r.exercise_datetime DESC";
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

  function selectByNoSessionId($start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS r.* FROM $this->tableName r, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " s, " . DB_TABLE_ELEARNING_COURSE . " cs, " . DB_TABLE_USER . " u WHERE r.subscription_id = s.id AND s.session_id IS NULL AND s.course_id = cs.id AND s.user_account_id = u.id ORDER BY cs.name, r.exercise_datetime DESC, u.firstname, u.lastname";
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

  function selectBySessionId($sessionId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS r.* FROM $this->tableName r, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " s, " . DB_TABLE_ELEARNING_COURSE . " cs, " . DB_TABLE_USER . " u WHERE r.subscription_id = s.id AND s.session_id = '$sessionId' AND s.course_id = cs.id AND s.user_account_id = u.id ORDER BY cs.name, r.exercise_datetime DESC, u.firstname, u.lastname";
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

  function selectBySessionIdAndCourseIdAndExerciseId($sessionId, $courseId, $exerciseId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS r.* FROM $this->tableName r, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " s, " . DB_TABLE_USER . " u WHERE r.subscription_id = s.id AND s.session_id = '$sessionId' AND s.course_id = '$courseId' AND s.user_account_id = u.id AND r.elearning_exercise_id = '$exerciseId' ORDER BY r.exercise_datetime DESC, u.firstname, u.lastname";
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

  function selectBySessionIdAndCourseId($sessionId, $courseId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS r.* FROM $this->tableName r, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " s, " . DB_TABLE_USER . " u WHERE r.subscription_id = s.id AND s.session_id = '$sessionId' AND s.course_id = '$courseId' AND s.user_account_id = u.id ORDER BY r.exercise_datetime DESC, u.firstname, u.lastname";
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

  function selectBySessionIdAndClassId($sessionId, $classId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS r.* FROM $this->tableName r, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " s, " . DB_TABLE_ELEARNING_COURSE . " cs, " . DB_TABLE_USER . " u WHERE r.subscription_id = s.id AND s.session_id = '$sessionId' AND s.class_id = '$classId' AND s.course_id = cs.id AND s.user_account_id = u.id ORDER BY cs.name, r.exercise_datetime DESC, u.firstname, u.lastname";
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

  function selectBySessionIdAndCourseIdAndClassIdAndExerciseId($sessionId, $courseId, $classId, $exerciseId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS r.* FROM $this->tableName r, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " s, " . DB_TABLE_USER . " u WHERE r.subscription_id = s.id AND s.session_id = '$sessionId' AND s.course_id = '$courseId' AND s.class_id = '$classId' AND s.user_account_id = u.id AND r.elearning_exercise_id = '$exerciseId' ORDER BY u.firstname, u.lastname";
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

  function selectByClassIdAndExerciseId($classId, $exerciseId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS r.* FROM $this->tableName r, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " s, " . DB_TABLE_USER . " u WHERE r.subscription_id = s.id AND s.class_id = '$classId' AND s.user_account_id = u.id AND r.elearning_exercise_id = '$exerciseId' ORDER BY u.firstname, u.lastname";
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

  function selectBySessionIdAndCourseIdAndClassIdAndTeacherId($sessionId, $courseId, $classId, $teacherId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS r.* FROM $this->tableName r, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " s, " . DB_TABLE_USER . " u WHERE r.subscription_id = s.id AND s.session_id = '$sessionId' AND s.course_id = '$courseId' AND s.class_id = '$classId' AND s.teacher_id = '$teacherId' AND s.user_account_id = u.id ORDER BY r.exercise_datetime DESC, u.firstname, u.lastname";
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

  function selectBySessionIdAndClassIdAndTeacherId($sessionId, $classId, $teacherId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS r.* FROM $this->tableName r, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " s, " . DB_TABLE_USER . " u WHERE r.subscription_id = s.id AND s.session_id = '$sessionId' AND s.class_id = '$classId' AND s.teacher_id = '$teacherId' AND s.user_account_id = u.id ORDER BY r.exercise_datetime DESC, u.firstname, u.lastname";
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

  function selectByCourseIdAndClassIdAndTeacherId($courseId, $classId, $teacherId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS r.* FROM $this->tableName r, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " s, " . DB_TABLE_USER . " u WHERE r.subscription_id = s.id AND s.course_id = '$courseId' AND s.class_id = '$classId' AND s.teacher_id = '$teacherId' AND s.user_account_id = u.id ORDER BY r.exercise_datetime DESC, u.firstname, u.lastname";
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

  function selectByClassId($classId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS r.* FROM $this->tableName r, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " s, " . DB_TABLE_USER . " u WHERE r.subscription_id = s.id AND s.class_id = '$classId' AND s.user_account_id = u.id ORDER BY r.exercise_datetime DESC, u.firstname, u.lastname";
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

  function selectByCourseIdAndTeacherId($courseId, $teacherId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS r.* FROM $this->tableName r, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " s, " . DB_TABLE_USER . " u WHERE r.subscription_id = s.id AND s.course_id = '$courseId' AND s.teacher_id = '$teacherId' AND s.user_account_id = u.id ORDER BY r.exercise_datetime DESC, u.firstname, u.lastname";
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

  function selectByCourseIdAndClassId($courseId, $classId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS r.* FROM $this->tableName r, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " s, " . DB_TABLE_USER . " u WHERE r.subscription_id = s.id AND s.course_id = '$courseId' AND s.class_id = '$classId' AND s.user_account_id = u.id ORDER BY r.exercise_datetime DESC, u.firstname, u.lastname";
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

  function selectBySessionIdAndCourseIdAndTeacherId($sessionId, $courseId, $teacherId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS r.* FROM $this->tableName r, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " s, " . DB_TABLE_USER . " u WHERE r.subscription_id = s.id AND s.session_id = '$sessionId' AND s.course_id = '$courseId' AND s.teacher_id = '$teacherId' AND s.user_account_id = u.id ORDER BY r.exercise_datetime DESC, u.firstname, u.lastname";
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

  function selectBySessionIdAndTeacherId($sessionId, $teacherId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS r.* FROM $this->tableName r, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " s, " . DB_TABLE_USER . " u WHERE r.subscription_id = s.id AND s.session_id = '$sessionId' AND s.teacher_id = '$teacherId' AND s.user_account_id = u.id ORDER BY r.exercise_datetime DESC, u.firstname, u.lastname";
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

  function selectBySessionIdAndCourseIdAndClassId($sessionId, $courseId, $classId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS r.* FROM $this->tableName r, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " s, " . DB_TABLE_USER . " u WHERE r.subscription_id = s.id AND s.session_id = '$sessionId' AND s.course_id = '$courseId' AND s.class_id = '$classId' AND s.user_account_id = u.id ORDER BY r.exercise_datetime DESC, u.firstname, u.lastname";
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

  function selectByCourseId($courseId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS r.* FROM $this->tableName r, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " s, " . DB_TABLE_USER . " u WHERE r.subscription_id = s.id AND s.course_id = '$courseId' AND s.user_account_id = u.id ORDER BY r.exercise_datetime DESC, u.firstname, u.lastname";
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

  function selectByTeacherId($teacherId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS r.* FROM $this->tableName r, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " s, " . DB_TABLE_USER . " u WHERE r.subscription_id = s.id AND s.teacher_id = '$teacherId' AND s.user_account_id = u.id ORDER BY r.exercise_datetime DESC, u.firstname, u.lastname";
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

  function selectByExerciseId($elearningExerciseId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE elearning_exercise_id = '$elearningExerciseId' ORDER BY exercise_datetime DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectBySubscriptionAndExercise($subscriptionId, $elearningExerciseId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (subscription_id = '$subscriptionId' OR (subscription_id IS NULL AND '$subscriptionId' < '1')) AND elearning_exercise_id = '$elearningExerciseId' ORDER BY exercise_datetime DESC LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByEmail($email) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE email = '$email' ORDER BY exercise_datetime DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectByEmailAndExercise($email, $elearningExerciseId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE email = '$email' AND elearning_exercise_id = '$elearningExerciseId' ORDER BY exercise_datetime DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectByEmailAndExerciseAndDate($email, $elearningExerciseId, $exerciseDate) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE email = '$email' AND elearning_exercise_id = '$elearningExerciseId' AND DATE(exercise_datetime) = '$exerciseDate' ORDER BY exercise_datetime DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectOldResults($sinceDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE exercise_datetime IS NOT NULL AND exercise_datetime <= '$sinceDate' ORDER BY exercise_datetime DESC";
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

}

?>
