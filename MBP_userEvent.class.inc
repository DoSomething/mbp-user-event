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

    echo '------- mbp-user-event->produceTodaysBirthdays() START: ' . date('D M j G:i:s T Y') . ' -------', "\n";

    $todayDate = date('n-j');
    $userApiUrl = getenv('DS_USER_API_HOST') . ':' . getenv('DS_USER_API_PORT') . '/users?birthdate=' . $todayDate;

    $this->populateQueue($userApiUrl);
    echo '------- mbp-user-event->produceTodaysBirthdays() END: ' . date('D M j G:i:s T Y') . ' -------', "\n";
  }

  /*
   * Produce entries in the MB_USER_EVENT_13BIRTHDAY_QUEUE
   *
   * @todo: When the template supports more than 1st anniversaries add
   * additional merge tag with calculated wording.
   */
  public function produceTodays13thBirthdays() {

    echo '------- mbp-user-event->produceTodays13thBirthdays() START: ' . date('D M j G:i:s T Y') . ' -------', "\n";

    $todayDate = date('n-j', time()) . '-' . (date('Y', time()) - 13);
    $userApiUrl = getenv('DS_USER_API_HOST') . ':' . getenv('DS_USER_API_PORT') . '/users?birthdate=' . $todayDate;

    $this->populateQueue($userApiUrl);
    echo '------- mbp-user-event->produceTodays13thBirthdays() END: ' . date('D M j G:i:s T Y') . ' -------', "\n";
  }

  /*
   * Produce entries in the MB_USER_EVENT_ANNIVERSARY_QUEUE
   */
  public function produceTodaysAnniversaries() {

    echo '------- mbp-user-event->produceTodaysAnniversaries() START: ' . date('D M j G:i:s T Y') . ' -------', "\n";

    // @todo: Need a limit from the User API of users who are having their one year anniversary only, no other years
    $todayDate = date('n-j', time()) . '-' . (date('Y', time()) - 1);
    $userApiUrl = getenv('DS_USER_API_HOST') . ':' . getenv('DS_USER_API_PORT') . '/users?drupal_register_date=' . $todayDate;

    $this->populateQueue($userApiUrl);
    echo '------- mbp-user-event->produceTodaysAnniversaries() END: ' . date('D M j G:i:s T Y') . ' -------', "\n";
  }

  /*
   * Request data from the User API and process the results to create entries in
   * queue defined in the MessageBroker connection details.
   *
   * @param string $userApiUrl
   *   The URL with parameters to send to the User API
   */
  private function populateQueue($userApiUrl) {

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
        'year' => $result->year,
        'drupal_uid' => $result->drupal_uid,
        'merge_vars' => array(
          'FNAME' => ucwords(strtolower($result->first_name)),
        )
      );
      $payload = serialize($payload);
      $this->messageBroker->publishMessage($payload);
    }

  }

}
