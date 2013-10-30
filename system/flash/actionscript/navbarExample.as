import navbar;

class navbarExample extends navbar {

  static var app: navbar;

  static function renderNavbar(navbarItems) {

    var s = '';

    var stringUtils = new StringUtils();

    for (var j = 0; j < navbarItems.length; j++) {
      var navbarItem = navbarItems[j];
      var navbarItemName = stringUtils.trim(navbarItem['name']);
      var navbarItemDescription = stringUtils.trim(navbarItem['description']);
      var navbarItemHide = stringUtils.trim(navbarItem['hide']);
      var navbarItemListOrder = stringUtils.trim(navbarItem['listOrder']);
      var navbarItemUrl = stringUtils.trim(navbarItem['url']);
      var navbarItemBlankTarget = stringUtils.trim(navbarItem['blankTarget']);
      var navbarItemTemplateModelId = stringUtils.trim(navbarItem['templateModelId']);

      s += '\nName ' + navbarItemName + ' Description ' + navbarItemDescription + ' Hide ' + navbarItemHide + ' ListOrder ' + navbarItemListOrder + ' Url ' + navbarItemUrl + ' BlankTarget ' + navbarItemBlankTarget + ' TemplateModelId ' + navbarItemTemplateModelId;
      }

    StringUtils.trace(s);
    }

  static function main(){
    app = new navbar(renderNavbar);
    }

  }
