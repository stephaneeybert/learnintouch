// Retrieve the wddx packet into an actionscript object
// Get the url parameters
// Read the wddx file
// Parse the file into an object
// Run the function with the object

// Import the wddx library
import WDDX;

class wddxToObject {

  var wddxObject: Object;
  var xmlUtils: Object;
  var wddxUtils: Object;
  var userFunc: Function = function() {};
  var sourceClass: Object;

  function wddxToObject(wddxname, sClass, userFunction: Function) {

    // Create an xml utility object
   	xmlUtils = new XML();
	  xmlUtils.ignoreWhite = true;
	  xmlUtils.AS = this;

    // Create the wddx utility
   	wddxUtils = new WDDX();
	  userFunc = userFunction;
	  sourceClass = sClass;

    xmlUtils.onLoad = function(success:Boolean) {

	    // Check for loading success
      if(success){
        // Retrieve the packet into an actionscript object
	      var wddxObject = this.AS.wddxUtils.deserialize(this);

	      // Call user function and pass generated object (named wddxObject) and language code into it
        this.AS.userFunc.call(this.AS.sourceClass, wddxObject);
        }
      }

    // Check that a wddx filename has been received
    if (typeof(wddxname) != '' && typeof(wddxname) != 'undefined') {

      // Load the wddx file
      xmlUtils.load(wddxname);

      // Delete the utility
      delete(xmlUtils);
      }
    }

  }
