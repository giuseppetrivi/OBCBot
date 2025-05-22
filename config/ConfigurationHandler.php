<?php

namespace CustomBotName\config;

use CustomBotName\exceptions\ConfigurationException;


/**
 * [Singleton](https://refactoring.guru/design-patterns/singleton) class to handle configuration properies.
 */
class ConfigurationHandler {

  private static $_ClassInstance = null;

  private string $development_mode;

  private string $config_filename = "actual_config.json";
  private ?array $config_properties = null;

  
  /**
   * @param string $development_mode Key to take (from the configuration file) the set of properties to use during the execution
   */
  private function __construct(string $development_mode="production") {
    $this->development_mode = $development_mode;
    $this->setConfigurationProperties();
  }


  /**
   * Takes the data in the configuration file and converts it into an associative array
   */  
  private function setConfigurationProperties() {
    $development_mode = $this->development_mode;
    $config_complete_path = __DIR__ . "/" . $this->config_filename;
    $file_content = file_get_contents($config_complete_path);
    if (!$file_content) {
      throw new ConfigurationException("Configuration file ($config_complete_path) doesn't exists");
    }

    $json_config = json_decode($file_content, true);
    if (!array_key_exists($development_mode, $json_config)) {
      throw new ConfigurationException("Tried to use a development mode ($development_mode) that is not into configuration file");
    }

    $this->config_properties = $json_config[$development_mode];
  }

  /**
   * Set the instance of the ConfigurationHandler class
   * 
   * @param string $development_mode Name of the configuration setting to set, specified into config file
   * @return ConfigurationHandler
   */
  public static function setInstance(string $development_mode="production") {
    if (self::$_ClassInstance==null) {
      self::$_ClassInstance = new ConfigurationHandler($development_mode);
    }
    return self::$_ClassInstance;
  }

  /**
   * Get the Singleton instance of the ConfigurationHandler class
   */
  public static function getInstance() {
    if (self::$_ClassInstance==null) {
      throw new ConfigurationException("The ConfigurationHandler instance is null");
    }
    return self::$_ClassInstance;
  }

  /**
   * Get the name of development mode
   * 
   * @return string
   */
  public function getDevelopmentMode() {
    return $this->development_mode;
  }



  /** [Example of how to] handle a json configuration file and its data. It is editable according to your specifications... */


  /* Database info */
  public function getDatabaseUsername() {
    return $this->config_properties['DATABASE_INFO']['username'];
  }
  public function getDatabasePassword() {
    return $this->config_properties['DATABASE_INFO']['password'];
  }
  public function getDatabaseName() {
    return $this->config_properties['DATABASE_INFO']['db_name'];
  }
  public function getDatabaseHost() {
    return $this->config_properties['DATABASE_INFO']['db_host'];
  }

  /* Telegram Bot API token */
  public function getTelegramBotApiToken() {
    return $this->config_properties['TELEGRAM_BOT_API_TOKEN'];
  }

  /* Open access to bot */
  public function getOpenAccessToBot() {
    return $this->config_properties['OPEN_ACCESS_TO_BOT'];
  }

}