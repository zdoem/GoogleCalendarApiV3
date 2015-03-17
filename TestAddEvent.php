<?php
//
// built from example at:
// http://stackoverflow.com/questions/26064095/inserting-google-calendar-entries-with-service-account
//

//Clendar Admin :LH075-ตารางนัดหมายงาน
$calendarIdOwner = "r73n817q7lg2sj2vh4t3gbn174@group.calendar.google.com";
$title = "ทดสอบ Test Add Event#1";
$desc = "Ladbrokes ร้านรับพนันชื่อดังของเกาะอังกฤษ หั่นราคาให้ “หงส์แดง” ลิเวอร์พูล เป็นเต็ง1 ที่มีโอกาสคว้า ซลาตัน อิบราฮิโมวิช ดาวยิงจอมเก๋า";

$startdate = new DateTime('2015-03-21 10:00', new DateTimeZone('Asia/Bangkok'));
$startdate = $startdate->format('c');
$enddate = new DateTime('2015-03-21 11:00', new DateTimeZone('Asia/Bangkok'));
$enddate = $enddate->format('c');

/*****************************/
// call function to add one event to the calendar (xxxxxxxxxxx@googlemail.com = the calendar owner)
/*****************************/
calendarize($title,$desc,$startdate,$enddate,$calendarIdOwner);

//-----------------------------------------------//
// funtion to add an event to my Google calendar //
//-----------------------------------------------//
function calendarize ($title, $desc, $start_ev_datetime, $end_ev_datetime, $cal_id) {
session_start();
	require_once '/src/Google/autoload.php';
	
	//Google credentials Authication :Admin@gmail.com
	$client_id = '937566136451-p825gi4ad984tgg6b17rfmt248hqmlhl.apps.googleusercontent.com';
	$service_account_name = '937566136451-p825gi4ad984tgg6b17rfmt248hqmlhl@developer.gserviceaccount.com';
	$key_file_location = 'dev1-a20e4344ea69.p12';
	
	//Case: for Config user1@gamil.com
	//$client_id = 'xxxxxxxxxx-ls9hobkt5iktovm4hjvtjee6220690fk.apps.googleusercontent.com';
	// $service_account_name = 'xxxxxxxxxx-ls9hobkt5iktovm4hjvtjee6220690fk@developer.gserviceaccount.com';
	//$key_file_location = 'dev-calendar-go2doem.p12';

	
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
		
    //$calendarList = $calendarService->calendarList;
    
    //Set the Event data
    echo "Set the Event data [start.]<br>";
    $event = new Google_Service_Calendar_Event();
    $event->setSummary($title);
    $event->setDescription($desc);
    $start = new Google_Service_Calendar_EventDateTime();
    $start->setDateTime($start_ev_datetime);
    $start->setTimeZone('Asia/Bangkok');
    $event->setStart($start);
    $end = new Google_Service_Calendar_EventDateTime();
    $end->setDateTime($end_ev_datetime);
    $end->setTimeZone('Asia/Bangkok');
    $event->setEnd($end);
   echo "Set the Event data [End.]<br>";
   
    try {
      $createdEvent = $calendarService->events->insert($cal_id, $event);
    } catch (Exception $e) {
      var_dump($e->getMessage());
      echo "<br>!!!!!Exception Insert Event : "+getMessage()+"<br>";
    }
    echo 'Event Successfully Added with ID: '.$createdEvent->getId();
    
    echo "<br>==============END=================<br>";
}
?>