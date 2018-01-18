<?php
require_once __DIR__ . '/vendor/autoload.php';
date_default_timezone_set('America/New_York');
 
define('APPLICATION_NAME', 'Google Calendar API PHP Quickstart');
define('CREDENTIALS_PATH', __DIR__ . '/calendar-php-quickstart.json');
define('CLIENT_SECRET_PATH', __DIR__ . '/client_secret.json');
// If modifying these scopes, delete your previously saved credentials
// at ~/.credentials/calendar-php-quickstart.json
define('SCOPES', implode(' ', array(
  Google_Service_Calendar::CALENDAR)
));
 
if (php_sapi_name() != 'cli') {
  throw new Exception('This application must be run on the command line.');
}
 
/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient() {
  $client = new Google_Client();
  $client->setApplicationName(APPLICATION_NAME);
  $client->setScopes(SCOPES);
  $client->setAuthConfig(CLIENT_SECRET_PATH);
  $client->setAccessType('offline');
 
  // Load previously authorized credentials from a file.
  $credentialsPath = expandHomeDirectory(CREDENTIALS_PATH);
  if (file_exists($credentialsPath)) {
    $accessToken = json_decode(file_get_contents($credentialsPath), true);
  } else {
    // Request authorization from the user.
    $authUrl = $client->createAuthUrl();
    printf("Open the following link in your browser:\n%s\n", $authUrl);
    print 'Enter verification code: ';
    $authCode = trim(fgets(STDIN));
 
    // Exchange authorization code for an access token.
    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
 
    // Store the credentials to disk.
    if(!file_exists(dirname($credentialsPath))) {
      mkdir(dirname($credentialsPath), 0700, true);
    }
    file_put_contents($credentialsPath, json_encode($accessToken));
    printf("Credentials saved to %s\n", $credentialsPath);
  }
  $client->setAccessToken($accessToken);
 
 
// Refresh the token if it's expired.
  if ($client->isAccessTokenExpired()) {
    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    file_put_contents($credentialsPath, json_encode($client->getAccessToken()));
  }
  return $client;
}
 
/**
 * Expands the home directory alias '~' to the full path.
 * @param string $path the path to expand.
 * @return string the expanded path.
 */
function expandHomeDirectory($path) {
  $homeDirectory = getenv('HOME');
  if (empty($homeDirectory)) {
    $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
  }
  return str_replace('~', realpath($homeDirectory), $path);
}
 
// Get the API client and construct the service object.
$client = getClient();
$service = new Google_Service_Calendar($client);
$calendarId = 'yourcalendar@gmail.com';
 
$event = new Google_Service_Calendar_Event(array(
  'summary' => $argv[1],
  'description' => $argv[1],
  'start' => array(
    'dateTime' => $argv[2],
    'timeZone' => 'America/New_York',
   ),
  'end' => array(
    'dateTime' => $argv[3],
    'timeZone' => 'America/New_York',
   ),
   ));
 
if (!empty($argv[4])){
  try{
  $existingEvent = $service->events->get($calendarId, trim($argv[4]));
  } catch (Google_Service_Exception $e)
  {
    error_log($e,0);
  }
if (!empty($existingEvent))
  {
    error_log('Existing event info',0);
    $event = $service->events->update($calendarId, trim($argv[4]), $event);
  }
  else
  {
    $event = $service->events->insert($calendarId, $event);
  }
}
else
{
   $event = $service->events->insert($calendarId, $event);
}

print $event->id;