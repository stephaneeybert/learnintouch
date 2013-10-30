import navmenu;

class navmenuExample extends navmenu {

  static var app: navmenu;

  // Parse the content of a menu
  static function parseNavmenuItems(navmenuItems, level) {

    var stringUtils = new StringUtils();

    var j = 0;
    var indentation = '';
    while(j < level) {
      indentation += '      ';
      j++;
      }

    var s = '';
    for (j = 0; j < navmenuItems.length; j++) {
      var navmenuItem = stringUtils.trim(navmenuItems[j]);
      var navmenuItemName = stringUtils.trim(navmenuItem['name']);
      var navmenuItemDescription = stringUtils.trim(navmenuItem['description']);
      var navmenuItemListOrder = stringUtils.trim(navmenuItem['listOrder']);
      var navmenuItemUrl = stringUtils.trim(navmenuItem['url']);
      var navmenuItemBlankTarget = stringUtils.trim(navmenuItem['blankTarget']);
      var navmenuItemSubItems = stringUtils.trim(navmenuItem['subitems']);

      s += indentation + 'level ' + level + ' ' + j + '::\n' + indentation + 'name = ' + navmenuItemName + '\n' + indentation + 'descr = ' + navmenuItemDescription + '\n' + indentation + 'listorder = ' + navmenuItemListOrder + '\n' + indentation + 'URL = ' + navmenuItemUrl + '\n' + indentation + 'blanktarget = ' + navmenuItemBlankTarget + '\n';

      if (navmenuItemSubItems.length > 0) {
        var subMenu = parseNavmenuItems(navmenuItemSubItems, level + 1);
        s += indentation + 'sublevel::\n' + subMenu;
        }

      }

    return s;
    }

  static function renderNavmenu(navmenuItems) {

    var s = parseNavmenuItems(navmenuItems, 0);

    _root.createTextField("tf", 0, 0, 0, 800, 600); _root.tf.text = s;

    }

  static function main(){
    app = new navmenu(renderNavmenu);
    }

  }
