<?php
/**
 * mbc-registration-email.php
 *
 * Collect new user registrations from the userRegistrationQueue as a batch job.
 * A collection of entries will result in a batch submission to create
 * entries/accounts in MailChimp. The MailChimp accounts are used to build
 * segments for mass mailouts.
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
  'directUserEvents' => array(
    'name' => getenv("MB_USER_EVENT_EXCHANGE"),
    'type' => getenv("MB_USER_EVENT_EXCHANGE_TYPE"),
    'passive' => getenv("MB_USER_EVENT_EXCHANGE_PASSIVE"),
    'durable' => getenv("MB_USER_EVENT_EXCHANGE_DURABLE"),
    'auto_delete' => getenv("MB_USER_EVENT_EXCHANGE_AUTO_DELETE"),
  ),
  'queue' => array(
    0 => array(
        'name' => getenv("MB_USER_EVENT_BIRTHDAY_QUEUE"),
        'passive' => getenv("MB_USER_EVENT_BIRTHDAY_QUEUE_PASSIVE"),
        'durable' => getenv("MB_USER_EVENT_BIRTHDAY_QUEUE_DURABLE"),
        'exclusive' => getenv("MB_USER_EVENT_BIRTHDAY_QUEUE_EXCLUSIVE"),
        'auto_delete' => getenv("MB_USER_EVENT_BIRTHDAY_QUEUE_AUTO_DELETE"),
        'bindingKey' => getenv("MB_USER_EVENT_BIRTHDAY_QUEUE_BINDING_KEY"),
    ),
    1 => array(
        'name' => getenv("MB_USER_EVENT_13BIRTHDAY_QUEUE"),
        'passive' => getenv("MB_USER_EVENT_13BIRTHDAY_QUEUE_PASSIVE"),
        'durable' => getenv("MB_USER_EVENT_13BIRTHDAY_QUEUE_DURABLE"),
        'exclusive' => getenv("MB_USER_EVENT_13BIRTHDAY_QUEUE_EXCLUSIVE"),
        'auto_delete' => getenv("MB_USER_EVENT_13BIRTHDAY_QUEUE_AUTO_DELETE"),
        'bindingKey' => getenv("MB_USER_EVENT_13BIRTHDAY_QUEUE_BINDING_KEY"),
    ),
    2 => array(
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

// Kick off
$mbpUserEvent = new MBC_UserEvent($credentials, $config);

// Gather users (email) of todays birthdays
$status .= $mbpUserEvent->produceTodaysBirthdays();

// Gather users (email) of todays 13th birthdays
$status .= $mbpUserEvent->produceTodays13thBirthdays();

// Gather users (email) of todays registration anniversaries
$status .= $mbpUserEvent->produceTodaysAnniversaries();

print $status;