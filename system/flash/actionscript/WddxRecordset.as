/*
WddxRecordset is part of
WDDX Serializer/Deserializer for Flash MX 2004 v1.0
-------------------------------------------------- 
	Translated to ActionScript 2.0  by
		Jonathan Buhacoff (jonathan@openparty.net)
		
	See WDDX class for more info.
*/

//-------------------------------------------------------------------------------------------------
//  WddxRecordset object
//-------------------------------------------------------------------------------------------------
//  WddxRecordset([flagPreserveFieldCase]) creates an empty recordset.
//  WddxRecordset(columns [, flagPreserveFieldCase]) creates a recordset 
//  with a given set of columns provided as an array of strings.
//  WddxRecordset(columns, rows [, flagPreserveFieldCase]) creates a 
//  recordset with these columns and some number of rows.
//  In all cases, flagPreserveFieldCase determines whether the exact case
//  of field names is preserved. If omitted, the default value is false
//  which means that all field names will be lowercased.
class WddxRecordset {
	//  Add default properties
	var preserveFieldCase:Boolean = null;
	/*
		//  Add extensions
		if (typeof (wddxRecordsetExtensions) == "object") {
			for (prop in wddxRecordsetExtensions) {
	
				//  Hook-up method to WddxRecordset object
				this[prop] = wddxRecordsetExtensions[prop];
			}
		}
	*/
	function WddxRecordset() {
		preserveFieldCase = true;
		var val;
		//  Perfom any needed initialization
		if (arguments.length>0) {
			if (typeof (val=arguments[0].valueOf()) == "boolean") {
				//  Case preservation flag is provided as 1st argument
				preserveFieldCase = arguments[0];
			} else {
				//  First argument is the array of column names
				var cols = arguments[0];
				//  Second argument could be the length or the preserve case flag
				var nLen = 0;
				if (arguments.length>1) {
					if (typeof (val=arguments[1].valueOf()) == "boolean") {
						//  Case preservation flag is provided as 2nd argument
						preserveFieldCase = arguments[1];
					} else {
						//  Explicitly specified recordset length
						nLen = arguments[1];
						if (arguments.length>2) {
							//  Case preservation flag is provided as 3rd argument
							preserveFieldCase = arguments[2];
						}
					}
				}
				for (var i = 0; i<cols.length; ++i) {
					var colValue = new Array(nLen);
					for (var j = 0; j<nLen; ++j) {
						colValue[j] = null;
					}
					this[preserveFieldCase ? cols[i] : cols[i].toLowerCase()] = colValue;
				}
			}
		}
	}
	//  duplicate() returns a new copy of the current recordset
	function duplicate() {
		var copy = new WddxRecordset();
		var i:String;
		for (i in this) {
			if (i.toUpperCase() == "PRESERVEFIELDCASE") {
				copy[i] = this[i];
			} else {
				if (this[i].isColumn()) {
					copy.addColumn(i);
					for (var j in this[i]) {
						copy.setField(j, i, getField(j, i));
					}
				}
			}
		}
		return (copy);
	}
	//  isColumn(name) returns true/false based on whether this is a column name
	function isColumn(name) {
		return (typeof (this[name]) == "object" && name.indexOf("_private_") == -1);
	}
	//  getRowCount() returns the number of rows in the recordset
	function getRowCount() {
		var nRowCount = 0;
		for (var col in this) {
			if (isColumn(col)) {
				nRowCount = this[col].length;
				break;
			}
		}
		return nRowCount;
	}
	//  addColumn(name) adds a column with that name and length == getRowCount()
	function addColumn(name) {
		var nLen = getRowCount();
		var colValue = new Array(nLen);
		for (var i = 0; i<nLen; ++i) {
			colValue[i] = null;
		}
		this[preserveFieldCase ? name : name.toLowerCase()] = colValue;
	}
	//  addRows() adds n rows to all columns of the recordset
	function addRows(n) {
		for (var col in this) {
			if (isColumn(col)) {
				var nLen = this[col].length;
				for (var i = nLen; i<nLen+n; ++i) {
					this[col][i] = "";
				}
			}
		}
	}
	//  getField() returns the element in a given (row, col) position
	function getField(row, col) {
		return this[preserveFieldCase ? col : col.toLowerCase()][row];
	}
	//  setField() sets the element in a given (row, col) position to value
	function setField(row, col, value) {
		this[preserveFieldCase ? col : col.toLowerCase()][row] = value;
	}
	//  wddxSerialize() serializes a recordset
	//  returns true/false
	function wddxSerialize(serializer, node) {
		var colNamesList = "";
		var colNames = new Array();
		var i = 0;
		for (var col in this) {
			if (isColumn(col)) {
				colNames[i++] = col;
				if (colNamesList.length>0) {
					colNamesList += ",";
				}
				colNamesList += col;
			}
		}
		var nRows = getRowCount();
		node.appendChild((new XML()).createElement("recordset"));
		node.lastChild.attributes["rowCount"] = nRows;
		node.lastChild.attributes["fieldNames"] = colNamesList;
		var bSuccess = true;
		for (i=0; bSuccess && i<colNames.length; i++) {
			var name = colNames[i];
			node.lastChild.appendChild((new XML()).createElement("field"));
			node.lastChild.lastChild.attributes["name"] = name;
			for (var row = 0; bSuccess && row<nRows; row++) {
				bSuccess = serializer.serializeValue(this[name][row], node.lastChild.lastChild);
			}
		}
		return bSuccess;
	}
}

