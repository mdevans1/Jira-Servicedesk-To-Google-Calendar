# Scriptrunner Script to Add an Event to Google Calendar

This action relies on two scripts. One is a PHP script that lives local on the JIRA ServiceDesk server. One is a ScriptRunner post-function script that runs after a transition.

## PHP Server-side script

The PHP script local to the Jira server relies on the guidance found here: [ Google Calendar API]( https://developers.google.com/google-apps/calendar/quickstart/php)

Composer must be installed on the system and run in the GoogleCalendar directory to resolve the various Google API dependencies
> example script location: /opt/GoogleCalendar/scheduleEvent.php

Modify calendarID to point to your Calendar

`$calendarId = 'yourcalendar@gmail.com';`

The script expects four (4) arguments from the ScriptRunner script in the following order.

1. Event Name/Title
1. Scheduled Date and Time in the Format yyyy-MM-dd'T'HH:mm:ss.SSSXXX 
1. End Date and Time in the format yyyy-MM-dd'T'HH:mm:ss.SSSXXX
1. (optional) Google event ID if previously scheduled

The script returns the Google Event ID that is handled by the ScriptRunner script and stored with the incident


## ScriptRunner Post-Function Script

Two custom fields need to be defined in Jira to be used in the scriptrunner script: 

- Google Event ID - Text Field
- Scheduled Date - Date/Time

Our workflow also includes a state named **Pending** and a Pending Reason that can be set to **Scheduled**. 

These are validated by the script. We also included a transition condition (ScriptRunner workflow function - Allows the transition if this query matches a JQL query) to only appear on Incidents that match the following:

`Scheduled Date" is not EMPTY AND "Pending reason" = Scheduled And resolution = Unresolved`