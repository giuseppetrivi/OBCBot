<?php

namespace OBCBot\entities;

use OBCBot\exceptions\RuleException;
use OBCBot\config\ConfigurationHandler;
use OBCBot\entities\authorization_rules\Rule;
use OBCBot\entities\authorization_rules\CheckIfBotIsOpenToAccessRule;


/**
 * Class that executes preliminary checks on the User
 */
class BotAuthorization extends BaseEntity {

  public array $rules = [];

  protected ?ConfigurationHandler $_Config = null;


  public function __construct(ConfigurationHandler $_Config) {
    $this->setConfig($_Config);

    $this->rulesToAdd();
  }


  /**
   * Adds the specified rule into $rules array 
   * 
   * @param Rule $_Rule Specified rule object
   */
  private function addRule(Rule $_Rule) {
    array_push($this->rules, $_Rule);
  }

  /**
   * Choose which specific rule objects add into $rules array
   * [This rules are basic examples]
   */
  private function rulesToAdd() {
    $this->addRule(new CheckIfBotIsOpenToAccessRule($this->getConfig()));
  }


  /**
   * Checks every rule into $rules array
   * 
   * @return void|RuleException Exception when a rule is not fulfilled by the user, with the specific rule error message
   */
  public function verifyAuthorization() {
    $rules = $this->getRules();
    foreach ($rules as $_SpecificRuleInstance) {
      if (!$_SpecificRuleInstance->rule()) {
        throw new RuleException($_SpecificRuleInstance->errorMessage());
        break;
      }
    }
  }

}

?>
