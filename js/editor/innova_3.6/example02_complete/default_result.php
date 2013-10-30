<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link href="style/test.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
if(isset($_POST["txtContent"]))
  {
  $sContent=stripslashes($_POST['txtContent']); /*** remove (/) slashes ***/
  echo $sContent;
  }
?>
</body>
</html>
