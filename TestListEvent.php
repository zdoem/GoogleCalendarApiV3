<?php
header('Content-Type: text/html; charset=utf-8');
echo "=====List All Event from calendar Admin ========<br>";

//Clendar Admin :LH075-ตารางนัดหมายงาน  :Admin@gmail.com
$calendarIdOwner = "r73n817q7lg2sj2vh4t3gbn174@group.calendar.google.com";

//Call fuction below
getEventCalendarize($calendarIdOwner);

function getEventCalendarize ($cal_id) {
	session_start();
	require_once '/src/Google/autoload.php';
	
	//Google credentials Authication :Config of Admin@gmail.com
	$client_id = '937566136451-p825gi4ad984tgg6b17rfmt248hqmlhl.apps.googleusercontent.com';
	$service_account_name = '937566136451-p825gi4ad984tgg6b17rfmt248hqmlhl@developer.gserviceaccount.com';
	$key_file_location = 'dev1-a20e4344ea69.p12';
	
	if (!strlen($service_account_name) || !strlen($key_file_location))
		echo missingServiceAccountDetailsWarning();
	$client = new Google_Client();
	echo "New Google_Client [OK.]<br>";
	
	$client->setApplicationName("Dev1");
	if (isset($_SESSION['service_token'])) {
		$client->setAccessToken($_SESSION['service_token']);
	}
	echo "SetApplicationName [OK.]<br>";
	$key = file_get_contents($key_file_location);
	$cred = new Google_Auth_AssertionCredentials(
			$service_account_name,
			array('https://www.googleapis.com/auth/calendar'),
			$key
	);
	
	echo "Google credentials Authication [OK.]<br>";
	$client->setAssertionCredentials($cred);
	if($client->getAuth()->isAccessTokenExpired()) {
		try {
			$client->getAuth()->refreshTokenWithAssertion($cred);
		} catch (Exception $e) {
			var_dump($e->getMessage());
		}
	}
	echo "Get service_token  [OK.]<br>";
	
	
	$_SESSION['service_token'] = $client->getAccessToken();
	$calendarService = new Google_Service_Calendar($client);
	
	echo "Create Google_Service_Calendar  [OK.]<br>";

	$events = $calendarService->events->listEvents($cal_id);
	//event object
	
	while(true) {
		foreach ($events->getItems() as $event) {
			echo "ID: ".$event->getId()." ,Title:".$event->getSummary().",Desc:".$event->getDescription()."<BR>";
		}
		$pageToken = $events->getNextPageToken();
		if ($pageToken) {
			$optParams = array('pageToken' => $pageToken);
			$events = $calendarService->events->listEvents($cal_id, $optParams);
		} else {
			break;
		}
	}	
	
	echo "===========Successfully==========<br>";
}	
?>