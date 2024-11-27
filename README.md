This project is a PHP-based script for integrating with the Pipedrive API to create organizations, persons, and leads.
These are the required or recommended Software/Tools

(NOTE: When first running it you should get a message that the person, lead and org alerady exists, that is because I have alerady run it once with the parameters in the json file, change these all slightly to see add new people/orgs/leads that do not already exist). 
       The script automatically checks if an organization, person, or lead already exists before attempting to create them.
       Logs and terminal output will indicate any errors or actions taken by the script.

1. PHP (version 7.4 or higher). Ensure it is installed and added to your system's PATH to allow execution from the terminal. Download source https://www.php.net/downloads

2. Composer, a dependency manager for PHP. This is required to install and manage project dependencies such as Guzzle. Download it from https://getcomposer.org/

3. A text editor or integrated development environment (IDE) such as Visual Studio Code, Sublime Text, or similar. I used VS code for this without any major issues. 

-- Required Libraries to Download --

4. GuzzleHTTP. This library is used for making API calls. After installing Composer, run the following command in your terminal to install Guzzle: composer require guzzlehttp/guzzle

5. Clone or download the project files to a local directory of your choice (e.g., integration_project).

6. Ensure that PHP and Composer are properly installed and configured on your system.

7. Use Composer to install the necessary dependencies for the project. Navigate to the project’s root directory in your terminal and execute:  composer install

8. Add your API key to the script. Replace the placeholder in the code with your actual Pipedrive API key.

9. I have included my own test data that I used, this should give a log and terminal output that says that I, my email and lead already exists, change the lead, persons name and email to add a new lead/perso/org .  Replace your test data in the test/test_data.json file.
10. Ensure the structure of the data matches the JSON schema provided in the project. 

11. Run the script using the PHP CLI. Navigate to the directory containing the main script and execute:  php src/pipedrive_lead_integration.php  ( if you went to the src folder like I did, then just remove the src and execute php pipedrive_lead_integration.php)

12. Check the logs/pipedrive.log file for a detailed record of actions and any errors encountered during execution. These should also be printed to the terminal. 


