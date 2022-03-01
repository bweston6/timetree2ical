# TimeTree2iCal
Create a rolling 7-day ics file from TimeTree to use in better calendar apps.

## Deployment
To use this you need to insert the following details into `public/index.php`:

* Your TimeTree "Personal Access Token" (https://timetreeapp.com/developers/personal_access_tokens).
* The ID of the TimeTree calendar you want to use (see the cURL example at: https://developers.timetreeapp.com/en/docs/api/oauth-app#list-calendars).

you can then run it via PHP on your favourite web-server.

When a client accesses the URL, an ics file will be returned containing the next 7-days of events from your TimeTree calendar. This time window is a limitation of the TimeTree API.

## Further Development
Other things that could be implemented are:

* Additional calendar information from TimeTree, such as location.
* Adding a list of TimeTree calendars into the file.
