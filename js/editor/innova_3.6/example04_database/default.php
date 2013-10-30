<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <style>
    body{font:11px verdana,arial,sans-serif;}
    a{color:#0000cc;font-size:xx-small;}
  </style>

  <?php
  $oConn = mysql_connect("localhost","","");
  mysql_select_db("insitecreation") or die("database not found.");
  if($oConn)
    {
    //Update content
    if(isset($_POST["txtContent"]))
      {
      $sTitle=stripslashes($_POST["inpTitle"]);//remove slashes (/)
      $sTitle=ereg_replace("'","''",$sTitle);//fix SQL

      $sContent=stripslashes($_POST['txtContent']);//remove slashes (/)
      $sContent=ereg_replace("'","''",$sContent);//fix SQL

      $sSQL="Update content set content='$sContent', title='$sTitle' where id=1";
      mysql_query($sSQL,$oConn);
      }

    //Load content
    $sSQL="Select title,content From content where id=1";
    $oResult=mysql_query($sSQL,$oConn);
    if($oResult)
      {
      $sTitle=mysql_result($oResult,0,"title");
      $sContent=mysql_result($oResult,0,"content");
      }
    }
  mysql_close($oConn);
  ?>
  <script language=JavaScript src='../scripts/innovaeditor.js'></script>
</head>
<body>

<h4>Updating a Database (PHP example) - <a href="../default.htm">Back</a></h4>

<form method="post" action="default.php" id="Form1">
  Title: <input type=text name=inpTitle id=inpTitle value="<?php  if(isset($sTitle)) echo $sTitle; ?>"><br><br>
  <textarea id="txtContent" name="txtContent" rows=4 cols=30>
  <?php
  function encodeHTML($sHTML)
    {
    $sHTML=ereg_replace("&","&amp;",$sHTML);
    $sHTML=ereg_replace("<","&lt;",$sHTML);
    $sHTML=ereg_replace(">","&gt;",$sHTML);
    return $sHTML;
    }
  if(isset($sContent)) echo encodeHTML($sContent);
  ?>
  </textarea>

  <script>
    var oEdit1 = new InnovaEditor("oEdit1");
    oEdit1.REPLACE("txtContent");//Specify the id of the textarea here
  </script>

  <input type="submit" value=" SAVE ">
</form>

</body>
</html>