<%
file = "content.htm" 'File to edit

dim fso
Set fso = Server.CreateObject("Scripting.FileSystemObject")

if Not IsEmpty(Request.Form("txtContent")) then
  'Save
  sContent = Request.Form("txtContent")
  const ForWriting = 2
  const Create = True
  dim tso
  set tso = fso.OpenTextFile(Server.MapPath(file), ForWriting, Create)
  tso.write sContent
  tso.close
  set tso = Nothing
end if
%>
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

<h4>Editing a File (ASP example) - <a href="../default.htm">Back</a></h4>

<form method="post" action="default.asp" id="Form1">

  <textarea id="txtContent" name="txtContent" rows=4 cols=30>
    <%
    function encodeHTML(sHTML)
      sHTML=replace(sHTML,"&","&amp;")
      sHTML=replace(sHTML,"<","&lt;")
      sHTML=replace(sHTML,">","&gt;")
      encodeHTML=sHTML
    end function

    const ForReading=1
    dim ts
    set ts = fso.OpenTextFile(Server.MapPath(file), ForReading)
    while not ts.AtEndOfStream
      Response.Write encodeHTML(ts.readline) & VbCrLf
    wend
    set ts=nothing
    set fso=nothing
    %>
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
