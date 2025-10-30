<?php
/**
 * Basic Usage Examples for Mini QR PHP Library
 * 
 * This file demonstrates common use cases for the Mini QR data encoding library.
 */

require_once __DIR__ . '/../src/DataEncoding.php';

use MiniQR\DataEncoding;

echo "=== Mini QR PHP Library - Usage Examples ===\n\n";

// Example 1: Generate URL
echo "1. URL Generation:\n";
$url = DataEncoding::generateUrlData(['url' => 'example.com']);
echo "   Input: example.com\n";
echo "   Output: {$url}\n\n";

// Example 2: Generate Email
echo "2. Email with Subject and Body:\n";
$email = DataEncoding::generateEmailData([
    'address' => 'contact@example.com',
    'subject' => 'Hello from QR Code',
    'body' => 'This message was generated from a QR code!'
]);
echo "   Output: {$email}\n\n";

// Example 3: Generate Phone Number
echo "3. Phone Number:\n";
$phone = DataEncoding::generatePhoneData(['phone' => '+1-555-123-4567']);
echo "   Output: {$phone}\n\n";

// Example 4: Generate SMS
echo "4. SMS Message:\n";
$sms = DataEncoding::generateSmsData([
    'phone' => '+1-555-123-4567',
    'message' => 'Hello! Scan this QR code to send me a message.'
]);
echo "   Output: {$sms}\n\n";

// Example 5: Generate WiFi Configuration
echo "5. WiFi Network Configuration:\n";
$wifi = DataEncoding::generateWifiData([
    'ssid' => 'MyHomeNetwork',
    'encryption' => 'WPA',
    'password' => 'SecurePassword123!',
    'hidden' => false
]);
echo "   Output: {$wifi}\n\n";

// Example 6: Generate vCard (Contact Card)
echo "6. vCard (Contact Information):\n";
$vcard = DataEncoding::generateVCardData([
    'firstName' => 'John',
    'lastName' => 'Doe',
    'org' => 'Acme Corporation',
    'position' => 'Senior Developer',
    'phoneWork' => '+1-555-987-6543',
    'phoneMobile' => '+1-555-123-4567',
    'email' => 'john.doe@acme.com',
    'website' => 'https://johndoe.dev',
    'street' => '123 Tech Street',
    'city' => 'San Francisco',
    'state' => 'CA',
    'zipcode' => '94102',
    'country' => 'USA'
]);
echo "   Output:\n" . str_replace("\n", "\n   ", $vcard) . "\n\n";

// Example 7: Generate Geographic Location
echo "7. Geographic Location:\n";
$location = DataEncoding::generateLocationData([
    'latitude' => 37.7749,
    'longitude' => -122.4194
]);
echo "   Output: {$location}\n";
echo "   (San Francisco coordinates)\n\n";

// Example 8: Generate Calendar Event
echo "8. Calendar Event:\n";
$event = DataEncoding::generateEventData([
    'title' => 'Team Meeting',
    'location' => 'Conference Room A',
    'startTime' => '2024-06-15T14:00:00Z',
    'endTime' => '2024-06-15T15:00:00Z'
]);
echo "   Output:\n" . str_replace("\n", "\n   ", $event) . "\n\n";

// Example 9: Data Type Detection
echo "9. Data Type Detection:\n";
$testData = [
    'https://github.com/lyqht/mini-qr',
    'mailto:test@example.com',
    'tel:+1234567890',
    'WIFI:T:WPA;S:TestNetwork;P:password123;;',
];

foreach ($testData as $data) {
    $detected = DataEncoding::detectDataType($data);
    echo "   Input: {$data}\n";
    echo "   Detected Type: {$detected['type']}\n";
    echo "   Parsed Data: " . json_encode($detected['parsedData']) . "\n\n";
}

// Example 10: Special Character Escaping
echo "10. Special Character Escaping:\n";
$ssidWithSpecialChars = 'My;Network,"WiFi"';
$wifi = DataEncoding::generateWifiData([
    'ssid' => $ssidWithSpecialChars,
    'encryption' => 'WPA',
    'password' => 'pass;word"123'
]);
echo "   SSID: {$ssidWithSpecialChars}\n";
echo "   Output: {$wifi}\n";
echo "   (Notice how special characters are escaped)\n\n";

echo "=== Examples Complete ===\n";
