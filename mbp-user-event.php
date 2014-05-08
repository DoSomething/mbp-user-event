<?php
/**
 * mbp-user-event.php
 *
 * Collect users for different event types based on a specific date (typically
 * today). Users found are added to the event queues to be consumed by
 * mbc-user-event.
 */

// Load up the Composer autoload magic
require_once __DIR__ . '/vendor/autoload.php';

// Load configuration settings common to the Message Broker system
// symlinks in the project directory point to the actual location of the files
require __DIR__ . '/mb-secure-config.inc';
require __DIR__ . '/mb-config.inc';

require __DIR__ . '/MBP_userEvent.class.inc';

// Settings
$credentials = array(
  'host' =>  getenv("RABBITMQ_HOST"),
  'port' => getenv("RABBITMQ_PORT"),
  'username' => getenv("RABBITMQ_USERNAME"),
  'password' => getenv("RABBITMQ_PASSWORD"),
  'vhost' => getenv("RABBITMQ_VHOST"),
);

$config = array(
  'exchange' => array(
    'name' => getenv("MB_USER_EVENT_EXCHANGE"),
    'type' => getenv("MB_USER_EVENT_EXCHANGE_TYPE"),
    'passive' => getenv("MB_USER_EVENT_EXCHANGE_PASSIVE"),
    'durable' => getenv("MB_USER_EVENT_EXCHANGE_DURABLE"),
    'auto_delete' => getenv("MB_USER_EVENT_EXCHANGE_AUTO_DELETE"),
  ),
  'queue' => array(
    array(
      'name' => getenv("MB_USER_EVENT_BIRTHDAY_QUEUE"),
      'passive' => getenv("MB_USER_EVENT_BIRTHDAY_QUEUE_PASSIVE"),
      'durable' => getenv("MB_USER_EVENT_BIRTHDAY_QUEUE_DURABLE"),
      'exclusive' => getenv("MB_USER_EVENT_BIRTHDAY_QUEUE_EXCLUSIVE"),
      'auto_delete' => getenv("MB_USER_EVENT_BIRTHDAY_QUEUE_AUTO_DELETE"),
      'bindingKey' => getenv("MB_USER_EVENT_BIRTHDAY_QUEUE_BINDING_KEY"),
    ),
    array(
      'name' => getenv("MB_USER_EVENT_13BIRTHDAY_QUEUE"),
      'passive' => getenv("MB_USER_EVENT_13BIRTHDAY_QUEUE_PASSIVE"),
      'durable' => getenv("MB_USER_EVENT_13BIRTHDAY_QUEUE_DURABLE"),
      'exclusive' => getenv("MB_USER_EVENT_13BIRTHDAY_QUEUE_EXCLUSIVE"),
      'auto_delete' => getenv("MB_USER_EVENT_13BIRTHDAY_QUEUE_AUTO_DELETE"),
      'bindingKey' => getenv("MB_USER_EVENT_13BIRTHDAY_QUEUE_BINDING_KEY"),
    ),
    array(
      'name' => getenv("MB_USER_EVENT_ANNIVERSARY_QUEUE"),
      'passive' => getenv("MB_USER_EVENT_ANNIVERSARY_QUEUE_PASSIVE"),
      'durable' => getenv("MB_USER_EVENT_ANNIVERSARY_QUEUE_DURABLE"),
      'exclusive' => getenv("MB_USER_EVENT_ANNIVERSARYP_QUEUE_EXCLUSIVE"),
      'auto_delete' => getenv("MB_USER_EVENT_ANNIVERSARY_QUEUE_AUTO_DELETE"),
      'bindingKey' => getenv("MB_USER_EVENT_ANNIVERSARY_QUEUE_BINDING_KEY"),
    ),
  ),
);

$status = NULL;

echo '------- mbp-user-event START: ' . date('D M j G:i:s T Y') . ' -------', "\n";

// Kick off
// Gather users (email) of todays birthdays
$config['routingKey'] = getenv("MB_USER_EVENT_BIRTHDAY_ROUTING_KEY");
$mbpUserEvent = new MBC_UserEvent($credentials, $config);
$mbpUserEvent->produceTodaysBirthdays();

// Gather users (email) of todays 13th birthdays
$config['routingKey'] = getenv("MB_USER_EVENT_13BIRTHDAY_ROUTING_KEY");
$mbpUserEvent = new MBC_UserEvent($credentials, $config);
// $mbpUserEvent->produceTodays13thBirthdays();

// Gather users (email) of todays one year (see todo for support of more than
// 1st anniversary support) registration anniversaries
$config['routingKey'] = getenv("MB_USER_EVENT_ANNIVERSARY_ROUTING_KEY");
$mbpUserEvent = new MBC_UserEvent($credentials, $config);
// $mbpUserEvent->produceTodaysAnniversaries();

echo '------- mbp-user-event END: ' . date('D M j G:i:s T Y') . ' -------', "\n";
