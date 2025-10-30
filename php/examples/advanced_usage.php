<?php
/**
 * Advanced Integration Example
 * 
 * This example demonstrates more advanced usage patterns and integration scenarios
 * for the Mini QR PHP library.
 */

require_once __DIR__ . '/../src/DataEncoding.php';

use MiniQR\DataEncoding;

echo "=== Advanced Mini QR PHP Integration Examples ===\n\n";

// Example 1: Dynamic vCard generation from user data
echo "1. Dynamic vCard Generation:\n";
function generateContactCard(array $user): string
{
    return DataEncoding::generateVCardData([
        'firstName' => $user['first_name'],
        'lastName' => $user['last_name'],
        'org' => $user['company'] ?? '',
        'position' => $user['title'] ?? '',
        'phoneWork' => $user['work_phone'] ?? '',
        'phoneMobile' => $user['mobile'] ?? '',
        'email' => $user['email'],
        'website' => $user['website'] ?? '',
        'version' => '3'
    ]);
}

$userData = [
    'first_name' => 'Jane',
    'last_name' => 'Developer',
    'email' => 'jane@techcorp.com',
    'company' => 'Tech Corp',
    'title' => 'Lead Engineer',
    'mobile' => '+1-555-0123'
];

$vcard = generateContactCard($userData);
echo "   Generated vCard for {$userData['first_name']} {$userData['last_name']}\n";
echo "   First few lines:\n";
echo "   " . implode("\n   ", array_slice(explode("\n", $vcard), 0, 5)) . "\n...\n\n";

// Example 2: Event invitation system
echo "2. Calendar Event Invitation:\n";
function createEventInvitation(string $title, string $location, \DateTime $start, int $durationMinutes): string
{
    $end = clone $start;
    $end->modify("+{$durationMinutes} minutes");
    
    return DataEncoding::generateEventData([
        'title' => $title,
        'location' => $location,
        'startTime' => $start,
        'endTime' => $end
    ]);
}

$meetingStart = new DateTime('2024-07-01 14:00:00', new DateTimeZone('UTC'));
$event = createEventInvitation('Product Launch Meeting', 'Main Conference Room', $meetingStart, 60);
echo "   Meeting scheduled for: " . $meetingStart->format('Y-m-d H:i:s') . " UTC\n";
echo "   Duration: 60 minutes\n\n";

// Example 3: WiFi credentials for guest network
echo "3. Guest WiFi QR Code Generator:\n";
function generateGuestWiFi(string $networkName, string $password = null): string
{
    $config = [
        'ssid' => $networkName,
        'encryption' => $password ? 'WPA' : 'nopass'
    ];
    
    if ($password) {
        $config['password'] = $password;
    }
    
    return DataEncoding::generateWifiData($config);
}

$guestWifi = generateGuestWiFi('Guest-Network-2024', 'Welcome2024!');
echo "   Network: Guest-Network-2024\n";
echo "   Generated: {$guestWifi}\n\n";

// Example 4: Smart URL shortener with QR data
echo "4. Smart URL Generator:\n";
function generateSmartUrl(string $url, array $analytics = []): string
{
    // In a real application, you might add tracking parameters
    $fullUrl = $url;
    if (!empty($analytics)) {
        $params = http_build_query($analytics);
        $separator = strpos($url, '?') !== false ? '&' : '?';
        $fullUrl .= $separator . $params;
    }
    
    return DataEncoding::generateUrlData(['url' => $fullUrl]);
}

$trackedUrl = generateSmartUrl('example.com/product', [
    'utm_source' => 'qr',
    'utm_medium' => 'print',
    'utm_campaign' => 'summer2024'
]);
echo "   Original: example.com/product\n";
echo "   With tracking: {$trackedUrl}\n\n";

// Example 5: Bulk QR data generation
echo "5. Bulk Contact Export:\n";
$contacts = [
    ['name' => 'Alice Smith', 'email' => 'alice@example.com'],
    ['name' => 'Bob Jones', 'email' => 'bob@example.com'],
    ['name' => 'Carol White', 'email' => 'carol@example.com']
];

