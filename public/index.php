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

// Get data from TimeTree
$instance = new OauthClient(
	"", // insert personal access token from https://timetreeapp.com/developers/personal_access_tokens 
);
$calendars = (array) $instance->getUpcomingEvents(
	new GetUpcomingEventsParams("Te1ESi6yaHKj", timezone_open("Europe/London"), 7)
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
