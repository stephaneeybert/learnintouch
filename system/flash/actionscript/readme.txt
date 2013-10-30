A navigation link, a navigation bar or a navigation menu can be rendered in a Flash animation.

In order for the Flash animation to retrieve the data from the system and avoid hard coding the Flash animation
data, some parameters and a wddx file are used.

A copy of the appropriate example file can be done with its actionscript being used in a Flash animation.

The Flash file is passed two parameters:
  wddxname
  languageCode

Like in:

<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0' width='300' height='300'> <param name='movie' value='http://www.thalasoft.com/account/data/flash/file/mp3.swf?languageCode=en&wddxname=mp3.navbar.63.wddx' /> <param name='quality' value='high' /> <param name='bgcolor' value='' /> <param name='wmode' value='transparent' /> <embed src='http://www.thalasoft.com/account/data/flash/file/mp3.swf?languageCode=en&wddxname=mp3.navbar.63.wddx' quality='high' wmode='transparent' bgcolor='' width='300' height='300' type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash'> </embed> </object>

The parameters are passed in the .swf file url, after the filename.

The series of parameters starts after the ? and each parameter is separated from the preceding one by a &

In the above example the parameters are passed like this:

?languageCode=en&wddxname=mp3.navbar.63.wddx

The actionscript must retrieve these two parameters.

Using the wddx parameter the actionscript opens the wddx file and reads its content.

The library WDDX.as offers the wddx functions to parse the wddx file content into some actionscript object.

It is then possible to loop through the actionscript object.


The wddx library is composed of the two files WDDX.as and WddxRecordset.as

To compile an actionscript file use the command:

mtasc.sh filename (do not type the .as suffix)


Remarks:

1. An object _global used in wddx_mx.as is available from AS 1 V6 to AS2.0 also.
So the AS2.0 library is preferred because using AS 1 is deprecated from V6.
Therefore the old wdxx_mx.as library is not to be used.

2. The old wdxx_mx.as library contains a fatal error in the function Wddx.prototype.deserializeString.
There is infinite cycle in special symbols processing.
So it must not be used unless the error is fixed first.
Therefore the WDDX.as is to be used.

3. Using wddx overrides the class definition of the wddx library.
Therefore do not use wddx as a variable name.



