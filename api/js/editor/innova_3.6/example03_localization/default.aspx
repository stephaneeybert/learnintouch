<%@ Page Language="vb" ValidateRequest="false" Debug="true" %>
<%@ Register TagPrefix="editor" Assembly="WYSIWYGEditor" namespace="InnovaStudio" %>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style>
    body{font:11px verdana,arial,sans-serif;}
    a{color:#0000cc;font-size:xx-small;}
</style>

<script language="VB" runat="server">
    Sub Page_Load(Source As System.Object, E As System.EventArgs)
    
        'STEP 1: Editor Localization: Set Language property
        oEdit1.Language=selLanguage.Value 
        'Or you can specify certain language directly using (for example) :
        'oEdit1.Language=selLanguage.Value = "de-DE"
        
        'STEP 2: Asset Manager Localization: Add querystring c=en-US/da-DK/nl-NL...
        oEdit1.AssetManager="/Editor/assetmanager/assetmanager.aspx?c=" & selLanguage.Value   
        'Or you can specify certain language directly using (for example) :
        'oEdit1.AssetManager="/Editor/assetmanager/assetmanager.aspx?c=en-US"
        
        oEdit1.AssetManagerWidth=570
        oEdit1.AssetManagerHeight=510
    End Sub
    
    Sub Button1_Click(Source As System.Object, E As System.EventArgs)

    End Sub
</script>

</head>
<body>

<h4>Localization (ASP.NET example) - <a href="../default.htm">Back</a></h4>

<form id="Form1" method="post" runat="server">
    Select Language:
    <select ID="selLanguage" NAME="selLanguage" runat=server onchange="btnSubmit.click()">
        <option value="">English</option>
        <option value="da-DK">Danish</option>
        <option value="nl-NL">Dutch</option>
        <option value="fi-FI">Finnish</option>
        <option value="fr-FR">French</option>
        <option value="de-DE">German</option>      
        <option value="zh-CHS">Chinese Simplified</option>
        <option value="zh-CHT">Chinese Traditional</option>       
        <option value="nn-NO">Norwegian</option>
        <option value="es-ES">Spanish</option>
        <option value="sv-SE">Swedish</option>
        <option value="it-IT">Italian</option>
    </select>
    <br><br>
    
    <editor:wysiwygeditor 
        Runat="server"
        scriptPath="../scripts/"
        Text="<h1>Hello World!<h1>" 
        ID="oEdit1" />
        
    <asp:button runat="server" onclick="Button1_Click" Text="SUBMIT" ID="btnSubmit"/>
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