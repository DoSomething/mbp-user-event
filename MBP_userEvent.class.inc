<?php
/**
 * MBC_UserEvent class - functionality related to the Message Broker
 * producer mbp-user-event.
 */
class MBC_UserEvent
{
  
  /**
   * Message Broker object that details the connection to RabbitMQ.
   *
   * @var object
   */
  private $messageBroker;

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
    $this->messageBroker = new MessageBroker($credentials, $config);
  }
  
  /* 
   * Produce entries in the MB_USER_EVENT_BIRTHDAY_QUEUE
   */
  public function produceTodaysBirthdays() {

    $todayDate = date('n-j');
    $userApiUrl = getenv('DS_USER_API_HOST') . ':' . getenv('DS_USER_API_PORT') . '/users?birthdate=' . $todayDate;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $userApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $jsonResult = curl_exec($ch);
    curl_close($ch);

    $results = json_decode($jsonResult);

    foreach($results as $result) {
      if (!isset($result->first_name) ||  $result->first_name == '') {
        $result->first_name = 'Doer';
      }
      $payload = array(
        'email' => $result->email,
        'merge_vars' => array(
          'FNAME' => $result->first_name,
        )
      );
      $payload = serialize($payload);
      $this->messageBroker->publishMessage($payload);
    }

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
