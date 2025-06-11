<?php

namespace OBCBot\entities\telegrambot_sdk_interface;

use OBCBot\exceptions\TelegramBotInterfaceException;
use Psr\Http\Message\RequestInterface;
use Telegram\Bot\Api;
use Telegram\Bot\HttpClients\HttpClientInterface;
use Telegram\Bot\Objects\Update;
use Telegram\Bot\TelegramResponse;


/**
 * This is the custom class to interface system and [telegram-bot-sdk by irazasyed](https://github.com/telegram-bot-sdk/telegram-bot-sdk).
 * You can change it based on the bot library you want to use or, if you want to use
 * this same library, you can change the logic of this interface class.
 * 
 * Class to have some default behaviors in Telegram Bot API
 */
class TelegramBotSdkCustomInterface extends Api {

  private ?InputFromChat $_InputFromChat = null;


  /**
   * Constructor of class Telegram\Bot\Api (from library) with additional operations
   */
  public function __construct(?string $token = null, bool $async = false, ?HttpClientInterface $httpClientHandler = null, ?string $baseBotUrl = null) {
    parent::__construct($token, $async, $httpClientHandler, $baseBotUrl);

    $_Chat = $this->getChatWithChecks();
    $this->chat_id = $_Chat->getId();
    $this->_InputFromChat = $this->getInputFromChat();
  }


  /**
   * All parameters i want to be default, with setters and getters
   */
  private $chat_id = null;
  private $parse_mode = 'html';

  public function getChatId() {
    return $this->chat_id;
  }

  private function getParseMode() {
    return $this->parse_mode;
  }

  /**
   * Function to set custom default values for every post call of
   * Telegram Bot API
   */
  public function post(string $method, array $parameters=[], bool $file_upload=false): TelegramResponse {
    if ($this->getChatId()!=null) {
      $parameters['chat_id'] = $this->getChatId();
    }
    if (self::getParseMode()!=null) {
      $parameters['parse_mode'] = $this->getParseMode();
    }
    return parent::post($method, $parameters, $file_upload);
  }


  
  /**
   * Check the value of webhook Update instance
   */
  private function getWebhookUpdateWithChecks() {
    $_Update = parent::getWebhookUpdate();
    if ($_Update==null) {
      throw new TelegramBotInterfaceException("Update class is null");
    }
    return $_Update;
  }

  /**
   * Check the Chat instance (from Update instance)
   */
  public function getChatWithChecks() {
    $_Chat = $this->getWebhookUpdateWithChecks()->getChat();
    if ($_Chat==null) {
      throw new TelegramBotInterfaceException("Chat class is null");
    }
    return $_Chat;
  }

  /**
   * Makes all checks to get the input from chat (from message, query etc...)
   */
  public function getInputFromChat() {
    if ($this->_InputFromChat!=null) {
      return $this->_InputFromChat;
    }
    
    $_Update = $this->getWebhookUpdateWithChecks();
    $_Message = $_Update->getMessage();
    $_CallbackQuery = $_Update->getCallbackQuery();
    if ($_CallbackQuery!=null) {
      return new InputFromChat($_CallbackQuery->getData(), InputTypes::CALLBACK_QUERY);
    }
    else if ($_Message!=null) {
      return new InputFromChat($_Message->getText(), InputTypes::MESSAGE);
    }
    
    throw new TelegramBotInterfaceException("Request is neither a message nor a query");
  }

}
