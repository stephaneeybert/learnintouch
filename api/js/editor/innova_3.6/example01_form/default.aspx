<%@ Page Language="vb" ValidateRequest="false" Debug="true" %>
<%@ Register TagPrefix="editor" Assembly="WYSIWYGEditor" namespace="InnovaStudio" %>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style>
        body{font:11px verdana,arial,sans-serif;}
        a{color:#0000cc;font-size:xx-small;}
    </style>
    
    <script language="VB" runat="server">
        Sub Page_Load(Source As Object, E As EventArgs)
            If Not Page.IsPostBack Then
                oEdit1.Text = "<h3>Hello World!</h3>"
            End If
        End Sub

        Sub Button1_Click(Source As System.Object, E As System.EventArgs)
            Label1.Text = oEdit1.Text
        End Sub
    </script>
</head>
<body>

<h4>Using in a Web Form (ASP.NET example) - <a href="../default.htm">Back</a></h4>
    
<form id="Form1" method="post" runat="server">

    <editor:wysiwygeditor 
        Runat="server"
        scriptPath="../scripts/"       
        ID="oEdit1" />
        
    <asp:button runat="server" onclick="Button1_Click" Text="SUBMIT" />
    <br><br>
    <asp:label id="Label1" runat="server"/>
</form>

</body>
</html>