echo "   Generating QR data for " . count($contacts) . " contacts:\n";
foreach ($contacts as $contact) {
    $emailData = DataEncoding::generateEmailData([
        'address' => $contact['email'],
        'subject' => 'Hello from ' . $contact['name']
    ]);
    echo "   - {$contact['name']}: Generated email QR\n";
}
echo "\n";

// Example 6: Data type auto-detection and conversion
echo "6. Smart QR Data Parser:\n";
function parseAndEnhanceQRData(string $rawData): array
{
    $detected = DataEncoding::detectDataType($rawData);
    
    $result = [
        'original' => $rawData,
        'type' => $detected['type'],
        'data' => $detected['parsedData'],
        'enhanced' => false
    ];
    
    // Add enhancements based on type
    switch ($detected['type']) {
        case 'url':
            $result['enhanced'] = true;
            $result['clickable'] = '<a href="' . htmlspecialchars($detected['parsedData']['url']) . '">Visit Link</a>';
            break;
        case 'email':
            $result['enhanced'] = true;
            $result['clickable'] = '<a href="mailto:' . htmlspecialchars($detected['parsedData']['address']) . '">Send Email</a>';
            break;
        case 'phone':
            $result['enhanced'] = true;
            $result['clickable'] = '<a href="tel:' . htmlspecialchars($detected['parsedData']['phone']) . '">Call Now</a>';
            break;
    }
    
    return $result;
}

$testData = 'https://github.com/lyqht/mini-qr';
$parsed = parseAndEnhanceQRData($testData);
echo "   Input: {$parsed['original']}\n";
echo "   Type: {$parsed['type']}\n";
echo "   Enhanced: " . ($parsed['enhanced'] ? 'Yes' : 'No') . "\n";
if (isset($parsed['clickable'])) {
    echo "   HTML: {$parsed['clickable']}\n";
}
echo "\n";

// Example 7: Location-based services
echo "7. Location-Based QR Codes:\n";
function generateLocationQR(string $placeName, float $lat, float $lng): array
{
    return [
        'name' => $placeName,
        'qr_data' => DataEncoding::generateLocationData([
            'latitude' => $lat,
            'longitude' => $lng
        ]),
        'map_url' => "https://www.google.com/maps?q={$lat},{$lng}"
    ];
}

$locations = [
    ['name' => 'Eiffel Tower', 'lat' => 48.8584, 'lng' => 2.2945],
    ['name' => 'Statue of Liberty', 'lat' => 40.6892, 'lng' => -74.0445]
];

foreach ($locations as $loc) {
    $qr = generateLocationQR($loc['name'], $loc['lat'], $loc['lng']);
    echo "   {$qr['name']}:\n";
    echo "     QR Data: {$qr['qr_data']}\n";
    echo "     Map URL: {$qr['map_url']}\n";
}
echo "\n";

// Example 8: Multi-format contact export
echo "8. Multi-Format Contact Export:\n";
function exportContactAllFormats(array $contact): array
{
    $formats = [];
    
    // vCard format
    $formats['vcard'] = DataEncoding::generateVCardData($contact);
    
    // Email format
    if (!empty($contact['email'])) {
        $formats['email'] = DataEncoding::generateEmailData([
            'address' => $contact['email'],
            'subject' => 'Contact Request'
        ]);
    }
    
    // Phone format
    if (!empty($contact['phoneMobile'])) {
        $formats['phone'] = DataEncoding::generatePhoneData([
            'phone' => $contact['phoneMobile']
        ]);
    }
    
    // SMS format
    if (!empty($contact['phoneMobile'])) {
        $formats['sms'] = DataEncoding::generateSmsData([
            'phone' => $contact['phoneMobile'],
            'message' => 'Hi, I got your contact from QR code'
        ]);
    }
    
    return $formats;
}

$contact = [
    'firstName' => 'John',
    'lastName' => 'Doe',
    'email' => 'john@example.com',
    'phoneMobile' => '+1-555-9999'
];

$allFormats = exportContactAllFormats($contact);
echo "   Generated " . count($allFormats) . " different QR formats for contact\n";
echo "   Available formats: " . implode(', ', array_keys($allFormats)) . "\n\n";

echo "=== Integration Examples Complete ===\n";
echo "\nThese examples demonstrate how to integrate Mini QR PHP library\n";
echo "into real-world applications and workflows.\n";
