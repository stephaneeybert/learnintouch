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
                
            '***************************************************
            '   SETTING EDITOR DIMENSION (WIDTH x HEIGHT)
            '***************************************************
            
            oEdit1.Width = 725
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
            
            oEdit1.UseTab = False
            
            oEdit1.ButtonFeatures = New String() { _
             "Save", "FullScreen", "Preview", "Print", "Search", "SpellCheck", _
             "Table", "Guidelines", "Absolute", _
             "Flash", "Media", "InternalLink", "CustomObject", _
             "Form", "Characters", "ClearAll", "XHTMLFullSource", "XHTMLSource", "BRK", _
             "Cut", "Copy", "Paste", "PasteWord", "PasteText", _
             "Undo", "Redo", "Hyperlink", "Bookmark", "Image", _
             "JustifyLeft", "JustifyCenter", "JustifyRight", "JustifyFull", _
             "Numbering", "Bullets", "Indent", "Outdent", "LTR", "RTL", _
             "Line", "RemoveFormat", "BRK", _
             "StyleAndFormatting", "TextFormatting", "ListFormatting", _
             "BoxFormatting", "ParagraphFormatting", "CssText", "Styles", _
             "CustomTag", "Paragraph", "FontName", "FontSize", _
             "Bold", "Italic", "Underline", "Strikethrough", "Superscript", "Subscript", _
             "ForeColor", "BackColor", _
             "CustomName1", "CustomName2", "CustomName3"} ' => Custom Button Placement

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

    <h4>Full Features - Toolbar Reconfigured & Custom Buttons (ASP.NET example) - <a href="../default.htm">Back</a></h4>
    
    <editor:wysiwygeditor 
        Runat="server"
        scriptPath="../scripts/"
        ID="oEdit1" />
        
    <asp:button runat="server" onclick="Button1_Click" Text="SUBMIT" ID="btnSubmit" />

    <asp:label id="Label1" runat="server"/>
</form>

</body>
</html>