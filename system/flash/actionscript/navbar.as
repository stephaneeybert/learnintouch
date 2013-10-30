import parseUrl;
import wddxToObject;
import StringUtils;

class navbar {

  var renderingFunction = '';
  var languageCode:String = '';
  var wddxname:String = '';

  // Parse the content of a NAVIGATION BAR wddx file
  function parseContent(wddxObject) {

    var navbarLanguages = wddxObject['navbarLanguageArray'];

    var stringUtils = new StringUtils();

    for (var i in navbarLanguages) {
      var navbarLanguage = navbarLanguages[i];

      // Get the language
      var languageCode = stringUtils.trim(navbarLanguage['language']);

      // Use the content of the wddx file specific to the language specified in parameter
      // Otherwise use the content of the wddx file of the language set to 0 if any
      if (languageCode == this.languageCode || languageCode == '0') {
        // Get the items
        var navbarItems = navbarLanguage['0'];

        // Call the rendering function
        this.renderingFunction(navbarItems);
        }
      }
    }

  function navbar(renderingFunction) {
    // Set the rendering function
    this.renderingFunction = renderingFunction;

    // Get the url parameters
	  var parameters = new parseUrl();
	  this.languageCode = parameters.languageCode;
	  this.wddxname = parameters.wddxname;

    // Parse the file into an object
	  var t = new wddxToObject(this.wddxname, this, parseContent);
    }

  }
