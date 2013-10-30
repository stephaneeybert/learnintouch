<%@ Page Language="vb" ValidateRequest="false" Debug="true" ResponseEncoding="utf-8" %>
<%@ Register TagPrefix="editor" Assembly="WYSIWYGEditor" namespace="InnovaStudio" %>
<%@ Import Namespace="System.IO" %>
<html>
<head>
    <style>
        body{font:11px verdana,arial,sans-serif;}
        a{color:#0000cc;font-size:xx-small;}
    </style>
    <script language="VB" runat="server">
        Dim sFile As String = "content.htm"
        Private Sub Page_Load(Source As System.Object, E As System.EventArgs)
            If Not Page.IsPostBack Then
                Dim sr As StreamReader
                sr = File.OpenText(Server.MapPath(sFile))
                oEdit1.Text = sr.ReadToEnd()
                sr.Close()
            End If

            oEdit1.EditMode = EditorModeEnum.HTML
            oEdit1.btnStyles=true           
        End Sub
        Sub Button1_Click(Source As System.Object, E As System.EventArgs)
            Dim sr As New StreamWriter(Server.MapPath(sFile))
            sr.WriteLine(oEdit1.Text)
            sr.Close()
        End Sub
    </script>
</head>
<body>

<h4>Editing a File (ASP.NET example) - <a href="../default.htm">Back</a></h4>

<form id="Form1" method="post" runat="server">

    <editor:wysiwygeditor 
        Runat="server"      
        scriptPath="../scripts/"
        id="oEdit1" />
        
    <asp:button runat="server" onclick="Button1_Click" Text="Save" id="btnSubmit"/>
    <input type="button" value="view file" onclick="window.open('content.htm')">
</form>

</body>
</html>