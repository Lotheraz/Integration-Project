<?php

// Include Composer autoloader

require __DIR__ . '/../vendor/autoload.php'; 

use GuzzleHttp\Client;

// Function to log and print messages to the terminal and log
function logAndPrint($message) {
    $logFile = __DIR__ . '/../logs/pipedrive.log';

    // Check if the log directory exists, will create it if does not
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }

    // Add timestamp to the message, the 'date' function formats a local date/time according to a specified format. (Y-m-d H:i:s)
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message";

    // Write the message to the log file
    file_put_contents($logFile, $logMessage . PHP_EOL, FILE_APPEND);

    // Print the log messages to the terminal
    echo $logMessage . PHP_EOL;
}

// Load test data from JSON file
function loadTestData($filePath) {
    if (!file_exists($filePath)) {
        throw new Exception("Test data file not found: $filePath");
    }
    $data = file_get_contents($filePath);
    return json_decode($data, true);
}

// Fetch all organizations. To avoid hanging I've set a timer 
function fetchOrganizations($apiToken) {
    $client = new Client([
        'base_uri' => 'https://nettbureauasdevelopmentteam.pipedrive.com/v1/',// Base entry point for the end point to avoid redundancy. 
        'timeout' => 5.0, 
    ]);

    try {
        // Sends a GET request to the "organizations" endpoint of the Pipedrive API.
        $response = $client->get("organizations", [
            'query' => ['api_token' => $apiToken]
        ]);
        // Extracts the body of the response and decodes the JSON string into a PHP associative array. True = Array and not an object
        $data = json_decode($response->getBody(), true);
        return $data['data'] ?? [];
    } catch (Exception $e) {
        logAndPrint("Error fetching organizations: " . $e->getMessage());
        return [];
    }
}

