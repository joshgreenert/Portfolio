<?php
/*
 * Created by Joshua Greenert on 3/29/2021
 * 
 * This program will fetch all upcoming events from the support Google Calendar, check them against the table
 * in CRM for billing.dang-designs, and insert the ones that don't exist in the proper format.
 * 
 * TABLES: tblcalendar, crm_followups, crm_resources
 */
require '../../../vendor/autoload.php';

// Define the STDIN to supress errors.
define('STDIN',fopen("php://stdin","r"));
include('../../configuration.php');

// Create connection
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// Variables to use.
$userid;
$username;
$email;
$meetingtype;
$type_id;
$admin_id = 14;

/**
 * Returns an authorized API client.
 * @return Google_Client the authorized client object
 */
function getClient($authCode)
{
    $client = new Google_Client();
    $client->setApplicationName('Google Calendar API PHP Quickstart');
    $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/GoogleCalendarToCRM.php');
    $client->setScopes(Google_Service_Calendar::CALENDAR);
    $client->setAuthConfig('credentials.json');
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Load previously authorized token from a file, if it exists.
    // The file token.json stores the user's access and refresh tokens, and is
    // created automatically when the authorization flow completes for the first
    // time.
    $tokenPath = 'token.json';
    if (file_exists($tokenPath)) {
        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $client->setAccessToken($accessToken);
    }

    // If there is no previous token or it's expired.
    if ($client->isAccessTokenExpired()) {
        // Refresh the token if possible, else fetch a new one.
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        } else {
            
            // Check if the auth code exists already and has been sent to the browser for this call.
            if($authCode == "" && !isset($_GET['code'])){
                // Request authorization from the user.
                $authUrl = $client->createAuthUrl();
                printf("Open the following link in your browser:\n%s\n", $authUrl);
                print 'Enter verification code: ';
                $authCode = trim(fgets(STDIN));
            }

            // Exchange authorization code for an access token.
            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
            $client->setAccessToken($accessToken);

            // Check to see if there was an error.
            if (array_key_exists('error', $accessToken)) {
                throw new Exception(join(', ', $accessToken));
            }
        }
        // Save the token to a file.
        if (!file_exists(dirname($tokenPath))) {
            mkdir(dirname($tokenPath), 0700, true);
        }
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
    }
    return $client;
}

