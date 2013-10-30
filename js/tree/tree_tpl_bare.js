/*
This is a TIGRA javascript from SoftComplex.
Feel free to use your custom icons for the tree. Make sure they are all of the same size.
User icons collections are welcome, we'll publish them giving all regards.
*/

var tree_tpl = {
	// Name of the frame links will be opened in
	// Other possible values are: _blank, _parent, _search, _self and _top
	'target'  : '_self',	

	'icon_e'  : gJsTreeUrl + '/icons/', // empty image
	'icon_l'  : gJsTreeUrl + '/icons/line.gif',  // vertical line

  'icon_32' : gJsTreeUrl + '/icons/',   // root leaf icon normal
  'icon_36' : gJsTreeUrl + '/icons/',   // root leaf icon selected

	'icon_48' : gJsTreeUrl + '/icons/',   // root icon normal
	'icon_52' : gJsTreeUrl + '/icons/',   // root icon selected
	'icon_56' : gJsTreeUrl + '/icons/',   // root icon opened
	'icon_60' : gJsTreeUrl + '/icons/',   // root icon selected
	
	'icon_16' : gJsTreeUrl + '/icons/', // node icon normal
	'icon_20' : gJsTreeUrl + '/icons/', // node icon selected
	'icon_24' : gJsTreeUrl + '/icons/', // node icon opened
	'icon_28' : gJsTreeUrl + '/icons/', // node icon selected opened

	'icon_0'  : gJsTreeUrl + '/icons/', // leaf icon normal
	'icon_4'  : gJsTreeUrl + '/icons/page.gif', // leaf icon selected
	
	'icon_2'  : gJsTreeUrl + '/icons/joinbottom.gif', // junction for leaf
	'icon_3'  : gJsTreeUrl + '/icons/join.gif',       // junction for last leaf
	'icon_18' : gJsTreeUrl + '/icons/plusbottom.gif', // junction for closed node
	'icon_19' : gJsTreeUrl + '/icons/plus.gif',       // junction for last closed node
	'icon_26' : gJsTreeUrl + '/icons/minusbottom.gif',// junction for opened node
	'icon_27' : gJsTreeUrl + '/icons/minus.gif'       // junction for last opended node
};

