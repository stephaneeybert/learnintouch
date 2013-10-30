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

<h4>Full Features - Toolbar Reconfigured & Custom Buttons (PHP example) - <a href="../default.htm">Back</a></h4>

<form method="post" action="default_toolbar.php" id="Form1">

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

    oEdit1.width=725;
    oEdit1.height=450;

    /***************************************************
    ADDING CUSTOM BUTTONS
    ***************************************************/

    oEdit1.arrCustomButtons = [["CustomName1","alert('Command 1 here.')","Caption 1 here","btnCustom1.gif"],
    ["CustomName2","alert(\"Command '2' here.\")","Caption 2 here","btnCustom2.gif"],
    ["CustomName3","alert('Command \"3\" here.')","Caption 3 here","btnCustom3.gif"]]


    /***************************************************
    RECONFIGURE TOOLBAR BUTTONS
    ***************************************************/

    //Use regular toolbar

    oEdit1.useTab = false;

    oEdit1.features=["Save","FullScreen","Preview","Print", "Search","SpellCheck",
    "Table","Guidelines","Absolute", "Flash","Media","InternalLink","CustomObject",
    "Form","Characters","ClearAll","XHTMLFullSource","XHTMLSource","BRK",
    "Cut","Copy","Paste","PasteWord","PasteText",
    "Undo","Redo","Hyperlink","Bookmark","Image",
    "JustifyLeft","JustifyCenter","JustifyRight","JustifyFull",
    "Numbering","Bullets","Indent","Outdent", "LTR","RTL",
    "Line","RemoveFormat","BRK",
    "StyleAndFormatting","TextFormatting","ListFormatting",
    "BoxFormatting","ParagraphFormatting","CssText","Styles",
    "CustomTag","Paragraph","FontName","FontSize",
    "Bold","Italic","Underline","Strikethrough", "Superscript","Subscript",
    "ForeColor","BackColor",
    "CustomName1","CustomName2","CustomName3"];// => Custom Button Placement


    /***************************************************
    OTHER SETTINGS
    ***************************************************/
    oEdit1.css="style/test.css";//Specify external css file here

    oEdit1.cmdAssetManager = "modalDialogShow('/Editor/assetmanager/assetmanager.php',640,465)"; //Command to open the Asset Manager add-on.
    oEdit1.cmdInternalLink = "modelessDialogShow('links.htm',365,270)"; //Command to open your custom link lookup page.
    oEdit1.cmdCustomObject = "modelessDialogShow('objects.htm',365,270)"; //Command to open your custom content lookup page.

    oEdit1.arrCustomTag=[["First Name","{%first_name%}"],
    ["Last Name","{%last_name%}"],
    ["Email","{%email%}"]];//Define custom tag selection

    oEdit1.customColors=["#ff4500","#ffa500","#808000","#4682b4","#1e90ff","#9400d3","#ff1493","#a9a9a9"];//predefined custom colors

    oEdit1.mode="XHTMLBody"; //Editing mode. Possible values: "HTMLBody" (default), "XHTMLBody", "HTML", "XHTML"

    oEdit1.REPLACE("txtContent");
  </script>

  <input type="submit" value="Submit" id="btnSubmit">

</form>

</body>
</html>