import parseUrl;
import wddxToObject;
import StringUtils;

class navlink {

  var renderingFunction = '';
  var languageCode:String = '';
  var wddxname:String = '';

  // Parse the content of a NAVIGATION LINK wddx file
  function parseContent(wddxObject) {

    var navlinks = wddxObject['navlinkArray'];

    var stringUtils = new StringUtils();

    for (var i in navlinks) {
      var navlink = navlinks[i];

      // Get the language
      var navlinkLanguage = stringUtils.trim(navlink['language']);

      // Use the content of the wddx file specific to the language specified in parameter
      // Otherwise use the content of the wddx file of the language set to 0 if any
      if (navlinkLanguage == this.languageCode || navlinkLanguage == '0') {
        // Call the rendering function
        this.renderingFunction(navlink);
        }
      }
    }

  function navlink(renderingFunction) {
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
