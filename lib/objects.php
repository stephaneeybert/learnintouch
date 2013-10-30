<?php

class LibObjects {

  // Check if a class is a subclass of another clas
  static function isSubclass($child, $parent) {
    if(is_string($child)) {
      do {
        $child = get_parent_class($child);
        if (!$child) {
          return(false);
          }
        }
      while ($child==$parent);
      return(true);
      } else {
      return(is_subclass_of($child, $parent));
      }
    }

  }

?>
