# Scriptrunner Script to Add an Event to Google Calendar

This action relies on two scripts. One is a PHP script that lives local on the JIRA ServiceDesk server. One is a ScriptRunner post-function script that runs after a transition.

## PHP Server-side script

The PHP script local to the JIRA server relies on the guidance found here: [ Google Calendar API]( https://developers.google.com/google-apps/calendar/quickstart/php)

Composer must be installed on the system and run in the GoogleCalendar directory to resolve the various Google API dependencies
> example script location: /opt/GoogleCalendar/scheduleEvent.php

## ScriptRunner Post-Function Script

Two custom fields are used in the scriptrunner script: 

- Google Event ID - Text Field
- Scheduled Date - Date/Time

Our workflow includes a state named **Pending** and a Pending Reason that can be set to **Scheduled**. 

These are validated by the script. We also included a transition condition (ScriptRunner workflow function - Allows the transition if this query matches a JQL query) to only appear on Incidents that match the following:

`Scheduled Date" is not EMPTY AND "Pending reason" = Scheduled And resolution = Unresolved`