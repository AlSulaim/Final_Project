Login Page:
Handles user authentication for the system.
Supports login for both administrators and members.
Validates credentials against database records.
Redirects users to appropriate dashboards based on their role.
Displays error messages for invalid login attempts.

Registration Page:
Allow new users to create member accounts.
Collect personal details and contact information.
Validates all input fields before submission.
Ensures unique usernames and email addresses.
Automatically assigns "Student" role to new members.

Admin:-

Dashboard:
Displays system statistics.
Shows total number of activities, clubs, and registered members.
Includes navigation to manage indicators, clubs, members, and partnerships.
Icons and quick actions are provided for easier access.

Activities Page:
Displays a table of all activities in the system.
Includes: activity name, date, status, and associated club.
Admin can view details of each activity.
No add/edit/delete from admin side.

Activity Details Page:
Shows full details of the selected activity.
Displays cost, time, duration, attendance, and evaluation results.
Shows indicators linked to the activity and performance data.
Evaluation results are view-only.

Clubs Page:
Displays all clubs in the system.
Includes: club name, start date, manager name, and number of members.
Admin can view club details.

Club Details Page:
Displays club profile and related data.
Shows list of members with name, faculty, and department.
Shows all activities linked to the club.

KPIs Page:
Displays a list of all system indicators.
Divided into activity indicators and team indicators.
Admin can add new indicators or delete existing ones.

Analytics Page:
Shows evaluation results for all activities in the system.
Displays average score per indicator across all clubs.
Admin can filter by activity or indicator.
Tables include activity name, indicator name, and average result.

Members Page:
Displays all registered members.
Includes: name, role, college, department, email.
Admin can delete users.

Partnerships Page:
Displays all submitted partnerships by club managers.
Shows: partner name, type, category, status, and associated club.
Admin can approve or reject partnerships.

Partnership Details Page:
Displays full information of a specific partnership.
Includes: partner name, description, type, category, dates, and contact email.
Status can be updated by the admin.



Activity Manager:-

Dashboard : 
Club details appear Icons displayed:
He is the manager of any club , Number of activities available in the club and Number of KPIs available in the system
A table with the existing activities and their status, whether they have started or not


Activity Page : 
 Add a new activity, activity name, date, cost, time, and activity status.
A table with all registered activities and their status: active, pending, ended, and the ability to delete the activity.
When clicking on the activity, it shows you the activity details such as cost, time, attendance, and all members registered for the activity.
The manager can add feedback for the team through indicators and tasks for the team, and all of them will appear in a clear table


Club Page:
Add a new member to the club by their member ID.
A table showing all registered members in the club: name, college, department, and the member can be deleted from the club.
Partnerships Section: A table showing the partnerships of the club with all details


KPIs Page:
A table showing all the KPIs in the system 
activity KPIs and team KPIs


KPIs measurement page for activities and the team:
A specific activity can be selected and an KPI defined, with the results displayed in a table detailing all information.
There is also a table with all evaluations for members, including the evaluation date, member, KPIs, and result
 This table is automatically generated from member evaluations on the activity page.



Member:-

Dashboard:
Club details appear with icons displayed.
Shows if the user is the manager of any club.
Displays the number of activities available in the club and the number of indicators present in the system.
A table displays the existing activities and their status (whether started or not).

Activity Details Page:
Displays the full information of a specific activity.
Includes: activity name, date, cost, attendance, and list of registered members.
Shows assigned team members and their tasks.
Allows the manager to evaluate members using indicators.
Feedback and evaluations appear in structured tables.

My Activities Page:
Displays all activities the logged-in student is registered for.
Divided into:
Upcoming Activities
Completed Activities
Each activity shows its name, date, status, and allows rating or submitting feedback.

Feedback Page:
Displays a table of all submitted feedback.
Shows: activity name, feedback content, feedback score, and the student who submitted it.
The manager can view and analyze feedback for quality improvement.


=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-==-=-=-=-=-=-=-=-


Server Requirements:

XAMPP (includes Apache, MySQL, PHP) 


Setup Instructions:

Install XAMPP

Download and install XAMPP from https://www.apachefriends.org/

Start Apache and MySQL services from XAMPP Control Panel



Database Setup:

Open phpMyAdmin at http://localhost/phpmyadmin

Create a new database named smartactivity

Select the database and click on "Import" tab

Choose the smartactivity.sql file and click "Go"


Project Files Setup:

Place all project files in the htdocs folder of your XAMPP installation

Typically located at: C:\xampp\htdocs\/[smartactivity]



Configuration:

Verify database connection settings in database/db.php ( if you use xaampp you dont need to change anything )



Access the System:

Open your browser and visit: http://localhost/[project-folder]/login.php


=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-==-=-=-=-=-=-=-=-


Default Login Credentials:

Administrator
Username: admin
Password: 123

Activity Manager
Username: act
Password: 123

Member
Username: mmbr
Password: 123