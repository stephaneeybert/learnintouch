<?php

class NewsStoryDao extends Dao {

  var $tableName;

  function NewsStoryDao($dataSource, $tableName) {
    Dao::Dao($dataSource);

    $this->tableName = $tableName;
  }

  function createTable() {
    $sqlStatement = <<<HEREDOC
create table if not exists $this->tableName
(
id int unsigned not null auto_increment,
version int unsigned not null,
headline varchar(255) not null,
excerpt text,
audio varchar(255),
audio_url varchar(255),
link varchar(255),
release_date datetime,
archive_date datetime,
event_start_date datetime,
event_end_date datetime,
news_editor_id int unsigned,
index (news_editor_id), foreign key (news_editor_id) references news_editor(id),
news_paper_id int unsigned not null,
index (news_paper_id), foreign key (news_paper_id) references news_paper(id),
news_heading_id int unsigned,
index (news_heading_id), foreign key (news_heading_id) references news_heading(id),
list_order int unsigned not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($headline, $excerpt, $audio, $audioUrl, $link, $releaseDate, $archive, $eventStartDate, $eventEndDate, $newsEditor, $newsPaper, $newsHeading, $listOrder) {
    $newsEditor = LibString::emptyToNULL($newsEditor);
    $newsHeading = LibString::emptyToNULL($newsHeading);
    $releaseDate = LibString::emptyToNULL($releaseDate);
    $archive = LibString::emptyToNULL($archive);
    $eventStartDate = LibString::emptyToNULL($eventStartDate);
    $eventEndDate = LibString::emptyToNULL($eventEndDate);
    $releaseDate = LibString::addSingleQuotesIfNotNULL($releaseDate);
    $archive = LibString::addSingleQuotesIfNotNULL($archive);
    $eventStartDate = LibString::addSingleQuotesIfNotNULL($eventStartDate);
    $eventEndDate = LibString::addSingleQuotesIfNotNULL($eventEndDate);
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$headline', '$excerpt', '$audio', '$audioUrl', '$link', $releaseDate, $archive, $eventStartDate, $eventEndDate, $newsEditor, '$newsPaper', $newsHeading, '$listOrder')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $headline, $excerpt, $audio, $audioUrl, $link, $releaseDate, $archive, $eventStartDate, $eventEndDate, $newsEditor, $newsPaper, $newsHeading, $listOrder) {
    $newsEditor = LibString::emptyToNULL($newsEditor);
    $newsHeading = LibString::emptyToNULL($newsHeading);
    $releaseDate = LibString::emptyToNULL($releaseDate);
    $archive = LibString::emptyToNULL($archive);
    $eventStartDate = LibString::emptyToNULL($eventStartDate);
    $eventEndDate = LibString::emptyToNULL($eventEndDate);
    $releaseDate = LibString::addSingleQuotesIfNotNULL($releaseDate);
    $archive = LibString::addSingleQuotesIfNotNULL($archive);
    $eventStartDate = LibString::addSingleQuotesIfNotNULL($eventStartDate);
    $eventEndDate = LibString::addSingleQuotesIfNotNULL($eventEndDate);
    $sqlStatement = "UPDATE $this->tableName SET headline = '$headline', excerpt = '$excerpt', audio = '$audio', audio_url = '$audioUrl', link = '$link', release_date = $releaseDate, archive_date = $archive, event_start_date = $eventStartDate, event_end_date = $eventEndDate, news_editor_id = $newsEditor, news_paper_id = '$newsPaper', news_heading_id = $newsHeading, list_order = '$listOrder' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByListOrder($newsPaperId, $newsHeadingId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE news_paper_id = '$newsPaperId' AND (news_heading_id = '$newsHeadingId' OR (news_heading_id IS NULL AND '$newsHeadingId' < '1')) AND list_order = '$listOrder' ORDER BY list_order DESC";
    return($this->querySelect($sqlStatement));
  }

  function selectByNextListOrder($newsPaperId, $newsHeadingId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE news_paper_id = '$newsPaperId' AND (news_heading_id = '$newsHeadingId' OR (news_heading_id IS NULL AND '$newsHeadingId' < '1')) AND list_order > '$listOrder' ORDER BY list_order LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByPreviousListOrder($newsPaperId, $newsHeadingId, $listOrder) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE news_paper_id = '$newsPaperId' AND (news_heading_id = '$newsHeadingId' OR (news_heading_id IS NULL AND '$newsHeadingId' < '1')) AND list_order < '$listOrder' ORDER BY list_order DESC LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function countDuplicateListOrderRows($newsPaperId, $newsHeadingId) {
    $sqlStatement = "SELECT count(distinct ns1.id) as count FROM $this->tableName ns1, $this->tableName ns2 where ns1.id != ns2.id and ns1.news_paper_id = ns2.news_paper_id and ns1.news_heading_id = ns2.news_heading_id and ns1.list_order = ns2.list_order and ns1.news_paper_id = $newsPaperId and (ns1.news_heading_id = '$newsHeadingId' OR (ns1.news_heading_id IS NULL AND '$newsHeadingId' < '1'))";
    return($this->querySelect($sqlStatement));
  }

  function selectByNewsPaper($newsPaperId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS DISTINCT ns.* FROM $this->tableName ns LEFT JOIN " . DB_TABLE_NEWS_HEADING . " nh ON ns.news_heading_id = nh.id WHERE ns.news_paper_id = '$newsPaperId' ORDER BY nh.list_order, ns.list_order";
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

  function selectByNewsHeading($newsHeadingId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE news_heading_id = '$newsHeadingId' OR (news_heading_id IS NULL AND '$newsHeadingId' < '1') ORDER BY list_order";
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

  function selectByNewsEditor($newsEditorId) {
    $sqlStatement = "SELECT DISTINCT ns.* FROM $this->tableName ns LEFT JOIN " . DB_TABLE_NEWS_HEADING . " nh ON ns.news_heading_id = nh.id WHERE ns.news_editor_id = '$newsEditorId' OR (ns.news_editor_id IS NULL AND '$newsEditorId' < '1') ORDER BY nh.list_order, ns.list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByNewsPaperAndNewsHeading($newsPaperId, $newsHeadingId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE news_paper_id = '$newsPaperId' AND (news_heading_id = '$newsHeadingId' OR (news_heading_id IS NULL AND '$newsHeadingId' < '1')) ORDER BY list_order";
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

  function selectByNewsPaperAndNewsHeadingOrderById($newsPaperId, $newsHeadingId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE news_paper_id = '$newsPaperId' AND (news_heading_id = '$newsHeadingId' OR (news_heading_id IS NULL AND '$newsHeadingId' < '1')) ORDER BY id";
    return($this->querySelect($sqlStatement));
  }

  function selectByNewsPaperAndNewsEditor($newsPaperId, $newsEditorId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS DISTINCT ns.* FROM $this->tableName ns LEFT JOIN " . DB_TABLE_NEWS_HEADING . " nh ON ns.news_heading_id = nh.id WHERE ns.news_paper_id = '$newsPaperId' AND (ns.news_editor_id = '$newsEditorId' OR (ns.news_editor_id IS NULL AND '$newsEditorId' < '1')) ORDER BY nh.list_order, ns.list_order";
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

  function selectByNewsHeadingAndNewsEditor($newsHeadingId, $newsEditorId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE (news_heading_id = '$newsHeadingId' OR (news_heading_id IS NULL AND '$newsHeadingId' < '1')) AND (news_editor_id = '$newsEditorId' OR (news_editor_id IS NULL AND '$newsEditorId' < '1')) ORDER BY list_order";
    return($this->querySelect($sqlStatement));
  }

  function selectByNewsPaperAndNewsHeadingAndNewsEditor($newsPaperId, $newsHeadingId, $newsEditorId, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE news_paper_id = '$newsPaperId' AND (news_heading_id = '$newsHeadingId' OR (news_heading_id IS NULL AND '$newsHeadingId' < '1')) AND (news_editor_id = '$newsEditorId' OR (news_editor_id IS NULL AND '$newsEditorId' < '1')) ORDER BY list_order";
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
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE lower(headline) LIKE lower('%$searchPattern%') OR lower(excerpt) LIKE lower('%$searchPattern%') OR release_date LIKE '%$searchPattern%' ORDER BY headline";
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

  function selectByAudio($audio) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE audio = '$audio'";
    return($this->querySelect($sqlStatement));
  }

  function selectByNewsPaperAndPublished($newsPaperId, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS DISTINCT ns.* FROM $this->tableName ns LEFT JOIN " . DB_TABLE_NEWS_HEADING . " nh ON ns.news_heading_id = nh.id WHERE ns.news_paper_id = '$newsPaperId' AND ((ns.release_date IS NOT NULL AND ns.archive_date IS NOT NULL AND DATE(ns.release_date) <= '$systemDate' AND DATE(ns.archive_date) >= '$systemDate') OR (ns.release_date IS NOT NULL AND ns.archive_date IS NULL AND DATE(ns.release_date) <= '$systemDate') OR (ns.release_date IS NULL AND ns.archive_date IS NOT NULL AND DATE(ns.archive_date) >= '$systemDate')) ORDER BY nh.list_order, ns.list_order";
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

  function selectByNewsPaperAndArchived($newsPaperId, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE news_paper_id = '$newsPaperId' AND archive_date IS NOT NULL AND archive_date < '$systemDate' ORDER BY release_date DESC";
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

  function selectByNewsPaperAndDeferred($newsPaperId, $systemDate, $start = false, $rows = false) {
    $sqlStatement = "SELECT SQL_CALC_FOUND_ROWS * FROM $this->tableName WHERE news_paper_id = '$newsPaperId' AND release_date IS NOT NULL AND release_date > '$systemDate' ORDER BY release_date DESC";
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

  // Count the number of rows of the last select statement
  // ignoring the LIMIT keyword if any
  // The SQL_CALC_FOUND_ROWS clause tells MySQL to calculate how many rows there would be
  // in the result set, disregarding any LIMIT clause with the number of rows later
  // retrieved using the SELECT FOUND_ROWS() statement
  function countFoundRows() {
    $sqlStatement = "SELECT FOUND_ROWS() as count";
    return($this->querySelect($sqlStatement));
  }

}

?>
