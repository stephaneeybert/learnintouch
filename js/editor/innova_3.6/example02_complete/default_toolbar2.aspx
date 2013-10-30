<%@ Page Language="vb" ValidateRequest="false" Debug="true" %>
<%@ Register TagPrefix="editor" Assembly="WYSIWYGEditor" namespace="InnovaStudio" %>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
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
                
            '***************************************************
            '   SETTING EDITOR DIMENSION (WIDTH x HEIGHT)
            '***************************************************
            
            oEdit1.Width = 700
            oEdit1.Height=450
            

            '***************************************************
            '   ADDING CUSTOM BUTTONS
            '***************************************************
        
            oEdit1.ToolbarCustomButtons.add(New CustomButton("CustomName1", "alert('Command 1 here.')", "Caption 1 here", "btnCustom1.gif"))
            oEdit1.ToolbarCustomButtons.add(New CustomButton("CustomName2", "alert('Command 2 here.')", "Caption 2 here", "btnCustom2.gif"))
            oEdit1.ToolbarCustomButtons.add(New CustomButton("CustomName3", "alert('Command 3 here.')", "Caption 3 here", "btnCustom3.gif"))

            '***************************************************
            '   RECONFIGURE TOOLBAR BUTTONS
            '***************************************************
 
            Dim grpEdit As ISGroup = New ISGroup("grpEdit", "", New String() {"Undo", "Redo", "FullScreen", "RemoveFormat", "BRK", "Cut", "Copy", "Paste", "PasteWord", "PasteText", "XHTMLSource"})
            Dim grpFont As ISGroup = New ISGroup("grpFont", "", New String() {"FontName", "FontSize", "Style", "BRK", "Bold", "Italic", "Underline", "Strikethrough", "Superscript", "ForeColor", "BackColor", "StyleAndFormatting"})
            Dim grpPara As ISGroup = New ISGroup("grpPara", "", New String() {"Paragraph", "Indent", "Outdent", "StyleAndFormatting", "BRK", "JustifyLeft", "JustifyCenter", "JustifyRight", "JustifyFull", "Numbering", "Bullets"})
            Dim grpInsert As ISGroup = New ISGroup("Insert", "", New String() {"Hyperlink", "Bookmark", "BRK", "Image"})
            Dim grpTables As ISGroup = New ISGroup("grpTables", "", New String() {"Table", "BRK", "Guidelines"})
            Dim grpResource as ISGroup = new ISGroup("grpResource", "", new String() {"InternalLink", "BRK", "CustomObject"})

            Dim grpMedia As ISGroup = New ISGroup("grpMedia", "", New String() {"Media", "BRK", "Flash"})
            Dim grpMisc As ISGroup = New ISGroup("grpStyles", "", New String() {"Characters", "Line", "Absolute", "BRK", "CustomTag"})
            Dim grpCustom = new ISGroup("grpCustom", "", New String() {"CustomName1", "CustomName2", "BRK", "CustomName3"})
            
            Dim tabHome = new ISTab("tabHome", "Home")
            tabHome.Groups.AddRange(New ISGroup() {grpEdit, grpFont, grpPara, grpInsert, grpTables})
            
            oEdit1.ToolbarTabs.add(tabHome)
            
            Dim tabStyle = new ISTab("tabStyle", "Objects")
            tabStyle.Groups.AddRange(New ISGroup() {grpResource, grpMedia, grpMisc, grpCustom})
            
            oEdit1.ToolbarTabs.add(tabStyle)
            
            '***************************************************
            '   OTHER SETTINGS
            '***************************************************
            oEdit1.onSave="document.forms.Form1.elements.btnSubmit.click();"
            
            oEdit1.Css="style/test.css" 'Specify external css file here
            
            'oEdit1.EditingStyles.add(new EditingStyle("BODY",false,"","font-family:Verdana,Arial,Helvetica;font-size:x-small;"))
            'oEdit1.EditingStyles.add(new EditingStyle(".ScreenText",true,"Screen Text","font-family:Tahoma;"))
            'oEdit1.EditingStyles.add(new EditingStyle(".ImportantWords",true,"Important Words","font-weight:bold;"))
            'oEdit1.EditingStyles.add(new EditingStyle(".Highlight",true,"Highlight","font-family:Arial;color:red;"))
                    
            oEdit1.AssetManagerWidth = 570
            oEdit1.AssetManagerHeight = 510
            oEdit1.AssetManager = "/Editor/assetmanager/assetmanager.aspx?c=en-US" 

            oEdit1.InternalLinkWidth = 365
            oEdit1.InternalLinkHeight = 270
            oEdit1.InternalLink = "links.htm"

            oEdit1.CustomObjectWidth = 365
            oEdit1.CustomObjectHeight = 270
            oEdit1.CustomObject = "objects.htm"

            oEdit1.CustomTags.add(new Param("First Name","{%first_name%}"))
            oEdit1.CustomTags.add(new Param("Last Name","{%last_name%}"))
            oEdit1.CustomTags.add(new Param("Email","{%email%}"))

            oEdit1.CustomColors = New String() {"#ff4500","#ffa500","#808000","#4682b4","#1e90ff","#9400d3","#ff1493","#a9a9a9"}

            oEdit1.EditMode = EditorModeEnum.XHTMLBody

        End Sub

        Sub Button1_Click(Source As System.Object, E As System.EventArgs)
            Label1.Text = oEdit1.Text
        End Sub
    </script>
</head>
<body>


    
<form id="Form1" method="post" runat="server">

    <h4>Full Features - Tabbed Toolbar (ASP.NET example) - <a href="../default.htm">Back</a></h4>
    
    <editor:wysiwygeditor 
        Runat="server"
        scriptPath="../scripts/"
        ID="oEdit1" />
        
    <asp:button runat="server" onclick="Button1_Click" Text="SUBMIT" ID="btnSubmit" />

    <asp:label id="Label1" runat="server"/>
</form>

</body>
</html>