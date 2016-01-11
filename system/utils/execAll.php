<?PHP

require_once("website.php");

$adminUtils->checkForStaffLogin();

// List the directories
$dirNames = LibDir::getDirNames($gAccountPath);

foreach ($dirNames as $dirName) {

  chdir($gAccountPath . $dirName);

  if ($dirName != "." && $dirName != ".." && is_dir("data")) {
    $str = "Account name: <b>$dirName</b><br>";
    print($str);

    $search = array('&#8222;', '&#8220;');
    $replace = array("'", "'");
    $cleaned = html_entity_decode(str_replace($search, $replace, htmlentities($string)));

    mkdir("data/adir");
    mkdir("data/adir/image");
    chmod("data/adir/image", 0755);

//    rmdir("data/container");
//    rename("data/news/newsletter", "data/news/newsPaper");
//    unlink("data/template/image/file.txt");
    }
  }

?>
