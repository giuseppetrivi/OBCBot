<?php

namespace OBCBot\view;

use Error;
use OBCBot\exceptions\ViewException;
use ReflectionClass;


/**
 * Base class to get automatically the keyboards
 * declared into Keyboards and InlineKeyboards classes
 */
class ViewWrapper {

  private function __construct() {}


  /**
   * Method that creates automatically getters and setters for the 
   * attributes of the sub-classes 
   */
  public static function __callStatic($method_name, $method_arguments) {
    /* Separates the mode (get) from the attribute to affect */
    $mode = substr($method_name, 0, 3);
    if ($mode!="get") {
      throw new ViewException("The method called ($method_name) is not a getter");
    }

    $attribute = substr($method_name, 3);

    /* Search for a valid attribute with the one named in the method call */
    $_ReflClass = new ReflectionClass(get_called_class());
    $array_of_object_vars_name = array_keys($_ReflClass->getConstants());
    $formatted_attribute_name = "";
    if ( array_search(ViewWrapper::convertMethodNameInConstantName($attribute), $array_of_object_vars_name) !== false ) {
      $formatted_attribute_name = ViewWrapper::convertMethodNameInConstantName($attribute);
    }
    else {
      throw new ViewException("Attribute name ($attribute) doesn't match any of the attribute in this class");
    }

    /* Execute the command on attribute based on the mode in the method call */
    if (strcmp($mode, "get")==0) {
      $_RC = new ReflectionClass(get_called_class());
      $classname = $_RC->getShortName();
      
      if ($classname == "Keyboards") {
        return get_called_class()::createKeyboard(constant(get_called_class()."::".$formatted_attribute_name));
      }
      else if ($classname == "InlineKeyboards") {
        return get_called_class()::createInlineKeyboard(constant(get_called_class()."::".$formatted_attribute_name));
      }
      else {
        throw new ViewException("The class called is neither Keyboards nor InlineKeyboards");
      }
      
    }
  }

  
  /**
   * Converts the method name into a constant name
   * Example: getMethodName --> METHOD_NAME
   * 
   * @param string $attribute method_name
   * @return string Return the converted method name
   */
  private static function convertMethodNameInConstantName($attribute) {
    $attribute = lcfirst($attribute);
    $formatted_attribute_name = "";
    $attribute_length = strlen($attribute);
    for ($i=0; $i<$attribute_length; $i++) {
      $char = $attribute[$i];
      if (ctype_upper($char)) {
        $formatted_attribute_name .= '_'.strtoupper($char);
      }
      else {
        $formatted_attribute_name .= strtoupper($char);
      }
    }
    return $formatted_attribute_name;
  }


}