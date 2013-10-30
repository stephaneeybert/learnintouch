<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <style>
    body{font:11px verdana,arial,sans-serif;}
    a{color:#0000cc;font-size:xx-small;}
  </style>

  <!-- STEP 1: Editor Localization: Include language file -->
  <?php
  $sLanguage="";
  if(isset($_POST["selLanguage"])) $sLanguage=$_POST["selLanguage"];
  if($sLanguage<>"") echo "<script language=JavaScript src='../scripts/language/".$sLanguage."/editor_lang.js'></script>";
  ?>
  <!--
  Or you can specify certain language directly using (for example) :
  <script language=JavaScript src='../scripts/language/german/editor_lang.js'></script>
  -->

  <script language=JavaScript src='../scripts/innovaeditor.js'></script>
</head>
<body>

<h4>Localization (PHP example) - <a href="../default.htm">Back</a></h4>

<form method="post" action="default.php" id="Form1">
  Select Language:
  <select ID="selLanguage" NAME="selLanguage" onchange="document.getElementById('btnSubmit').click()">
    <option value=""    <?php if($sLanguage=="") echo "selected"; ?> >English</option>
    <option value="da-DK"  <?php if($sLanguage=="da-DK") echo "selected"; ?> >Danish</option>
    <option value="nl-NL" <?php if($sLanguage=="nl-NL") echo "selected"; ?> >Dutch</option>
    <option value="fi-FI" <?php if($sLanguage=="fi-FI") echo "selected"; ?> >Finnish</option>
    <option value="fr-FR"  <?php if($sLanguage=="fr-FR") echo "selected"; ?> >French</option>
    <option value="de-DE"  <?php if($sLanguage=="de-DE") echo "selected"; ?> >German</option>
    <option value="zh-CHS"  <?php if($sLanguage=="zh-CHS") echo "selected"; ?> >Chinese Simplified</option>
    <option value="zh-CHT"  <?php if($sLanguage=="zh-CHT") echo "selected"; ?> >Chinese Traditional</option>
    <option value="nn-NO" <?php if($sLanguage=="nn-NO") echo "selected"; ?> >Norwegian</option>
    <option value="es-ES" <?php if($sLanguage=="es-ES") echo "selected"; ?> >Spanish</option>
    <option value="sv-SE" <?php if($sLanguage=="sv-SE") echo "selected"; ?> >Swedish</option>
    <option value="it-IT" <?php if($sLanguage=="it-IT") echo "selected"; ?> >Italian</option>
  </select>

  <br><br>


  <textarea id="txtContent" name="txtContent" rows=4 cols=30>
  <?php
  function encodeHTML($sHTML)
    {
    $sHTML=ereg_replace("&","&amp;",$sHTML);
    $sHTML=ereg_replace("<","&lt;",$sHTML);
    $sHTML=ereg_replace(">","&gt;",$sHTML);
    return $sHTML;
    }

  if(isset($_POST["txtContent"]))
    {
    $sContent=stripslashes($_POST['txtContent']); /*** remove (/) slashes ***/
    echo encodeHTML($sContent);
    }
  ?>
  </textarea>

  <script>
    var oEdit1 = new InnovaEditor("oEdit1");

    //STEP 2: Asset Manager Localization: Add querystring lang=en-US/da-DK/nl-NL...
    oEdit1.cmdAssetManager="modalDialogShow('/Editor315/assetmanager/assetmanager.php?lang=<?php echo $sLanguage?>',640,465)";//Use "relative to root" path
    /*
    Or you can specify certain language directly using (for example) :
    oEdit1.cmdAssetManager="modalDialogShow('/Editor/assetmanager/assetmanager.php?lang=de-DE',640,465)";
    */

    oEdit1.REPLACE("txtContent");
  </script>

  <input type="submit" value="Submit" id="btnSubmit">
</form>























<!-- SPECIAL THANKS -->

<hr>
<ul>
<li>
  <b>Dansk Version</b>:<br>
  Special thanks to: <br>
  Lars Hansen / <a href="http://www.knappekragh.dk">www.knappekragh.dk</a><br><br>
</li>
<li>
  <b>Dutch Version</b>:<br>
  Special thanks to: <br>
  Mike van den Berg<br><br>
</li>
<li>
  <b>Finnish Version</b>:<br>
  Special thanks to: <br>
  Mika Nieminen / <a href="http://www.itvision.org">www.itvision.org</a><br><br>
</li>
<li>
  <b>French Version</b>:<br>
  Special thanks to: <br>
  Roland GALZY / <a href="http://www.mediadoo.com">www.mediadoo.com</a><br><br>
</li>
<li>
  <b>German Version</b>:<br>
  Special thanks to: <br>
  Philipp Alexander Starkl / <a href="http://www.ddesign.at">www.ddesign.at</a><br><br>
</li>
<li>
  <b>Chinese Version</b>:<br>
  Special thanks to: <br>
  Jimsun<br>
  Agog Digital Marketing Strategy<br>
  Provides quality search engine optimization and web development service.<br>
  <a href="http://www.agogdigital.com">www.agogdigital.com</a><br><br>
</li>
<li>
  <b>Norwegian Version</b>:<br>
  Special thanks to: <br>
  Per Willy Buffelen<br><br>
</li>
<li>
  <b>Spanish Version</b>:<br>
  Special thanks to: <br>
  Fredi Vinyals<br><br>
</li>
<li>
  <b>Swedish Version</b>:<br>
  Special thanks to: <br>
  Tomas Wikers / <a href="http://www.wikers.com">www.wikers.com</a><br><br>
</li>
<li>
  <b>Italian Version</b>:<br>
  Special thanks to: <br>
  Sam Morgan / <a href="http://www.wiredeyes.com">WIREDEYES - Internet Consultancy</a><br><br>
</li>
</ul>


</body>
</html>