<?php

class NewsFeedDao extends Dao {

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
news_paper_id int unsigned not null,
image varchar(255),
max_display_number int unsigned,
image_align varchar(10),
image_width int unsigned,
with_excerpt boolean not null,
with_image boolean not null,
search_options boolean not null,
search_calendar boolean not null,
display_upcoming boolean not null,
search_title varchar(255),
search_display_as_page boolean not null,
primary key (id), unique (id)
) type = INNODB;
HEREDOC;

    return($this->querySelect($sqlStatement));
  }

  function insert($newsPaperId, $image, $maxDisplayNumber, $imageAlign, $imageWidth, $withExcerpt, $withImage, $searchOptions, $searchCalendar, $displayUpcoming, $searchTitle, $searchDisplayAsPage) {
    $sqlStatement = "INSERT INTO $this->tableName VALUES ('', '', '$newsPaperId', '$image', '$maxDisplayNumber', '$imageAlign', '$imageWidth', '$withExcerpt', '$withImage', '$searchOptions', '$searchCalendar', '$displayUpcoming', '$searchTitle', '$searchDisplayAsPage')";
    return($this->querySelect($sqlStatement));
  }

  function update($id, $newsPaperId, $image, $maxDisplayNumber, $imageAlign, $imageWidth, $withExcerpt, $withImage, $searchOptions, $searchCalendar, $displayUpcoming, $searchTitle, $searchDisplayAsPage) {
    $sqlStatement = "UPDATE $this->tableName SET news_paper_id = '$newsPaperId', image = '$image', max_display_number = '$maxDisplayNumber', image_align = '$imageAlign', image_width = '$imageWidth', with_excerpt = '$withExcerpt', with_image = '$withImage', search_options = '$searchOptions', search_calendar = '$searchCalendar', display_upcoming = '$displayUpcoming', search_title = '$searchTitle', search_display_as_page = '$searchDisplayAsPage' WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function delete($id) {
    $sqlStatement = "DELETE FROM $this->tableName WHERE id = '$id'";
    return($this->querySelect($sqlStatement));
  }

  function countAll() {
    $sqlStatement = "SELECT count(*) as count FROM $this->tableName";
    return($this->querySelect($sqlStatement));
  }

  function selectById($id) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE id = '$id' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByNewsPaperId($newsPaperId) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE news_paper_id = '$newsPaperId' LIMIT 1";
    return($this->querySelect($sqlStatement));
  }

  function selectByImage($image) {
    $sqlStatement = "SELECT * FROM $this->tableName WHERE image = '$image'";
    return($this->querySelect($sqlStatement));
  }

}

?>
