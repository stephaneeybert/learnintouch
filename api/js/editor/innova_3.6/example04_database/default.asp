<%
dim adoCn
dim adoRs
set adoCn = Server.CreateObject("ADODB.Connection")
adoCn.Open "Provider=Microsoft.Jet.OLEDB.4.0;Data Source=" & Server.MapPath("database/data.mdb")
set adoRs = Server.CreateObject("ADODB.Recordset")

if Not IsEmpty(Request.Form("txtContent")) then
  'Update content
  sTitle = Replace(Request.Form("inpTitle"),"'","''")
  sContent = Replace(Request.Form("txtContent"),"'","''")
  adoCn.Execute "Update content set content='" & sContent & "', title='" & sTitle & "' where id=1"
end if

'Load content
set adoRs = adoCn.Execute("Select * From Content where id=1")
If not adoRs.EOF then
  sTitle = adoRs("title")
  sContent = adoRs("content")
End If
adoRs.Close

set adoRs = nothing
adoCn.Close
set adoCn = nothing
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

<h4>Updating a Database (ASP example) - <a href="../default.htm">Back</a></h4>

<form method="post" action="default.asp" id="Form1">
  Title: <input type="text" name="inpTitle" size=40 value="<%=sTitle%>" ID="Text1"><br><br>

  <textarea id="txtContent" name="txtContent" rows=4 cols=30>
  <%
  function encodeHTML(sHTML)
    sHTML=replace(sHTML,"&","&amp;")
    sHTML=replace(sHTML,"<","&lt;")
    sHTML=replace(sHTML,">","&gt;")
    encodeHTML=sHTML
  end function

  Response.Write encodeHTML(sContent)
  %>
  </textarea>

  <script>
    var oEdit1 = new InnovaEditor("oEdit1");
    oEdit1.REPLACE("txtContent");//Specify the id of the textarea here
  </script>

  <input type="submit" value=" SAVE ">
</form>

</body>
</html>