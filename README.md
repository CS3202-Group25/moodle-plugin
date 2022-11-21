# Moodle Plugin for Establishing Workflows to Handle Requests

## Group 25

#### Mentor: Dr Gayashan Amarasinghe

#### Team Members:
#### Balara Sawanmee - 190572L
#### Aruna Senanayake - 190576D
#### Thevin Senath - 190583V

Moodle plugin for Establishing Workflows to Handle Requests provides students an area to submit their requests regarding course content to the relevant lecturer through the Moodle without using emails. Using this plugin students can directly send requests asking for extending deadlines and recorrections to the lecturer and the lecturer can eithe approve or disapprove these requests. If the lecturer approve a extend deadline request, the moodle automatically change the deadline of the relevant assessment and that is a main feature in this plugin.

### Files in the plugin:

As this is an activity plugin, to create the workflow, mod_form.php is used to implement the UI.
The backend functionalities of the activity such as creating, editing and deleting have been implemented in the lib.php.
The view.php is the activity view page. It renders the relevant template according to the capability of a particular user.
The version.php file contains metadata used to describe the plugin, and includes information such as the version number, a list of dependencies, the minimum Moodle version required and maturity of the plugin.
In db/install.xml, the plugin's database has been implemented as an XML file. As a result, when the plugin is installed, the tables that are pertinent to it are automatically added to the Moodle database.
Capabilities have been implemented in db/access.php which is used to control access using the access API of Moodle.
Forms have been implemented as classes in classes/form.
The file db/messages.php added to manage notifications in the plugin and these notifications are handled using functions defined in classes/messagesender.php.
To handle the database operations with workflow, workflow_request and workflow_request_extend tables, classes/workflowcontroller.php and classes/requestcontroller.php are added respectively.
Unit tests have been implemented in the test folder.
Mustache files in templates folder have been used to implement the pages that are not forms.

### Execution:

A local installation of Moodle version 3.9.x is required to test the plugin on your computer and it can be dpwnloaded at https://download.moodle.org. The workflow plugin folder must then be inserted into the moodle/mod directory (it may be downloaded from the GitHub Repository link). To upgrade Moodle, use 
"php admin/cli/upgrade.php" command in the moodle directory. Then Moodle will automatically install the plugin.


All the user instructions for the plugin are mentioned in the User Manual.

### Resources:

GitHub Repository Link: https://github.com/CS3202-Group25/moodle-plugin.git

User Manual: https://drive.google.com/file/d/1metrtl-4O7h-YiceHzU_8dW4AUYVoQfa/view?usp=sharing

UI Testing: https://drive.google.com/drive/folders/1EBddL90YZfKB5DelwqADy-WshbC-2HBx?usp=share_link

Youtube Video Demonstration: https://youtu.be/u1_VF8Qh33o


If you get a clone of this repository do as follows.

1. Download and install Docker - https://docs.docker.com/get-docker/
2. Run ``` docker-compose up --build ``` (make sure not to run XAMPP or any other apache network)
3. Go to http://localhost:8080
4. Log in using credentials
    username - root
    password - example
5. Create a database named 'moodle'
6. Go to http://localhost and setup moodle
7. Develop plugin in the local/workflow directory