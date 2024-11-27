<?php

// This is just a test file to test the relation between persons, orgs and leads. 

require __DIR__ . '/../vendor/autoload.php'; // Inkluder Composer-autoloader

use GuzzleHttp\Client;

$apiToken = 'cc8b7ad043662da5fc83b3359789daea6cf21c8a';
$client = new Client([
    'base_uri' => 'https://nettbureauasdevelopmentteam.pipedrive.com/v1/',
    'timeout'  => 5.0,
]);

function fetchDetails($client, $endpoint, $id, $apiToken) {
    try {
        $response = $client->get("{$endpoint}/{$id}?api_token={$apiToken}");
        $data = json_decode($response->getBody(), true);
        if ($data['success']) {
            return $data['data'];
        } else {
            echo "Kunne ikke hente data fra {$endpoint}. Feil: " . json_encode($data['error']) . PHP_EOL;
            return null;
        }
    } catch (Exception $e) {
        echo "Feil under henting av data fra {$endpoint}: " . $e->getMessage() . PHP_EOL;
        return null;
    }
}

// IDs vi vil sjekke
$organizationId = 31; // ID-en til Nettbureau Test AS
$personId = 35;       // ID-en til Ove Andre Tidemansen
$leadId = '5df24b60-ac61-11ef-ace8-4709d6b5d72a'; // ID-en til leadet

// Hent organisasjonsdetaljer
echo "Henter organisasjonsdetaljer..." . PHP_EOL;
$organizationDetails = fetchDetails($client, 'organizations', $organizationId, $apiToken);
if ($organizationDetails) {
    echo "Organisasjon: " . $organizationDetails['name'] . PHP_EOL;
    echo "Tilknyttede personer: " . json_encode($organizationDetails['owner_id']) . PHP_EOL;
}

// Hent persondetaljer
echo "Henter persondetaljer..." . PHP_EOL;
$personDetails = fetchDetails($client, 'persons', $personId, $apiToken);
if ($personDetails) {
    echo "Person: " . $personDetails['name'] . PHP_EOL;
    echo "Tilknyttet organisasjon: " . ($personDetails['org_id']['name'] ?? 'Ingen tilknytning') . PHP_EOL;
}

// Hent leaddetaljer
echo "Henter leaddetaljer..." . PHP_EOL;
$leadDetails = fetchDetails($client, 'leads', $leadId, $apiToken);
if ($leadDetails) {
    echo "Lead: " . $leadDetails['title'] . PHP_EOL;

    // Sjekk om person_id er et array eller en int
    $leadPersonName = is_array($leadDetails['person_id']) ? ($leadDetails['person_id']['name'] ?? 'Ingen navn') : $leadDetails['person_id'];
    echo "Tilknyttet person: " . $leadPersonName . PHP_EOL;

    // Sjekk om organization_id er et array eller en int
    $leadOrganizationName = is_array($leadDetails['organization_id']) ? ($leadDetails['organization_id']['name'] ?? 'Ingen navn') : $leadDetails['organization_id'];
    echo "Tilknyttet organisasjon: " . $leadOrganizationName . PHP_EOL;
}
