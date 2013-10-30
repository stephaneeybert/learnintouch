function loadTxt()
  {
    var txtLang = document.getElementsByName("txtLang");
    txtLang[0].innerHTML = "Class name";
    txtLang[1].innerHTML = "Title text";
    
    document.getElementById("btnOk").value = " ok ";  
    document.getElementById("btnClose").value = "close";
  }
function getTxt(s)
    {
    switch(s)
        {
        case "": return "";
        default: return "";
        }
    }  
function writeTitle()
  {
  document.write("<title>Apply Tooltip</title>")
  }