class parseUrl {

  var url: String;
  var urlBits: Array;
  var strParameters: String;
  var parameters: Array;
  var parameter: String;
  public var languageCode:String = '';
  public var wddxname:String = '';

  function parseUrl() {

    // Get the parameters
    url = _root._url;
    urlBits = url.split('?');   
    if (urlBits.length > 1) {
      strParameters = urlBits[1];
      parameters = strParameters.split('&');

      if (parameters.length > 1) {
        // Get the language code
        parameter = parameters[0].split('=');
        if (parameter.length > 1) {
          languageCode = parameter[1];
          }
        // Get the wddx filename
        parameter = parameters[1].split('=');
        if (parameter.length > 1) {
          wddxname = parameter[1];
          }
        }

      }
    }

  }
