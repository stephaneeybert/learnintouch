// Create a dialog popup
function dialogPopupNew(url, title, left, top, width, height, scrollbars) {
  // Open the window
  var win = openWindow(url, title, left, top, width, height, scrollbars, 1);
  
  return(win);
  }
  
// Create a dialog popup after a delay
function autoDialogNew(url, delay, title, left, top, width, height, scrollbars) {
  return(setTimeout("openWindow('"+url+"', '"+title+"', '"+left+"', '"+top+"', '"+width+"', '"+height+"', '"+scrollbars+"', '1')", delay * 1000));
  }

// Create a content popup after a delay
function autoPopupNew(content, delay, title, left, top, width, height, scrollbars) {
  return(setTimeout("popupNew('"+content+"', '"+title+"', '"+left+"', '"+top+"', '"+width+"', '"+height+"', '"+scrollbars+"')", delay * 1000));
  }

// Create a popup
function popupNew(content, title, left, top, width, height, scrollbars) {

  // Open the window
  var win = openWindow("", title, left, top, width, height, scrollbars, 1);

  // Set the content
  win.document.open();
  win.document.write(content);
  win.document.close();

  return(win);
  }

// Open a popup browser window
function openWindow(url, title, left, top, width, height, scrollbars, resize) {
  var parms = "";  
  if (width)  parms += "width="+width;
  if (height) parms +=  ",height="+height;
  if (top || top == 0) parms += ",top="+top;
  if (left) parms += ",left="+left
  if (scrollbars) parms += ",scrollbars"
  if (resize) parms += ",resizable";
  if (parms.indexOf(',')==0) parms = parms.substring(1);
  win = window.open(url,title,parms);
  win.focus();

  return(win);
  }

