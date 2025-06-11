<?php

namespace OBCBot\control;

use OBCBot\exceptions\state_exceptions\StateFunctionException;
use OBCBot\exceptions\state_exceptions\StateInputException;

/**
 * Base class to handle states.
 * 
 * The logic is this:
 * Every state has its pre-conditions, based on the validity of inputs.
 * Inputs can be verified statically or dinamically.
 * 
 * Then there is the core code of the state, that depends on the input
 * 
 * Finally, there is the only post-condition that is the change of the state
 * in the database
 */
abstract class AbstractState {
  protected $_Bot;
  protected $_User;

  /**
   * Array of all valid inputs possibile for the current specific
   * state
   * [SHOULD BE OVERRIDDEN]
   */
  protected array $valid_static_inputs = [];

  /**
   * Name of the function to be called by the mainCode method
   */
  protected string|null $function_to_call = null;

  /**
   * Next state to be setted in the database at the end of all
   * the core code of the current state. If null it will delete
   * the state record in the database. This means that the next
   * state will be the start menu
   */
  protected string|null $state_name = null;
  /**
   * Data to be setted fo the next state. If null, it will be empty
   */
  protected string|null $state_data = null;

  /**
   * @param TelegramBotSdkCustomInterface $_Bot
   * @param User $_User
   */
  public function __construct($_Bot, $_User) {
    $this->_Bot = $_Bot;
    $this->_User = $_User;
  }
  

  /**
   * Validate automatically the inputs basing on the valid_inputs list.
   * This function is protected because it can be override to make the
   * stantard static validation more custom.
   * This function uses TelegramBotSdkCustomInterface object.
   * 
   * @return bool
   */
  protected function validateStaticInputs() {
    $input_text = $this->_Bot->getInputFromChat()->getText();
    if (count($this->valid_static_inputs)!=0 && array_key_exists($input_text, $this->valid_static_inputs)) {
      $this->function_to_call = $this->valid_static_inputs[$input_text];
      return true;
    }
    return false;
  }

  /**
   * Validate some dynamic rules, not present in the valid_static_inputs list.
   * The default return is false, but can be overrided and implemented
   * pesonally
   * [CAN OVERRIDE, for personal rules]
   * 
   * @return bool
   */
  protected function validateDynamicInputs() {
    return false;
  }


  /**
   * From the name of the state, deletes the last one to get
   * the previous state name
   * 
   * @return array
   */
  protected function getPreviousState() {
    $classname_complete = get_class($this);

    $array_pf_states = explode("\\", $classname_complete);
    array_pop($array_pf_states);
    return implode("\\", $array_pf_states);
  }

  /**
   * Append the parameter "next_state" to the actual state
   * 
   * @param string $next_state
   * @return string
   */
  protected function appendNextState($next_state) {
    return get_class($this) . "\\" . $next_state;
  }

  /**
   * Keeps the actual state
   */
  protected function keepThisState() {
    $this->state_name = get_class($this);
  }

  /**
   * Set the next_state attribute
   */
  protected function setNextState($state_name=null, $state_data=null) {
    $this->state_name = $state_name;
    $this->state_data = $state_data;
  }

  /**
   * Change the state into the database to set it to the next
   * state (eventually also with data)
   */
  private function changeState() {
    $this->_User->getStateHandler()->updateState($this->state_name, $this->state_data);
  }



  /**
   * Verifies the validity of the input of the state, to satify its
   * pre-conditions
   * This has also to set the specific procedure to be executed in the
   * mainCode block
   * 
   * @return true|Exception
   */
  protected function preConditionInput() {
    $check_static_inputs = $this->validateStaticInputs();
    if ($check_static_inputs==false) {
      $check_dynamic_inputs = $this->validateDynamicInputs();
      if ($check_dynamic_inputs==false) {
        throw new StateInputException("Input doesn't match any of the static and dynamic inputs of this state");
      }
    }

    if ($this->function_to_call==null) {
      throw new StateFunctionException("Function to call is null");
    }

    return true;
  }

  /**
   * Contains the core code to execute the actions of the state.
   * There is the standard call to the function to execute setted
   * by the pre-condition check
   */
  protected function mainCode() {
    call_user_func(array($this, $this->function_to_call));
  }

  /**
   * Empty procedure to simply not do anything
   */
  protected function emptyProcedure() {
    $this->keepThisState();
  }

  /**
   * Verify all the post-conditions, starting from the
   * change of the state
   */
  protected function postConditionState() {
    $this->changeState();
  }

  /**
   * Function visible from outside the boundaries of State class
   * that executes the code with pre and post-conditions
   */
  public function codeToRun() {
    $this->preConditionInput();
    $this->mainCode();
    $this->postConditionState();
  }


  /**
   * Simple test function
   */
  public function testExecution() {
    return "I'm into the class " . get_class($this);
  }


}