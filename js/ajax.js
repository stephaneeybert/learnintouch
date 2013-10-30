// Send a GET asynchronous request to the server
function ajaxAsynchronousRequest(url, clientFunction) {
	var xhr = ajaxNewXMLHttpRequest();
	
	xhr.open("GET", url);

	xhr.onreadystatechange = function() {
		if (xhr.readyState != 4) return;
		if (xhr.status != 200) return;
    if (clientFunction) {
      if ('responseText' in xhr) {
		    clientFunction(xhr.responseText);
      } else {
		    clientFunction('');
      }
    }
	}

	xhr.send(null);
}

/*
An example

<script type="text/javascript">
function clientUpdate(responseText) {
	document.getElementById("myElementId").innerHTML = responseText;
}
</script>
<div id="myElementId"></div>
<button onclick="ajaxAsynchronousRequest('service.php', clientUpdate);">Click Me</button>
*/

// Send a POST asynchronous request to the server
function ajaxAsynchronousPOSTRequest(url, params, clientFunction) {
	var xhr = ajaxNewXMLHttpRequest();
	
	xhr.open("POST", url);

  // Create the params string
  var strParams = '';
  for (var key in params) {
    if (strParams.length > 0) {
      strParams += "&";
    }
    strParams += key + "=" + params[key];
  }

  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

	xhr.onreadystatechange = function() {
		if (xhr.readyState != 4) return;
		if (xhr.status != 200) return;
    if (clientFunction) {
      if ('responseText' in xhr) {
		    clientFunction(xhr.responseText);
      } else {
		    clientFunction('');
      }
    }
	}

	xhr.send(strParams);
}

// Create an XMLHttpRequest object
function ajaxNewXMLHttpRequest() {
	var xhr;

	try {
		xhr = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
	   try {
	        xhr = new ActiveXObject("Microsoft.XMLHTTP");
	    } catch (E) {
	        xhr = false;
	    }
	}
	
	if (!xhr && typeof XMLHttpRequest != 'undefined') {
		xhr = new XMLHttpRequest();
	}
	
	return xhr;
}

