<?php

$guest = $_SERVER['REMOTE_ADDR'];

// only allow local access
$allowed = 0;
if(
  substr($guest, 0, 3) == '10.' ||
  substr($guest, 0, 7) == '172.16.' ||
  substr($guest, 0, 7) == '172.17.' ||
  substr($guest, 0, 8) == '192.168.' ||
  $guest == '127.0.0.1'
) {
    $allowed = 1;
  }
if ($allowed == 0) {
  $response = array(
    'success'=>false,
    'content'=>array(
      'error'=>'Remote host not allowed'
    )
  );
  echo json_encode($response);
  exit();
}
require "livestatus_client.php";

// FIXME: Do we really want unlimited memory?
ini_set('memory_limit', -1);

header('Content-Type: application/json');

$path_parts = explode('/', $_SERVER['REQUEST_URI']);
$request_method = $_SERVER['REQUEST_METHOD'];

if (is_array($path_parts)) {
  foreach ($path_parts as $key => $part) {
    if ($part == '' || $part == 'nems-api') unset($path_parts[$key]);
  }
  sort($path_parts);
  $action = $path_parts[0];
  $tmp = explode('?',$action);
  $action = $tmp[0];
}

$client = new LiveStatusClient('/usr/local/nagios/var/rw/live.sock');
$client->pretty_print = true;

$response = [ 'success' => true ];
$args = json_decode(file_get_contents("php://input"),true);
//$args = $_GET;

try {
    switch ($action) {

    case 'acknowledge_problem':
        $client->acknowledgeProblem($args);
        break;

    case 'cancel_downtime':
        $client->cancelDowntime($args);
        break;

    case 'schedule_downtime':
        $client->scheduleDowntime($args);
        break;

    case 'enable_notifications':
        $client->enableNotifications($args);
        break;

    case 'disable_notifications':
        $client->disableNotifications($args);
        break;

    default:
        $response['content'] =  $client->getQuery($action, $_GET);

    }

} catch (LiveStatusException $e) {
    $response['success'] = false;
    $response['content'] = ['code' => $e->getCode(), 'message' => $e->getMessage()];
    http_response_code($e->getCode());
}
echo json_encode($response);

?>
