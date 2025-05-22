<?php

namespace CustomBotName\entities;

use Error;
use CustomBotName\exceptions\BaseEntityException;


/**
 * This is the base class for the entities which contains the general
 * methods that every sub-class needs
 */
class BaseEntity {

  private function __construct() {}

  /**
   * Method that creates automatically getters and setters for the attributes
   * of the sub-classes
   */
  public function __call($method_name, $method_arguments) {
    /* Separates the mode (get or set) from the attribute to affect */
    $mode = substr($method_name, 0, 3);
    if ($mode!="get" && $mode!="set") {
      throw new BaseEntityException("The method called ($method_name) is not a getter or a setter");
    }
    $attribute = substr($method_name, 3);

    /**
     * Search for a valid attribute with the one named in the method call
     * The order of search is: attribute (this_format), constant (THIS_FORMAT), class instance (_ThisFormat)
     */
    $array_of_object_vars_name = array_keys(get_class_vars(get_class($this)));
    $formatted_attribute_name = "";
    if ( array_search($this->convertMethodNameInAttributeName($attribute), $array_of_object_vars_name) !== false ) {
      $formatted_attribute_name = $this->convertMethodNameInAttributeName($attribute);
    }
    else if ( array_search($this->convertMethodNameInConstantName($attribute), $array_of_object_vars_name) !== false ) {
      $formatted_attribute_name = $this->convertMethodNameInConstantName($attribute);
    }
    else if ( array_search($this->convertMethodNameInClassInstanceName($attribute), $array_of_object_vars_name) !== false ) {
      $formatted_attribute_name = $this->convertMethodNameInClassInstanceName($attribute);
    }
    else {
      throw new BaseEntityException("Attribute name ($attribute) doesn't match any of the attributes in this class");
    }

    /* Execute the command on attribute based on the mode in the method call */
    if (strcmp($mode, "get")==0) {
      return $this->{$formatted_attribute_name};
    }
    else if (strcmp($mode, "set")==0) {
      if (array_key_exists(0, $method_arguments)) {
        $this->{$formatted_attribute_name} = $method_arguments[0];
      }
    }
  }

  
  /**
   * Converts the method name into an attribute name
   * Example: getMethodName --> method_name
   * 
   * @param string $attribute method_name
   * @return string Return the converted method name
   */
  private function convertMethodNameInAttributeName($attribute) {
    $attribute = lcfirst($attribute);
    $formatted_attribute_name = "";
    $attribute_length = strlen($attribute);
    for ($i=0; $i<$attribute_length; $i++) {
      $char = $attribute[$i];
      if (ctype_upper($char)) {
        $formatted_attribute_name .= '_'.strtolower($char);
      }
      else {
        $formatted_attribute_name .= strtolower($char);
      }
    }
    return $formatted_attribute_name;
  }

  /**
   * Converts the method name into a constant name
   * Example: getMethodName --> METHOD_NAME
   * 
   * @param string $attribute method_name
   * @return string Return the converted method name
   */
  private function convertMethodNameInConstantName($attribute) {
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

  /**
   * Converts the method name into a class instance name
   * Example: getMethodName --> _MethodName
   * 
   * @param string $attribute method_name
   * @return string Return the converted method name
   */
  private function convertMethodNameInClassInstanceName($attribute) {
    $formatted_attribute_name = "";
    $onlyfirst = false;
    $attribute_length = strlen($attribute);
    for ($i=0; $i<$attribute_length; $i++) {
      $char = $attribute[$i];
      if (ctype_upper($char) && !$onlyfirst) {
        $formatted_attribute_name .= '_';
        $onlyfirst = true;
      }
      $formatted_attribute_name .= $char;
    }
    return $formatted_attribute_name;
  }

}