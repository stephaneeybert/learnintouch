
// Hide some menus
// The state of each menu (hidden or visible) is stored in a cookie
function navmenuHideAllMenus(rootMenuId) {
  // Use the current state if any
  var cookieMenus = getCookie("navmenu");

  if (cookieMenus && cookieMenus.indexOf('menu') != -1) {
    var listMenus = cookieMenus.split("|");
	
	  for (var i = 0; i < listMenus.length; i++) {
      var menu = listMenus[i].split(":");
	    if (menu[1] == 1) {
	      navmenuHideMenu(menu[0], 0);
        }
      }
    } else {
    // Otherwise use the default state
    navmenuHideMenu(rootMenuId, 1);
    }
  }

// To ensure that even browsers that do not support javascript still display
// all the menus and sub menus, the initial hiding of the sub menus is done
// using some javascript instead of css
function navmenuHideMenu(menuId, flg) {

  var menu = document.getElementById(menuId);

  var subMenus = menu.getElementsByTagName("dd");

  for (var i = 0; i <= subMenus.length; i++) {
    if (subMenus[i]) {
	    if (flg == 1){
        navmenuStoreState(subMenus[i].id, 1);
		    }	 
      subMenus[i].style.display = 'none';
      }
    }
	if (flg == 0){
	  menu.style.display='none';
	  }
  }

// Display or hide a menu
function navmenuDisplayHide(menuId) {
  
  var menu = document.getElementById(menuId);
  if (menu.style.display=='none') {
    // Display the menu
    navmenuStoreState(menuId, 0);
    menu.style.display='block';
    } else if (menu.style.display=='block') {
    // Hide the menu
    navmenuStoreState(menuId, 1);
    menu.style.display='none';
    }
  }

// Get the hidden state of a menu
function navmenuGetState(menuId) {
  var state = 0;

  var cookieMenus = getCookie("navmenu");

  if (cookieMenus && cookieMenus.indexOf('menu') != -1) {
    var listMenus = cookieMenus.split("|");
    for (var i = 0; i < listMenus.length; i++) {
      var menu = listMenus[i].split(":");
      if (menu[0] == menuId) {
        state = menu[1];
        }
      }
    }

  return(state);
  }

  var cookieMenus;

// Set the hidden state of a menu
function navmenuStoreState(menuId, state) {
  var cookieMenus = getCookie("navmenu");

  if (cookieMenus && cookieMenus.indexOf('menu') != -1) {
	  if (cookieMenus && cookieMenus.indexOf(menuId) != -1) {
      // Update a menu in the cookie
      var menus_id = new Array();
	    var menus_state = new Array();
      var listMenus = cookieMenus.split("|");
    
	    for (var i = 0; i < listMenus.length; i++) {
        var menu = listMenus[i].split(":");
		
		    if (menu[0] == menuId) {
		      menus_id[i] = menuId;
			    menus_state[i] = state;
          } else {
		      menus_id[i] = menu[0];
			    menus_state[i] = menu[1];
		      }
        }
      var updateListMenus = '';

      for (var i = 1; i < listMenus.length ; i++) {
        updateListMenus = updateListMenus + menus_id[i-1] + ':' + menus_state[i-1] + '|';
        }
      updateListMenus = updateListMenus + menus_id[listMenus.length-1] + ':' + menus_state[listMenus.length-1];
      } else {
      // Add a menu to the cookie
      updateListMenus = cookieMenus + '|' + menuId + ':' + state;
      }
    } else {
    // Add the first menu to the cookie
    updateListMenus = menuId + ':' + state;
    }

  setCookie("navmenu", updateListMenus, 1);
  }
