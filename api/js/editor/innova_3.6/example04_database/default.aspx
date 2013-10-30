<%@ Page Language="vb" ValidateRequest="false" Debug="true" ResponseEncoding="utf-8" %>
<%@ Register TagPrefix="editor" Assembly="WYSIWYGEditor" namespace="InnovaStudio" %>
<%@ Import Namespace="System.Data" %>
<%@ Import Namespace="System.Data.OleDb" %>
<html>
<head>
    <style>
        body{font:11px verdana,arial,sans-serif;}
        a{color:#0000cc;font-size:xx-small;}
    </style>
    <script language="VB" runat="server">
        Dim sConn As String="Provider=Microsoft.Jet.OLEDB.4.0;Data Source=" & Server.MapPath("database/data2.mdb")
        Dim sSQL As String
        Dim oConn As OleDbConnection
        Dim oCommand As OleDbCommand
        Dim oDataReader As OleDbDataReader

        Sub Page_Load(Source As Object, E As EventArgs)
            If Not Page.IsPostBack Then
                oConn=New OleDbConnection(sConn)
                oConn.Open()

                sSQL="Select * From Content where id=1"
                oCommand=New OleDbCommand(sSQL,oConn)

                oDataReader=oCommand.ExecuteReader()
                Do While oDataReader.Read()=true
                    inpTitle.Text=oDataReader("title")
                    oEdit1.Text = oDataReader("content")
                Loop
                oDataReader.Close()
                oConn.Close()
            End If
        End Sub
            
        Sub Button1_Click(Source As Object, E As EventArgs)
            oConn=New OleDbConnection(sConn)
            oConn.Open()
                
            sSQL = "Update content set title='" & Replace(inpTitle.Text, "'", "''") & "', content='" & Replace(oEdit1.Text, "'", "''") & "' where id=1"
                
            oCommand=New OleDbCommand(sSQL,oConn)
            oCommand.ExecuteNonQuery()

            oConn.Close()
            End Sub
    </script>
</head>
<body>

<h4>Updating a Database (ASP.NET example) - <a href="../default.htm">Back</a></h4>
    
<form id="Form1" method="post" runat="server">
    Title: <asp:textbox id=inpTitle runat="server"/><br><br>
    <editor:wysiwygeditor 
        Runat="server"
        scriptPath="../scripts/"
        ID="oEdit1" />
    <asp:button runat="server" onclick="Button1_Click" Text=" SAVE " ID="btnSubmit"/>
</form>

</body>
</html>