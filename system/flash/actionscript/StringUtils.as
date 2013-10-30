class StringUtils {

  static function trace(str) {

    // Create a formatting object
    var textFormat = new TextFormat();
    textFormat.font = "Arial";
    textFormat.size = 12;
    textFormat.color = 0xCCCCCC;

    _root.createTextField("tf", 0, 0, 0, 800, 600);

    _root.tf.setTextFormat(textFormat);

    _root.tf.text = str;
    }

  function trim(str) {
    var ch;

    while (true) {
      ch = str.charCodeAt(0);
      if (ch == 32 || ch == 9 || ch == 10 || ch == 13) {
        str = str.slice(1);
        } else {
        break;
        }
      }

    while(true) {
      ch = str.charCodeAt(str.length - 1);
      if (ch == 32 || ch == 9 || ch == 10 || ch == 13) {
        str = str.slice(0, -1);
        } else {
        break;
        }
      }

    if (!str) {
      str = '';
      }

    return(str);
    }

  }
