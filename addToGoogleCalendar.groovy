import com.atlassian.jira.component.ComponentAccessor
import com.atlassian.jira.issue.Issue
import com.atlassian.jira.issue.ModifiedValue
import com.atlassian.jira.issue.util.DefaultIssueChangeHolder
import groovy.time.TimeCategory
import com.atlassian.jira.issue.MutableIssue
import com.atlassian.jira.issue.IssueManager
import com.atlassian.jira.ComponentManager
import org.apache.commons.lang.time.DateUtils;
import java.text.SimpleDateFormat;
import java.util.Date.*;
import com.atlassian.jira.event.type.EventDispatchOption
import com.atlassian.jira.security.Permissions
import com.atlassian.jira.user.util.UserUtil
 

def customFieldManager = ComponentAccessor.getCustomFieldManager()
def cmd = ["php", "/opt/GoogleCalendar/scheduleEvent.php"]
def summary = issue.getKey() + " " + issue.summary
def reason = customFieldManager.getCustomFieldObjectByName("Pending reason")
def scheduledDate = customFieldManager.getCustomFieldObjectByName("Scheduled Date")
def googleID = customFieldManager.getCustomFieldObjectByName("Google Event ID")

// Is the event being scheduled
if (reason !=null)
{

    if (issue.getCustomFieldValue(reason).toString() == "Scheduled")
    {

        //Was a due date set?
        if (scheduledDate != null)
        {
            //Get Scheduled Date
            def scheduleDate = new Date()
            scheduleDate = issue.getCustomFieldValue(scheduledDate)

            //set event to 30 minutes long
            def endDate = DateUtils.addMinutes(scheduleDate,30)

            //format date to Google expected format
            scheduleDate = scheduleDate.format("yyyy-MM-dd'T'HH:mm:ss.SSSXXX")
            endDate = endDate.format("yyyy-MM-dd'T'HH:mm:ss.SSSXXX")
            def scheduleComponents = [summary, scheduleDate, endDate]
            //has this event been scheduled before?
            if (googleID != null)
            {
                if (issue.getCustomFieldValue(googleID))
                {
                    def google = issue.getCustomFieldValue(googleID).toString()
                    scheduleComponents.add(google)
                }

            }           

            for (argument in scheduleComponents)
            {
                cmd.add(argument)
            }

            def proc = cmd.execute()
            def outputStream = new StringBuffer();
            proc.waitForProcessOutput(outputStream, System.err)
            def user = ComponentAccessor.getJiraAuthenticationContext().getLoggedInUser()
            issue.setCustomFieldValue(googleID, outputStream.toString())
            IssueManager issueManager = ComponentAccessor.getIssueManager()
            issueManager.updateIssue(user, issue, EventDispatchOption.DO_NOT_DISPATCH, false)
        }
    }
}