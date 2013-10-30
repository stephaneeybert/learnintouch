import navlink;

class navlinkExample extends navlink {

  static var app: navlink;

  static function renderNavlink(navlink) {

    var stringUtils = new StringUtils();

    var navlinkText = navlink['text'];
    var navlinkDescription = navlink['description'];
    var navlinkUrl = navlink['url'];
    var navlinkBlankTarget = navlink['blankTarget'];
    var navlinkLanguage = stringUtils.trim(navlink['language']);
    var navlinkTemplateModelId = stringUtils.trim(navlink['templateModelId']);

    var s = 'Text ' + navlinkText + ' Description ' + navlinkDescription + ' Url ' + navlinkUrl + ' BlankTarget ' + navlinkBlankTarget + ' Language ' + navlinkLanguage + ' navlinkTemplateModelId ' + navlinkTemplateModelId; 

    _root.createTextField("tf", 0, 0, 0, 800, 600); _root.tf.text = s;

    }

  static function main(){
    app = new navlink(renderNavlink);
    }

  }