// Fetch all persons
function fetchPersons($apiToken) {
    $client = new Client([
        'base_uri' => 'https://nettbureauasdevelopmentteam.pipedrive.com/v1/',
        'timeout' => 5.0,
    ]);

    try {
        $response = $client->get("persons", [
            'query' => ['api_token' => $apiToken]
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['data'] ?? [];
    } catch (Exception $e) {
        logAndPrint("Error fetching persons: " . $e->getMessage());
        return [];
    }
}

// Fetch all leads
function fetchLeads($apiToken) {
    $client = new Client([
        'base_uri' => 'https://nettbureauasdevelopmentteam.pipedrive.com/v1/',
        'timeout' => 5.0,
    ]);

    try {
        $response = $client->get("leads", [
            'query' => ['api_token' => $apiToken]
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['data'] ?? [];
    } catch (Exception $e) {
        logAndPrint("Error fetching leads: " . $e->getMessage());
        return [];
    }
}

// Create organization if it doesn't exist
function createOrganization($name, $apiToken) {
    $organizations = fetchOrganizations($apiToken);

    foreach ($organizations as $org) {
        if ($org['name'] === $name) {
            logAndPrint("Organization '{$name}' already exists. ID: {$org['id']}");
            return $org['id'];
        }
    }

    $client = new Client([
        'base_uri' => 'https://nettbureauasdevelopmentteam.pipedrive.com/v1/',
        'timeout' => 5.0,
    ]);

    try {
        $response = $client->post("organizations", [
            'json' => ['name' => $name],
            'query' => ['api_token' => $apiToken]
        ]);

        $data = json_decode($response->getBody(), true);
        if (isset($data['data']['id'])) {
            logAndPrint("Organization '{$name}' created with ID: " . $data['data']['id']);
            return $data['data']['id'];
        }
    } catch (Exception $e) {
        logAndPrint("Error creating organization: " . $e->getMessage());
    }

    return null;
}

// Create person if it doesn't exist
function createPerson($name, $email, $phone, $orgId, $contactType, $apiToken) {
    $persons = fetchPersons($apiToken);

// Loops through the list of persons retrieved from the API to check if a person with the same email already exists.
// If a match is found, logs the message and returns the existing person's ID. 
    foreach ($persons as $person) {
        if ($person['email'][0]['value'] === $email) {
            logAndPrint("Person with email '{$email}' already exists. ID: {$person['id']}");
            return $person['id'];
        }
    }

    $client = new Client([
        'base_uri' => 'https://nettbureauasdevelopmentteam.pipedrive.com/v1/',  
        'timeout' => 5.0, 
    ]);

    try {
        $response = $client->post("persons", [
            'json' => [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'org_id' => $orgId,
                'fd460d099264059d975249b20e071e05392f329d' => $contactType // Contact Type Field ID ( single option, I assume a drop down mentu or something similar)
            ],
            'query' => ['api_token' => $apiToken]
        ]);
// Checks if the response contains the key data with a subkey id, indicating successful creation of the person.
        $data = json_decode($response->getBody(), true);
        if (isset($data['data']['id'])) {
            logAndPrint("Person '{$name}' created with ID: " . $data['data']['id']);
            return $data['data']['id'];
        }
    } catch (Exception $e) {
        logAndPrint("Error creating person: " . $e->getMessage());
    }

    return null;
}

// Create lead if it doesn't exist
function createLead($title, $personId, $orgId, $housingType, $propertySize, $dealType, $comment, $apiToken) {
    $leads = fetchLeads($apiToken);

    foreach ($leads as $lead) {
        if ($lead['title'] === $title) {
            logAndPrint("Lead with title '{$title}' already exists. ID: {$lead['id']}");
            return;
        }
    }

    $client = new Client([
        'base_uri' => 'https://nettbureauasdevelopmentteam.pipedrive.com/v1/',
        'timeout' => 5.0,
    ]);

    try {
        $response = $client->post("leads", [
            'json' => [
                'title' => $title,
                'person_id' => $personId,
                'organization_id' => $orgId,
                '9cbbad3c5d83d6d258ef27db4d3784b5e0d5fd32' => $housingType,
                '7a275c324d7fbe5ab62c9f05bfbe87dad3acc3ba' => $propertySize,
                'cebe4ad7ce36c3508c3722b6e0072c6de5250586' => $dealType,
                '479370d7514958b2b4b4049c37be492f357fe7d8' => $comment // Comment Field ID
            ],
            'query' => ['api_token' => $apiToken]
        ]);

        $data = json_decode($response->getBody(), true);
        if (isset($data['data']['id'])) {
            logAndPrint("Lead '{$title}' created with ID: " . $data['data']['id']);
        }
    } catch (Exception $e) {
        logAndPrint("Error creating lead: " . $e->getMessage());
    }
}

// Main execution
try {
    $testData = loadTestData(__DIR__ . '/../test/test_data.json');
    $apiToken = 'cc8b7ad043662da5fc83b3359789daea6cf21c8a';

    $organizationName = $testData['organization']['name'];
    $personName = $testData['person']['name'];
    $personEmail = $testData['person']['email'];
    $personPhone = $testData['person']['phone'];
    $contactType = $testData['person']['contact_type'];
    $leadTitle = $testData['lead']['title']; 
    $housingType = $testData['lead']['housing_type'];
    $propertySize = $testData['lead']['property_size'];
    $dealType = $testData['lead']['deal_type'];
    $comment = $testData['lead']['comment']; 

 // If the organization was successfully created or already exists, continue and create the person.
 // If the person was successfully created or already exists, proceed to create the lead.
    $orgId = createOrganization($organizationName, $apiToken);
    if ($orgId) {
        $personId = createPerson($personName, $personEmail, $personPhone, $orgId, $contactType, $apiToken);
        if ($personId) {
            createLead($leadTitle, $personId, $orgId, $housingType, $propertySize, $dealType, $comment, $apiToken);
        }
    }
} catch (Exception $e) {
    logAndPrint("Error: " . $e->getMessage());
}
