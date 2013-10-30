<?php
$file = "content.htm"; //File to edit

if(isset($_POST["txtContent"]))
  {
  $sContent=stripslashes($_POST['txtContent']);//remove slashes (/)
  //file_put_contents($sContent);
  $handle = fopen($file,'w');
  fwrite($handle,$sContent);
  }
?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <style>
    body{font:11px verdana,arial,sans-serif;}
    a{color:#0000cc;font-size:xx-small;}
  </style>

  <script language=JavaScript src='../scripts/innovaeditor.js'></script>
</head>
<body>

<h4>Editing a File (PHP example) - <a href="../default.htm">Back</a></h4>

<form method="post" action="default.php" id="Form1">

  <textarea id="txtContent" name="txtContent" rows=4 cols=30>
  <?php
  function encodeHTML($sHTML)
    {
    $sHTML=ereg_replace("&","&amp;",$sHTML);
    $sHTML=ereg_replace("<","&lt;",$sHTML);
    $sHTML=ereg_replace(">","&gt;",$sHTML);
    return $sHTML;
    }

  echo encodeHTML(file_get_contents($file));
  ?>
  </textarea>

  <script>
    var oEdit1 = new InnovaEditor("oEdit1");

        oEdit1.btnStyles=true;
    oEdit1.mode="HTML";
    oEdit1.REPLACE("txtContent");
  </script>

  <input type="submit" value="Save">
  <input type="button" value="view file" onclick="window.open('content.htm')">
</form>

</body>
</html>