// Check the post for information
if(!isset($_GET['code'])){

    // Set the auth code to nothing to begin the execution.
    $authCode = "";

    // Get the API client and construct the service object.
    $client = getClient($authCode);
    $service = new Google_Service_Calendar($client);

    // Print the next 10 events on the user's calendar.
    $calendarId = 'primary';
    $optParams = array(
    'maxResults' => 100,
    'orderBy' => 'startTime',
    'singleEvents' => true,
    'timeMin' => date('c'),
    );
    $results = $service->events->listEvents($calendarId, $optParams);
    $events = $results->getItems();

    if (empty($events)) {
        print "No upcoming events found.\n";
    } else {
        print "Upcoming events:\n";
        foreach ($events as $event) {
            $start = $event->start->dateTime;
            if (empty($start)) {
                $start = $event->start->date;
            }

            // Set the call or meeting; default is call
            $meetingtype = (strpos(strtolower($event->getSummary()), "meeting") != false) ? "meeting" : "call";
            $type_id = ($meetingtype == "call") ? 2 : 1; 

            // Strip the date and time from the event object. MAY NOT BE NEEDED
            $date = substr($start, 0, strpos($start, "T"));
            $time = substr($start, strpos($start, "T") + 1, 8);
            $eventdatemod = $date . " " . $time;

            // Set the start time numeric parse string that goes into the calendar table. 
            // Get the event details needed for the transfer.  date("Y-m-d H:i:s");
            $gcalendarid = strval($event->getId());
            $gcalendartitle = $event->getSummary();
            $gdescription = $event->getDescription();
            $gstarttime = $eventdatemod;
            $crmstarttime = date("Y-m-d H:i:s");
            $tblstarttime = strtotime($time);

            // Get the email listed in the description using regex.
            preg_match_all("/[\._a-zA-Z0-9-+]+@[\._a-zA-Z0-9-]+/i", $gdescription, $matches);
            foreach($matches as $match){
                foreach($match as $mat){
                    $email = strval($mat);
                }
            }

            // Now use the connection to check the table for the calendar id, if it's not found enter it.
            $querycalendarids = "Select * from tblcalendar where calendar_id is not null and calendar_id = '$gcalendarid'";
            $resultcalendarcheck = $conn->query($querycalendarids);

            // We expect this to be null, so that the else statement is what inserts the new event.
            if($resultcalendarcheck->num_rows > 0){
                // There was a record with that id; don't enter it again.
                continue;
            }
            else{

                // Check the users email and find the name to insert at a later step; need name and last name put together and user's id.
                $queryuseremails = "Select id, name, lastname from crm_resources where email = '$email'";
                $resultusernamecheck = $conn->query($queryuseremails);

                if($resultusernamecheck->num_rows > 0){
                    while($row = $resultusernamecheck->fetch_assoc()){

                        $userid = $row['id'];
                        $username = $row['name'] . " " . $row['lastname'];
                        $title = "CRM - " . strval($username);

                        // Insert into the crm follow ups table to use the id later to insert into the description table. 
                        $insertcrmtable = "Insert into crm_followups (resource_id, type_id, admin_id, description, `date`, created_at, updated_at, deleted_at) 
                            values ($userid, $type_id, $admin_id, '$gdescription', '$gstarttime', '$crmstarttime', '$crmstarttime', NULL)";
                        if($conn->query($insertcrmtable) === TRUE){
                            //echo "new record added";

                            // Need to set up piece to add into the desc field for URL.
                            $querynewCRM = "Select id from crm_followups where created_at = '$crmstarttime'";
                            $resultnewCRM = $conn->query($querynewCRM);

                            if($resultnewCRM->num_rows > 0){
                                while($row2 = $resultnewCRM->fetch_assoc()){

                                    // Get the id and set the url to place into the description field.
                                    $crm_id = $row2['id'];
                                    $crm_url = " -( https://[WEBSITE_NAME_REDACTED]/crm.php#!/contacts/$userid/followup/$crm_id )";
                                    $gdescription .= $crm_url;

                                    $insertcalendartable = "Insert into tblcalendar (title, `desc`, start, end, allday, adminid, recurid, calendar_id) values 
                                        ('$title', '$gdescription', '$tblstarttime', 0, 1, $admin_id, 0, '$gcalendarid')";
                                    if($conn->query($insertcalendartable) === TRUE){
                                        echo "new record added";
                                    }
                                    else {
                                        echo "Error: " . $sql . "<br>" . $conn->error;
                                    }
                                }
                            }
                        }
                        else {
                            echo "Error: " . $sql . "<br>" . $conn->error;
                        }
                    }
                }
            }
        }
    }
    
    // Don't forget to close the connection.
    $conn->close();
}
else{

    // Get the auth code and store it for the authorization execution.
    $authCode = $_GET['code'];

    // Get the API client and construct the service object.
    $client = getClient($authCode);
    $service = new Google_Service_Calendar($client);

    print "Account Link Successful!\n";
    // Print the next 10 events on the user's calendar.
    // Left in for proof of life after initialization.
    $calendarId = 'primary';
    $optParams = array(
    'maxResults' => 10,
    'orderBy' => 'startTime',
    'singleEvents' => true,
    'timeMin' => date('c'),
    );
    $results = $service->events->listEvents($calendarId, $optParams);
    $events = $results->getItems();

    if (empty($events)) {
        print "No upcoming events found.\n";
    } else {
        print "Upcoming events:\n";
        foreach ($events as $event) {
            $start = $event->start->dateTime;
            if (empty($start)) {
                $start = $event->start->date;
            }
            printf("%s (%s)\n", $event->getSummary(), $start);
        }
    }
}

?>