<?php
/*
 * Created by Joshua Greenert on 3/30/2021
 * 
 * This script will pull data from the CRM_followups table if the time of the created checked is 
 * within 5 minutes of it's execution.   The resources will be pulled from the tables as long as 
 * the fields for the calendar_id field is confirmed as null, placed into an array to insert into
 * the event, and pushed to the calendar.  Then, the calendar will be called again to get the id; 
 * 
 * NOTE: This calendar has to be fast enough after the event has been sent to it to not incur a
 *       second event creation by the first script.  
 * 
 * TABLES: crm_followups, tblcalendar, crm_resources
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

// Google Calendar Function call
function getClient()
{
    $client = new Google_Client();
    $client->setApplicationName('Google Calendar API New Event');
    $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/CRMToGoogleCalendar.php');
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

    // Pull data from crm table that has been created within 5 minutes  
    $getRecentQuery = "SELECT * FROM crm_followups where created_at > (NOW() - INTERVAL 245 MINUTE) and deleted_at is NULL limit 1";
    $resultGetRecent = $conn->query($getRecentQuery);

    if($resultGetRecent->num_rows > 0){
        while($row = $resultGetRecent->fetch_assoc()){

            // Add in the variables needed for later.
            $crm_id = $row['id'];
            $resource_id = $row['resource_id'];
            $type = $row['type_id'] == 2 ? "Call" : "Meeting";
            $description = $row['description'];
            $event_date = $row['date'];

            $firstname;
            $lastname; 
            $email;
            $starttime;
            $event_title;
            $timezone;
            $datetime;

            // Use the resource id to capture to get user's name, last name, and email from resources table. 
            $getUserDataQuery = "SELECT name, email, lastname FROM crm_resources where id = $resource_id";
            $resultGetUserData = $conn->query($getUserDataQuery);

            if($resultGetUserData->num_rows > 0){
                while($row2 = $resultGetUserData->fetch_assoc()){

                    // Set the user data variables and the description variable to look for.
                    $firstname = $row2['name'];
                    $lastname = $row2['lastname'];
                    $email = $row2['email'];

                    // Use description values to pull query and check calendar_id field.
                    $getStartTimeQuery = "SELECT start FROM tblcalendar where `desc` like '%$description%' and calendar_id is null";
                    $resultGetStartTime = $conn->query($getStartTimeQuery);

                    if($resultGetStartTime->num_rows > 0){
                        while($row3 = $resultGetStartTime->fetch_assoc()){

                            // Convert start time back to numeric time.
                            $starttime = date('H:i:s', $row3['start']);
                            $endtime = date('H:i:s', (strtotime($starttime) + 60*60));

                            // Set event summary title, timezone.
                            $event_title = 'CRM - EVENT (' .$type. ' - ' .$firstname. ' ' .$lastname. ')';
                            $timezone = 'America/New_York';

                            // Set the datetime object to be dynamically entered.
                            $datetime = str_replace(" ", "T", $event_date);
                            $endtime = substr($datetime, 0, 10) . "T". $endtime;

                            // Get the API client and construct the service object.
                            $client = getClient($authCode);
                            $service = new Google_Service_Calendar($client);
                            $calendarId = 'primary';

                            print "Account Link Successful!\n";
                            // Print the next 10 events on the user's calendar.
                            // Left in for proof of life after initialization.
                            $optParams = array(
                            'maxResults' => 10,
                            'orderBy' => 'startTime',
                            'singleEvents' => true,
                            'timeMin' => date('c'),
                            );

                            $results = $service->events->listEvents($calendarId, $optParams);
                            $events = $results->getItems();

                            try{
                                $event = new Google_Service_Calendar_Event(array(
                                    'summary' => $event_title,
                                    'description' =>  $description,
                                    'start' => array(
                                    'dateTime' => $datetime,
                                    'timeZone' => $timezone,
                                    ),
                                    'end' => array(
                                    'dateTime' => $endtime,
                                    'timeZone' => $timezone,
                                    ),
                                    'reminders' => array(
                                    'useDefault' => TRUE,
                                    ),
                                ));
                                
                                $event = $service->events->insert($calendarId, $event);
                                printf('Event created: %s\n', $event->htmlLink);
                            }
                            catch(Exception $e){
                                echo 'Caught exception: ',  $e->getMessage(), "\n";
                            }

                            // Update the event in CRM with the new Google Calendar Event id
                            // Get the API client and construct the service object.
                            $client = getClient();
                            $service = new Google_Service_Calendar($client);

                            print "Account Link Successful!\n";
                            // Print the next 10 events on the user's calendar.
                            // Left in for proof of life after initialization.
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
                                    
                                    // Get the calendarid and set it 
                                    $gcalendarid = strval($event->getId());

                                    // Confirm that the record exists to update the calendar ID to.
                                    $getIDCalendarIDInsert = "Select id from tblcalendar where `desc` like '%$description%' and calendar_id is null and '$gcalendarid' not in (select calendar_id from tblcalendar where calendar_id is not null) ";
                                    $resultGetID = $conn->query($getIDCalendarIDInsert);

                                    if($resultGetID->num_rows > 0){
                                        while($row4 = $resultGetID->fetch_assoc()){

                                            $recordID = $row4['id'];

                                            // Place the google calendar id in the CRM table row.
                                            $insertGoogleID = "Update tblcalendar set calendar_id = '$gcalendarid' where id = $recordID";
                                            if($conn->query($insertGoogleID) === TRUE){
                                                echo "Google Calendar ID updated!";
                                            }
                                            else {
                                                echo "Error: " . $sql . "<br>" . $conn->error;
                                            }
                                        }
                                    }
                                    else{ echo "Description doesn't match, calendar ID is already entered, or calendar_id field is not null!"; }

                                }
                            }


                        }
                    }
                    else{ 
                        echo "No descriptions match or calendar_id fields are not null " .$description;
                        continue; 
                    }
                }
            }
            else{ echo "User not found or query was empty!"; }
        }
    }
    else{ echo "No recent records exist!"; }
}
else{

    // Get the auth code and store it for the authorization execution.
    $authCode = $_GET['code'];

    // Get the API client and construct the service object.
    $client = getClient();
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


// Don't forget to close the connection.
$conn->close();
?>