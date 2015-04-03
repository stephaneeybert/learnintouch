<?php

class ElearningAssignmentDao extends Dao {

  var $tableName;

  function ElearningAssignmentDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
elearning_subscription_id int unsigned not null,
index (elearning_subscription_id), foreign key (elearning_subscription_id) references elearning_subscription(id),
elearning_exercise_id int unsigned not null,
index (elearning_exercise_id), foreign key (elearning_exercise_id) references elearning_exercise(id),
elearning_result_id int unsigned,
index (elearning_result_id), foreign key (elearning_result_id) references elearning_result(id),
only_once boolean not null,
opening_date datetime,
closing_date datetime,
unique (elearning_subscription_id, elearning_exercise_id),
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($elearningSubscriptionId, $elearningExerciseId, $elearningResultId, $onlyOnce, $openingDate, $closingDate) {
    $elearningResultId = LibString::emptyToNULL($elearningResultId);
    $openingDate = LibString::emptyToNULL($openingDate);
    $openingDate = LibString::addSingleQuotesIfNotNULL($openingDate);
    $closingDate = LibString::emptyToNULL($closingDate);
    $closingDate = LibString::addSingleQuotesIfNotNULL($closingDate);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$elearningSubscriptionId', '$elearningExerciseId', $elearningResultId, '$onlyOnce', $openingDate, $closingDate)";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $elearningSubscriptionId, $elearningExerciseId, $elearningResultId, $onlyOnce, $openingDate, $closingDate) {
    $elearningResultId = LibString::emptyToNULL($elearningResultId);
    $openingDate = LibString::emptyToNULL($openingDate);
    $openingDate = LibString::addSingleQuotesIfNotNULL($openingDate);
    $closingDate = LibString::emptyToNULL($closingDate);
    $closingDate = LibString::addSingleQuotesIfNotNULL($closingDate);
    $sqlStatement = "UPDATE $this->tableName SET elearning_subscription_id = '$elearningSubscriptionId', elearning_exercise_id = '$elearningExerciseId', elearning_result_id = $elearningResultId, only_once = '$onlyOnce', opening_date = $openingDate, closing_date = $closingDate WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  // Count the number of rows of the last select statement
  // ignoring the LIMIT keyword if any
  // The SQL_CALC_FOUND_ROWS clause tells MySQL to calculate how many rows there would be
  // retrieved using the SELECT FOUND_ROWS() statement
  function countFoundRows() {
    $sqlStatement = "SELECT FOUND_ROWS() as count";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByExerciseId($elearningExerciseId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS ea.* FROM $this->tableName ea, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " es, " . DB_TABLE_USER . " u WHERE ea.elearning_subscription_id = es.id AND es.user_account_id = u.id AND ea.elearning_exercise_id = '$elearningExerciseId' ORDER BY ea.opening_date DESC";
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

  function selectByResultId($elearningResultId) {
    $sqlStatement = "SELECT ea.* FROM $this->tableName ea WHERE ea.elearning_result_id = '$elearningResultId'";
    return($this->querySelect($sqlStatement));
  }

  function selectBySubscriptionId($elearningSubscriptionId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS ea.* FROM $this->tableName ea WHERE ea.elearning_subscription_id = '$elearningSubscriptionId' ORDER BY ea.opening_date DESC";
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

  function selectBySubscriptionIdAndExerciseId($elearningSubscriptionId, $elearningExerciseId) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS ea.* FROM $this->tableName ea WHERE ea.elearning_subscription_id = '$elearningSubscriptionId' AND ea.elearning_exercise_id = '$elearningExerciseId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectBySubscriptionIdAndOpened($elearningSubscriptionId, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS ea.* FROM $this->tableName ea WHERE ea.elearning_subscription_id = '$elearningSubscriptionId' AND (ea.opening_date IS NULL OR DATE(ea.opening_date) <= '$systemDate') AND (ea.closing_date IS NULL OR DATE(ea.closing_date) >= '$systemDate') ORDER BY ea.opening_date DESC";
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

  function selectBySubscriptionIdAndNotClosed($elearningSubscriptionId, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS ea.* FROM $this->tableName ea WHERE ea.elearning_subscription_id = '$elearningSubscriptionId' AND (ea.closing_date IS NULL OR DATE(ea.closing_date) >= '$systemDate') ORDER BY ea.opening_date DESC";
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

  function selectBySubscriptionIdAndClosed($elearningSubscriptionId, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS ea.* FROM $this->tableName ea WHERE ea.elearning_subscription_id = '$elearningSubscriptionId' AND (ea.closing_date IS NOT NULL AND DATE(ea.closing_date) < '$systemDate') ORDER BY ea.opening_date DESC";
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

  function selectBySubscriptionIdAndDeferred($elearningSubscriptionId, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS ea.* FROM $this->tableName ea WHERE ea.elearning_subscription_id = '$elearningSubscriptionId' AND (ea.opening_date IS NOT NULL AND DATE(ea.opening_date) > '$systemDate') ORDER BY ea.opening_date DESC";
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

  function selectByClassId($elearningClassId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS ea.* FROM ($this->tableName ea, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " es, " . DB_TABLE_USER . " u, " . DB_TABLE_ELEARNING_RESULT . " er) WHERE ea.elearning_subscription_id = es.id AND u.id = es.user_account_id AND (ea.elearning_result_id = er.id OR ea.elearning_result_id IS NULL) AND es.class_id = '$elearningClassId' ORDER BY er.exercise_datetime DESC, er.firstname, er.lastname";
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

  function selectByClassIdAndOpened($elearningClassId, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS ea.* FROM ($this->tableName ea, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " es, " . DB_TABLE_USER . " u, " . DB_TABLE_ELEARNING_RESULT . " er) WHERE ea.elearning_subscription_id = es.id AND u.id = es.user_account_id AND (ea.elearning_result_id = er.id OR ea.elearning_result_id IS NULL) AND es.class_id = '$elearningClassId' AND (ea.opening_date IS NULL OR DATE(ea.opening_date) <= '$systemDate') AND (ea.closing_date IS NULL OR DATE(ea.closing_date) >= '$systemDate') ORDER BY er.exercise_datetime DESC, u.firstname, u.lastname";
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

  function selectByClassIdAndClosed($elearningClassId, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS ea.* FROM ($this->tableName ea, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " es, " . DB_TABLE_USER . " u, " . DB_TABLE_ELEARNING_RESULT . " er) WHERE ea.elearning_subscription_id = es.id AND u.id = es.user_account_id AND (ea.elearning_result_id = er.id OR ea.elearning_result_id IS NULL) AND es.class_id = '$elearningClassId' AND (ea.closing_date IS NOT NULL AND DATE(ea.closing_date) < '$systemDate') ORDER BY er.exercise_datetime DESC, u.firstname, u.lastname";
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

  function selectByClassIdAndNotClosed($elearningClassId, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS ea.* FROM ($this->tableName ea, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " es, " . DB_TABLE_USER . " u, " . DB_TABLE_ELEARNING_RESULT . " er) WHERE ea.elearning_subscription_id = es.id AND u.id = es.user_account_id AND (ea.elearning_result_id = er.id OR ea.elearning_result_id IS NULL) AND es.class_id = '$elearningClassId' AND (ea.closing_date IS NULL OR DATE(ea.closing_date) >= '$systemDate') ORDER BY er.exercise_datetime DESC, u.firstname, u.lastname";
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

  function selectByClassIdAndDeferred($elearningClassId, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS ea.* FROM ($this->tableName ea, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " es, " . DB_TABLE_USER . " u, " . DB_TABLE_ELEARNING_RESULT . " er) WHERE ea.elearning_subscription_id = es.id AND u.id = es.user_account_id AND (ea.elearning_result_id = er.id OR ea.elearning_result_id IS NULL) AND es.class_id = '$elearningClassId' AND (ea.opening_date IS NOT NULL AND DATE(ea.opening_date) > '$systemDate') ORDER BY er.exercise_datetime DESC, u.firstname, u.lastname";
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

  function selectOpened($systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS ea.* FROM ($this->tableName ea, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " es, " . DB_TABLE_USER . " u, " . DB_TABLE_ELEARNING_RESULT . " er) WHERE ea.elearning_subscription_id = es.id AND u.id = es.user_account_id AND (ea.elearning_result_id = er.id OR ea.elearning_result_id IS NULL) AND (ea.opening_date IS NULL OR DATE(ea.opening_date) <= '$systemDate') AND (ea.closing_date IS NULL OR DATE(ea.closing_date) >= '$systemDate') ORDER BY er.exercise_datetime DESC, u.firstname, u.lastname";
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

  function selectDeferred($systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS ea.* FROM ($this->tableName ea, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " es, " . DB_TABLE_USER . " u, " . DB_TABLE_ELEARNING_RESULT . " er) WHERE ea.elearning_subscription_id = es.id AND u.id = es.user_account_id AND (ea.elearning_result_id = er.id OR ea.elearning_result_id IS NULL) AND ea.opening_date IS NOT NULL AND DATE(ea.opening_date) > '$systemDate' ORDER BY er.exercise_datetime DESC, u.firstname, u.lastname";
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

  function selectClosed($systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS ea.* FROM ($this->tableName ea, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " es, " . DB_TABLE_USER . " u, " . DB_TABLE_ELEARNING_RESULT . " er) WHERE ea.elearning_subscription_id = es.id AND u.id = es.user_account_id AND (ea.elearning_result_id = er.id OR ea.elearning_result_id IS NULL) AND ea.closing_date IS NOT NULL AND DATE(ea.closing_date) < '$systemDate' ORDER BY er.exercise_datetime DESC, u.firstname, u.lastname";
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

  function selectNotClosed($systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS ea.* FROM ($this->tableName ea, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " es, " . DB_TABLE_USER . " u, " . DB_TABLE_ELEARNING_RESULT . " er) WHERE ea.elearning_subscription_id = es.id AND u.id = es.user_account_id AND (ea.elearning_result_id = er.id OR ea.elearning_result_id IS NULL) AND ea.closing_date IS NULL OR DATE(ea.closing_date) >= '$systemDate' ORDER BY er.exercise_datetime DESC, u.firstname, u.lastname";
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

  function selectByClassIdAndResultWithinSessionId($elearningClassId, $elearningSessionId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS ea.* FROM ($this->tableName ea, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " esu, " . DB_TABLE_ELEARNING_SESSION . " es, " . DB_TABLE_ELEARNING_RESULT . " er, " . DB_TABLE_USER . " u) WHERE ea.elearning_subscription_id = esu.id AND u.id = esu.user_account_id AND esu.class_id = '$elearningClassId' AND (ea.elearning_result_id = er.id OR ea.elearning_result_id IS NULL) AND er.exercise_datetime IS NOT NULL AND es.id = '$elearningSessionId' AND ((es.opening_date IS NOT NULL AND es.closing_date IS NOT NULL AND DATE(er.exercise_datetime) >= DATE(es.opening_date) AND DATE(er.exercise_datetime) <= DATE(es.closing_date)) OR (es.opening_date IS NOT NULL AND es.closing_date IS NULL AND DATE(er.exercise_datetime) >= DATE(es.opening_date)) OR (es.opening_date IS NULL AND es.closing_date IS NOT NULL AND DATE(er.exercise_datetime) <= DATE(es.closing_date))) GROUP BY er.id ORDER BY er.exercise_datetime DESC, er.firstname, er.lastname";
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

  function selectBySubscriptionIdOrderByResult($elearningSubscriptionId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS ea.* FROM $this->tableName ea, " . DB_TABLE_ELEARNING_RESULT . " er WHERE (ea.elearning_result_id = er.id OR ea.elearning_result_id IS NULL) AND ea.elearning_subscription_id = '$elearningSubscriptionId' ORDER BY er.exercise_datetime DESC, er.firstname, er.lastname";
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

  function selectByResultWithinSessionId($elearningSessionId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS ea.* FROM ($this->tableName ea, " . DB_TABLE_ELEARNING_SESSION . " es, " . DB_TABLE_ELEARNING_RESULT . " er, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " esu, " . DB_TABLE_USER . " u) WHERE ea.elearning_subscription_id = esu.id AND u.id = esu.user_account_id AND (ea.elearning_result_id = er.id OR ea.elearning_result_id IS NULL) AND er.exercise_datetime IS NOT NULL AND es.id = '$elearningSessionId' AND ((es.opening_date IS NOT NULL AND es.closing_date IS NOT NULL AND DATE(er.exercise_datetime) >= DATE(es.opening_date) AND DATE(er.exercise_datetime) <= DATE(es.closing_date)) OR (es.opening_date IS NOT NULL AND es.closing_date IS NULL AND DATE(er.exercise_datetime) >= DATE(es.opening_date)) OR (es.opening_date IS NULL AND es.closing_date IS NOT NULL AND DATE(er.exercise_datetime) <= DATE(es.closing_date))) GROUP BY er.id ORDER BY er.exercise_datetime DESC, er.firstname, er.lastname";
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

  function selectByClassIdAndResultSinceReleaseDate($elearningClassId, $sinceDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS ea.* FROM ($this->tableName ea, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " es, " . DB_TABLE_ELEARNING_RESULT . " er, " . DB_TABLE_USER . " u) WHERE ea.elearning_subscription_id = es.id AND u.id = es.user_account_id AND es.class_id = '$elearningClassId' AND (ea.elearning_result_id = er.id OR ea.elearning_result_id IS NULL) AND er.exercise_datetime IS NOT NULL AND DATE(er.exercise_datetime) >= '$sinceDate' GROUP BY er.id ORDER BY er.exercise_datetime DESC, er.firstname, er.lastname";
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

  function selectByResultSinceReleaseDate($sinceDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS ea.* FROM ($this->tableName ea, " . DB_TABLE_ELEARNING_SUBSCRIPTION . " es, " . DB_TABLE_ELEARNING_RESULT . " er, " . DB_TABLE_USER . " u) WHERE ea.elearning_subscription_id = es.id AND u.id = es.user_account_id AND (ea.elearning_result_id = er.id OR ea.elearning_result_id IS NULL) AND er.exercise_datetime IS NOT NULL AND DATE(er.exercise_datetime) >= '$sinceDate' GROUP BY er.id ORDER BY er.exercise_datetime DESC, er.firstname, er.lastname";
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

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function deleteBySubscriptionIdAndClosed($elearningSubscriptionId, $systemDate) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE elearning_subscription_id = '$elearningSubscriptionId' AND (closing_date IS NOT NULL AND DATE(closing_date) < '$systemDate') ";
    return($this->querySelect($sqlStatement));
  }

}

?>
