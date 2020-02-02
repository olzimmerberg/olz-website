<?php

require_once('../SimpleCalDAVClient.php');

$created = '20140403T091024Z';
$created = date('YmdTHis').'Z';
$modified = $created;
$ical_created = $created ;
$uid = 'testuid1';
$summary = 'test summary';
$start = '20160418T120000';
$ende = '20160418T130000';
$ort = 'test ort';
$description = 'test description';
$lb = '
';
$firstNewEvent = 'BEGIN:VCALENDAR
PRODID:-//olz_termine
BEGIN:VTIMEZONE
TZID:Europe/Berlin
BEGIN:DAYLIGHT
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
TZNAME:CEST
DTSTART:19700329T020000
RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=3
END:DAYLIGHT
BEGIN:STANDARD
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
TZNAME:CET
DTSTART:19701025T030000
RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=10
END:STANDARD
END:VTIMEZONE
BEGIN:VEVENT
CREATED:'.$created.$lb.
'LAST-MODIFIED:'.$modified.$lb.
'DTSTAMP:'.$ical_created.$lb.
'UID:'.$uid.$lb.
'SUMMARY:'.$summary.$lb.
'DTSTART;TZID=Europe/Berlin:'.$start.$lb.
'DTEND;TZID=Europe/Berlin:'.$ende.$lb.
'LOCATION:'.$ort.$lb.
'DESCRIPTION:'.$description.$lb.
'END:VEVENT
END:VCALENDAR';

/*$firstNewEvent = 'BEGIN:VCALENDAR
PRODID:-//SomeExampleStuff//EN
VERSION:2.0
BEGIN:VTIMEZONE
TZID:Europe/Berlin
X-LIC-LOCATION:Europe/Berlin
BEGIN:DAYLIGHT
TZOFFSETFROM:+0100
TZOFFSETTO:+0200
TZNAME:CEST
DTSTART:19700329T020000
RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=3
END:DAYLIGHT
BEGIN:STANDARD
TZOFFSETFROM:+0200
TZOFFSETTO:+0100
TZNAME:CET
DTSTART:19701025T030000
RRULE:FREQ=YEARLY;BYDAY=-1SU;BYMONTH=10
END:STANDARD
END:VTIMEZONE
BEGIN:VEVENT
CREATED:'.$created.
'LAST-MODIFIED:20140403T091044Z
DTSTAMP:20140416T091044Z
UID:'.$uid.$lb.
'SUMMARY:ExampleEvent1
DTSTART;TZID=Europe/Berlin:20160418T120000
DTEND;TZID=Europe/Berlin:20160418T130000
LOCATION:ExamplePlace1
DESCRIPTION:ExampleDescription1
END:VEVENT
END:VCALENDAR';*/


$client = new SimpleCalDAVClient();

try {
	/*
	 * To establish a connection and to choose a calendar on the server, use
	 * connect()
	 * findCalendars()
	 * setCalendar()
	 */
	
	$client->connect('https://utzinger-planung.ch/mycloud/remote.php/dav/', 'test', '1234');
	$arrayOfCalendars = $client->findCalendars(); // Returns an array of all accessible calendars on the server.
	$client->setCalendar($arrayOfCalendars["olz-termine_shared_by_ursu"]); // Here: Use the calendar ID of your choice. If you don't know which calendar ID to use, try config/listCalendars.php
    
	/*
	 * You can create calendar objects (e.g. events, todos,...) on the server with create().
	 * Just pass a string with the iCalendar-data which should be saved on the server.
	 * The function returns a CalDAVObject (see CalDAVObject.php) with the stored information about the new object on the server
	 */
//	$firstNewEventOnServer = $client->create($firstNewEvent); // Creates $firstNewEvent on the server and a CalDAVObject representing the event.
    
	/*
	 * You can getEvents with getEvents()
	 */
	
	$events = $client->getEvents('20160401T103000Z', '20160419T200000Z'); // Returns array($secondNewEventOnServer);

	foreach ( $events as $_event ){
		echo $_event->getEtag(); // Prints $secondNewEvent. See CalDAVObject.php
		echo '<br>';
		}
	

}

catch (Exception $e) {
	echo $e->__toString();
}

?>