<?php
// Include composer dependancies
require_once __DIR__ . '/../vendor/autoload.php';

use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\ValueObject\Date as EDate;
use Eluceo\iCal\Domain\ValueObject\DateTime as EDateTime;
use Eluceo\iCal\Domain\ValueObject\SingleDay;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;
use TimeTreeWebApi\OauthApp\OauthClient;
use TimeTreeWebApi\OauthApp\Parameter\GetUpcomingEventsParams;

define("TOKEN", "abcdef1234567890"); // Insert personal access token from https://timetreeapp.com/developers/personal_access_tokens 
define("CALENDAR_ID", "AbCdEf1234"); // Insert calendar ID. See the cURL example at: https://developers.timetreeapp.com/en/docs/api/oauth-app#list-calendars

// Get data from TimeTree
$instance = new OauthClient(TOKEN);
$calendars = (array) $instance->getUpcomingEvents(
	new GetUpcomingEventsParams(CALENDAR_ID, timezone_open("Europe/London"), 7)
);

// Generate events
$generator = function(&$calendars): Generator {
	foreach($calendars["data"] as &$event) {
		$event = (array)$event;
		$ea = (array)$event["attributes"];
		if ($ea["all_day"]) {
			yield (new Event())
				->setSummary($ea["title"])
				->setOccurrence(new SingleDay(new EDate(new DateTime($ea["start_at"]))))
			;
		} else {
			yield (new Event())
				->setSummary($ea["title"])
				->setOccurrence(new TimeSpan(
					new EDateTime(new DateTimeImmutable($ea["start_at"]), false),
					new EDateTime(new DateTimeImmutable($ea["end_at"]), false)
				))
			;
		}
	}
};

// Create calender from generator
$calendar = new Calendar($generator($calendars));
$componentFactory = new CalendarFactory();
$calendarComponent = $componentFactory->createCalendar($calendar);

// Set headers
header('Content-Type: text/calendar; charset=utf-8');
header('Content-Disposition: attachment; filename="cal.ics"');

// Output
echo $calendarComponent;
?>
