<?php

// Just a test file to test the log functions first iteration

require __DIR__ . '/../vendor/autoload.php'; // Inkluder Composer-autoloader

use GuzzleHttp\Client;

function logMessage($message) {
    $logFile = __DIR__ . '/../logs/pipedrive.log';

    // Sjekk om loggmappen finnes, opprett den hvis ikke
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true); // Opprett mappen med riktige tillatelser
    }

    // Skriv meldingen til loggfilen
    file_put_contents($logFile, $message . PHP_EOL, FILE_APPEND);

    // Skriv meldingen til terminalen
    echo $message . PHP_EOL;
}

function fetchLeads($apiToken) {
    $client = new Client([
        'base_uri' => 'https://nettbureauasdevelopmentteam.pipedrive.com/v1/',
        'timeout'  => 5.0,
    ]);

    try {
        $response = $client->get("leads", [
            'query' => [
                'api_token' => $apiToken
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        if (isset($data['data']) && is_array($data['data'])) {
            logMessage("Leads funnet:");
            foreach ($data['data'] as $lead) {
                $leadInfo = sprintf(
                    "ID: %s, Tittel: %s, Person ID: %s, Organisasjon ID: %s",
                    $lead['id'] ?? 'Ukjent',
                    $lead['title'] ?? 'Ukjent',
                    $lead['person_id'] ?? 'Ukjent',
                    $lead['organization_id'] ?? 'Ukjent'
                );
                logMessage($leadInfo);
            }
        } else {
            logMessage("Ingen leads funnet.");
        }
    } catch (Exception $e) {
        logMessage("Feil ved henting av leads: " . $e->getMessage());
    }
}

// Din API-token
$apiToken = 'cc8b7ad043662da5fc83b3359789daea6cf21c8a';

// Kall funksjonen for å hente leads og loggføre dem
fetchLeads($apiToken);
