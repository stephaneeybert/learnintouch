/*
WDDX Serializer/Deserializer for Flash MX 2004 v1.0
-------------------------------------------------- 
	Translated to ActionScript 2.0  by
		Jonathan Buhacoff (jonathan@buhacoff.net)
		
	Created by 
		Branden Hall (bhall@figleaf.com)
		Dave Gallerizzo (dgallerizzo@figleaf.com)

	Based on code by
		Simeon Simeonov (simeons@allaire.com)
		Nate Weiss (nweiss@icesinc.com)

	Version History:
		8/10/2000 - First created
		9/5/2000  - Minor bug fix to the deserializer
		9/15/2000 - Commented out wddxserializetype creation in the 
					 deserializer as they are not needed in most cases.
		9/21/2000 - Simplified the use of the deserializer. No longer need
		            to create the XML object yourself and the load and
					onLoad methods are part of the WDDX class.
		9/30/2000 - Cleaned up code and removed load and onLoad methods.
					Updated sample code.
		12/28/2000 - Added new duplicate method to WddxRecordset object
		1/10/2001 - Fixed problem where serialization caused text to always drop to lower
					case. Thanks to Bill Tremblay for spotting this one!
		1/17/2001 - Minor update to the serializer code so that it properly adds text nodes.
					Thanks for the catch from Carlos Mathews! 
					Also, the deserialization of primitive types now results in primitives 
					rather than instances of the object wrapper
		1/20/2001 - Quick fix to the deserializer for recordsets so that for..in loops can get
					the elements of the recordset in the proper order.
		2/5/2001  - Fix to the string deserialization so that it handles special characters 
					properly, thanks to Spike Washburn for this one!
		11/9/2001 - Finished small optimizations, fixed encoding issues and fixed case preservation
					issue.
		11/16/01  - (bkruse@macromedia.com)- put all WDDX classes in Object.WDDX namespace to fix
					scoping issues.
		4/19/2002 - Fixed various small bugs with date and number serialization/deserialization
		4/19/2002 - Removed WDDX classes from WDDX namespace and put into _global namespace
		2/13/2004 - Translated to ActionScript 2.0.  Just place this in your class path, then write
					"import WDDX;" and then use according examples below.  
		5/11/2004 - Fixed the string problem in the AS 2.0 edition. 

	Authors notes: 
		Serialization:	
			- Create an instance of the wddx class
			- Call the serialize method, passing it the object your wish
			  to serialize, it will return an XML object filled with the
			  serialized object.


			Example:
				myXML = new XML();
				foo = new WDDX();
				myXML = foo.serialize(bar);
			 
		Deserializtion:
			- Get the XML you want to deserialize
			- Create an instance of the WDDX class
			- Call the serialize method of your WDDX
			  object and pass it your XML. It will return
			  the deserialized object.

			Example:
				myXML = new XML();
				//
				// XML is loaded here
				//
				foo = new WDDX();
				myObj = foo.deserialize(myXML);
			
		- Branden 9/30/00

*/
import WddxRecordset;
//-------------------------------------------------------------------------------------------------
//  Wddx object
//-------------------------------------------------------------------------------------------------
// Base wddx object
class WDDX {
	// Build some tables needed for CDATA encoding
	var et = null;
	var etRev = null;
	var at = null;
	var atRev = null;
	var timezoneString:String = null;
	var preserveVarCase:Boolean = null;
	var useTimezoneInfo:Boolean = null;
	var wddxPacket:Object = null;
	function WDDX() {
		et = new Object();
		etRev = new Object();
		at = new Object();
		atRev = new Object();
		timezoneString = new String();
		preserveVarCase = true;
		useTimezoneInfo = true;
		for (var i = 0; i<256; ++i) {
			if (i<32 && i != 9 && i != 10 && i != 13) {
				var hex = i.toString(16);
				if (hex.length == 1) {
					hex = "0"+hex;
				}
				et[i] = "<char code='"+hex+"'/>";
				at[i] = "";
			} else if (i<128) {
				et[i] = chr(i);
				at[i] = chr(i);
			} else {
				et[i] = "&#x"+i.toString(16)+";";
				etRev["&#x"+i.toString(16)+";"] = chr(i);
				at[i] = "&#x"+i.toString(16)+";";
				atRev["&#x"+i.toString(16)+";"] = chr(i);
			}
		}
		et[ord("<")] = "&lt;";
		et[ord(">")] = "&gt;";
		et[ord("&")] = "&amp;";
		etRev["&lt;"] = "<";
		etRev["&gt;"] = ">";
		etRev["&amp;"] = "&";
		at[ord("<")] = "&lt;";
		at[ord(">")] = "&gt;";
		at[ord("&")] = "&amp;";
		at[ord("'")] = "&apos;";
		at[ord("\"")] = "&quot;";
		atRev["&lt;"] = "<";
		atRev["&gt;"] = ">";
		atRev["&amp;"] = "&";
		atRev["&apos;"] = "'";
		atRev["&quot;"] = "\"";
		// Deal with timezone offsets
		var tzOffset = (new Date()).getTimezoneOffset();
		if (tzOffset>=0) {
			timezoneString = "-";
		} else {
			timezoneString = "+";
		}
		timezoneString += Math.floor(Math.abs(tzOffset)/60)+":"+(Math.abs(tzOffset)%60);
	}
	// Serialize a Flash object
	function serialize(rootObj) {
		//delete(wddxPacket);
		wddxPacket = new Object();
		var temp = new XML();
		var packet = new XML();
		packet.appendChild(temp.createElement("wddxPacket"));
		wddxPacket = packet.firstChild;
		wddxPacket.attributes["version"] = "1.0";
		wddxPacket.appendChild(temp.createElement("header"));
		wddxPacket.appendChild(temp.createElement("data"));
		if (serializeValue(rootObj, wddxPacket.childNodes[1])) {
			return packet;
		} else {
			return null;
		}
	}
	// Determine the type of a Flash object and serialize it
	function serializeValue(obj, node) {
		var bSuccess = true;
		var val = obj.valueOf();
		var tzString = null;
		var temp = new XML();
		//  null object
		if (obj == null) {
			node.appendChild(temp.createElement("null"));
			// string object
		} else if (typeof (val) == "string") {
			serializeString(val, node);
			//  numeric objects (number or date)
		} else if (typeof (val) == "number") {
			//  date object
			if (typeof (obj.getTimezoneOffset) == "function") {
				//  deal with timezone offset if asked to
				if (useTimezoneInfo) {
					tzString = timezoneString;
				}
				node.appendChild(temp.createElement("dateTime"));
				node.lastChild.appendChild(temp.createTextNode(obj.getFullYear()+"-"+(obj.getMonth()+1)+"-"+obj.getDate()+"T"+obj.getHours()+":"+obj.getMinutes()+":"+obj.getSeconds()+tzString));
				//  number object
			} else {
				node.appendChild((new XML()).createElement("number"));
				node.lastChild.appendChild((new XML()).createTextNode(val));
			}
			//  boolean object
		} else if (typeof (val) == "boolean") {
			node.appendChild(temp.createElement("boolean"));
			node.lastChild.attributes["value"] = val;
			//  actual objects
		} else if (typeof (obj) == "object") {
			//  if it has a built in serializer, use it
			if (typeof (obj.wddxSerialize) == "function") {
				bSuccess = obj.wddxSerialize(this, node);
				//  array object
			} else if (typeof (obj.join) == "function" && typeof (obj.reverse) == "function") {
				node.appendChild(temp.createElement("array"));
				node.lastChild.attributes["length"] = obj.length;
				for (var i = 0; bSuccess && i<obj.length; ++i) {
					bSuccess = serializeValue(obj[i], node.lastChild);
				}
				//  generic object
			} else {
				node.appendChild(temp.createElement("struct"));
				if (typeof (obj.wddxSerializationType) == 'string') {
					node.lastChild.attributes["type"] = obj.wddxSerializationType;
				}
				for (var prop in obj) {
					if (prop != "wddxSerializationType") {
						bSuccess = serializeVariable(prop, obj[prop], node.lastChild);
						if (!bSuccess) {
							break;
						}
					}
				}
			}
		} else {
			//  Error: undefined values or functions
			bSuccess = false;
		}
		//  Successful serialization
		return bSuccess;
	}
	// Serialize a Flash varible
	function serializeVariable(vname, obj, node) {
		var bSuccess = true;
		var temp = new XML();
		if (typeof (obj) != "function") {
			node.appendChild(temp.createElement("var"));
			node.lastChild.attributes["name"] = preserveVarCase ? serializeAttr(vname) : serializeAttr(vname.toLowerCase());
			bSuccess = serializeValue(obj, node.lastChild);
		}
		return bSuccess;
	}
	// Serialize a Flash String
	function serializeString(s, node) {
		var tempString = "";
		var temp = new XML();
		var max = s.length;
		node.appendChild(temp.createElement("string"));
		for (var i = 0; i<max; ++i) {
			tempString += et[ord(s.substring(i, i+1))];
		}
		node.lastChild.appendChild(temp.createTextNode(tempString));
	}
	// Serialize attributes of a Flash variable
	function serializeAttr(s) {
		var tempString = "";
		var max = s.length;
		for (var i = 0; i<max; ++i) {
			tempString += at[ord(s.substring(i, i+1))];
		}
		return tempString;
	}
	// wddx deserializer
	function deserialize(wddxPacket) {
		if (typeof (wddxPacket) != "object") {
			wddxPacket = new XML(wddxPacket);
		}
		var wddxRoot = new XML();
		var wddxChildren = new Array();
		var temp;
		var dataObj = new Object();
		// Get first node that is not Null
		while (wddxPacket.nodeName == null) {
			wddxPacket = wddxPacket.firstChild;
		}
		wddxRoot = wddxPacket;
		if (wddxRoot.nodeName.toLowerCase() == "wddxpacket") {
			wddxChildren = wddxRoot.childNodes;
			temp = 0;
			// dig down until we find the data node or run out of nodes
			while (wddxChildren[temp].nodeName.toLowerCase() != "data" && temp<wddxChildren.length) {
				++temp;
			}
			// if we found a data node then deserialize its contents
			if (temp<wddxChildren.length) {
				dataObj = deserializeNode(wddxChildren[temp].firstChild);
				return dataObj;
			} else {
				return null;
			}
		} else {
			return null;
		}
	}
	// deserialize a single node of a WDDX packet
	function deserializeNode(node) {
		// get the name of the node
		var nodeType = node.nodeName.toLowerCase();
		// number node 
		if (nodeType == "number") {
			var dataObj = node.firstChild.nodeValue;
			//	dataObj.wddxSerializationType = "number";
			return Number(dataObj);
			// boolean node
		} else if (nodeType == "boolean") {
			var dataObj = (String(node.attributes.value).toLowerCase() == "true");
			//	dataObj.wddxSerializationType = "boolean";
			return dataObj;
			// string node
		} else if (nodeType == "string") {
			var dataObj;
			if (node.childNodes.length>1) {
				// complex string
				dataObj = "";
				var i = 0;
				for (i=0; i<node.childNodes.length; i++) {
					if (node.childNodes[i].nodeType == 3) {
						//this is a text node
						dataObj = dataObj+deserializeString(node.childNodes[i].nodeValue);
					} else if (node.childNodes[i].nodeName == "char") {
						dataObj += chr(parseInt(node.childNodes[i].attributes["code"], 16));
					}
				}
			} else {
				// simple string
				dataObj = deserializeString(node.firstChild.nodeValue);
			}
			// dataObj.wddxSerializationType = "string";
			return dataObj;
			// array node
		} else if (nodeType == "array") {
			var dataObj = new Array();
			var temp = 0;
			for (var i = 0; i<node.attributes["length"]; ++i) {
				dataObj[i] = deserializeNode(node.childNodes[i].cloneNode(true));
			}
			// dataObj.wddxSerializationType = "array";
			return dataObj;
			// datetime node
		} else if (nodeType == "datetime") {
			var dtString = node.firstChild.nodeValue;
			var tPos = dtString.indexOf("T");
			var tzPos = dtString.indexOf("+");
			var dateArray = new Array();
			var timeArray = new Array();
			var tzArray = new Array();
			var dataObj = new Date();
			if (tzPos == -1) {
				tzPos = dtString.lastIndexOf("-");
				if (tzPos<tPos) {
					tzPos = -1;
				}
			}
			// slice the datetime node value into the date, time, and timezone info
			dateArray = (dtString.slice(0, tPos)).split("-");
			timeArray = (dtString.slice(tPos+1, tzPos)).split(":");
			tzArray = (dtString.slice(tzPos)).split(":");
			// set the time and date of the object
			dataObj.setFullYear(parseInt(dateArray[0]), parseInt(dateArray[1])-1, parseInt(dateArray[2]));
			dataObj.setHours(parseInt(timeArray[0]), parseInt(timeArray[1]));
			// deal with timezone offset if there is one
			if (tzPos != -1) {
				var tzOffset = parseInt(tzArray[0])*60+parseInt(tzArray[1]);
				dataObj.setMinutes(dataObj.getMinutes()-(dataObj.getTimezoneOffset()+tzOffset));
			}
			// dataObj.wddxSerializationType = "datetime";
			return dataObj;
			// struct node
		} else if (nodeType == "struct") {
			var dataObj = new Object();
			for (var i = 0; i<node.childNodes.length; i++) {
				if (node.childNodes[i].nodeName.toLowerCase() == "var") {
					dataObj[deserializeAttr(node.childNodes[i].attributes["name"])] = deserializeNode(node.childNodes[i].firstChild);
				}
			}
			// dataObj.wddxSerializationType = "struct";
			return dataObj;
			// recordset node
		} else if (nodeType == "recordset") {
			var dataObj = new WddxRecordset((node.attributes["fieldNames"]).split(",").reverse(), parseInt(node.attributes["rowCount"]));
			for (var i = (node.childNodes.length-1); i>=0; i--) {
				if (node.childNodes[i].nodeName.toLowerCase() == "field") {
					var attr = deserializeAttr(node.childNodes[i].attributes["name"]);
					dataObj[attr].wddxSerializationType = "field";
					for (var j = (node.childNodes[i].childNodes.length-1); j>=0; j--) {
						dataObj[attr][j] = new Object();
						var tempObj = deserializeNode(node.childNodes[i].childNodes[j]);
						dataObj.setField(j, attr, tempObj);
					}
				}
			}
			//	dataObj.wddxSerializationType = "recordset";
			return dataObj;
		}
	}
	function deserializeAttr(attr) {
		return attr;

		var max = attr.length;
		var i = 0;
		var char;
		var output = "";
		while (i<max) {
			char = attr.substring(i+1, 1);
			if (char == "&") {
				var buff = char;
				do {
					char = attr.substring(i, i+1);
					buff += char;
					++i;
				} while (char != ";");
				output += atRev[buff];
			} else {
				output += char;
			}
			++i;
		}
		return output;

		}
	function deserializeString(str) {
		return str;

		var max = str.length;
		var i = 0;
		var char;
		var output = "";
		while (i<max) {
			char = str.substring(i+1, 1);
			if (char == "&") {
				var buff = char;
				do {
					++i;
					char = str.substring(i, i+1);
					buff += char;
				} while (char != ";");
				output += etRev[buff];
			} else {
				output += char;
			}
			++i;
		}
		return output;
	}
}

