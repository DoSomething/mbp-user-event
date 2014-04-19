<?php
/**
 * MBC_UserEvent class - functionality related to the Message Broker
 * producer mbp-user-event.
 */
class MBC_UserRegistration
{
  
    /**
   * Message Broker object that details the connection to RabbitMQ.
   *
   * @var object
   */
  private $MessageBroker;

  /**
   * Details of the channel connection in use by RabbitMQ.
   *
   * @var object
   */
  private $channel;

  /**
   * Collection of configuration settings.
   *
   * @var array
   */
  private $config;

  /**
   * Collection of secret connection settings.
   *
   * @var array
   */
  private $credentials;

  /**
   * Constructor for MBC_UserEvent
   *
   * @param array $credentials
   *   Secret settings from mb-secure-config.inc
   *
   * @param array $config
   *   Configuration settings from mb-config.inc
   */
  public function __construct($credentials, $config) {

    $this->config = $config;
    $this->credentials = $credentials;

    // Setup RabbitMQ connection
    $this->MessageBroker = new MessageBroker($credentials, $config);
    
  }
  
  /* 
   * Produce entries in the MB_USER_EVENT_BIRTHDAY_QUEUE
   */
  public function produceTodaysBirthdays() {
    
    $todayStamp = strtotime('00:00:01');
    $userApiUrl = getenv('DS_USER_API_HOST') . ':' . getenv('DS_USER_API_PORT') . '/users?birthdate=' . $todayStamp;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $userApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec($ch);
    curl_close($ch);
    
    // Decode result into array
    
    // Set routing key for exchange to direct message to
    // MB_USER_EVENT_BIRTHDAY_ROUTING_KEY
    
    // Loop through returned items
    
    // Create queue entry
    $this->MessageBroker->publishMessage($payload);
    
  }
  
  /*
   * Produce entries in the MB_USER_EVENT_13BIRTHDAY_QUEUE
   */
  public function produceTodays13thBirthdays() {
    
  }
  
  /*
   * Produce entries in the MB_USER_EVENT_ANNIVERSARY_QUEUE
   */
  public function produceTodaysAnniversaries() {
    
  }
  
}
