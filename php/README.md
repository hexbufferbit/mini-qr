# Mini QR - PHP Library (Standalone)

**This is a standalone PHP library** - it works completely independently and doesn't require any JavaScript code. The PHP library generates QR code data that can be used with any PHP QR code generator.

PHP port of the Mini QR JavaScript library for generating QR code data in various formats.

## Features

- ✅ **Data Encoding**: Generate properly formatted data for various QR code types
- 📧 **Email**: mailto URIs with subject, body, cc, and bcc
- 📞 **Phone**: tel URIs for phone numbers
- 💬 **SMS**: SMSTO format for text messages
- 📡 **WiFi**: WiFi network configuration strings
- 👤 **vCard**: Contact cards (supports vCard 2.1, 3.0, and 4.0)
- 📍 **Location**: Geographic coordinates in geo URI format
- 📅 **Calendar Events**: iCalendar format for events
- 🔍 **Auto-Detection**: Automatically detect and parse data types from QR code strings

## Installation

### Option 1: Copy Just the PHP Directory

The simplest way to use this library is to copy the entire `php/` directory to your project:

```bash
# Copy the php directory to your project
cp -r php/ /path/to/your/project/mini-qr/
```

Then include the main file:

```php
<?php
require_once 'mini-qr/src/DataEncoding.php';
use MiniQR\DataEncoding;

// Now you can use all functions
$url = DataEncoding::generateUrlData(['url' => 'example.com']);
```

### Option 2: Using Composer

```bash
composer require mini-qr/mini-qr-php
```

### Option 3: Manual Installation (Single File)

Copy just the `src/DataEncoding.php` file to your project:

```php
require_once 'path/to/php/src/DataEncoding.php';
```

## Usage

### Basic Examples

```php
<?php

use MiniQR\DataEncoding;

// Generate a URL
$url = DataEncoding::generateUrlData(['url' => 'example.com']);
// Output: https://example.com

// Generate an email with subject and body
$email = DataEncoding::generateEmailData([
    'address' => 'contact@example.com',
    'subject' => 'Hello',
    'body' => 'This is a test message'
]);
// Output: mailto:contact@example.com?subject=Hello&body=This%20is%20a%20test%20message

// Generate a phone number
$phone = DataEncoding::generatePhoneData(['phone' => '+1234567890']);
// Output: tel:+1234567890

// Generate an SMS
$sms = DataEncoding::generateSmsData([
    'phone' => '+1234567890',
    'message' => 'Hello from QR code!'
]);
// Output: SMSTO:+1234567890:Hello from QR code!
```

### WiFi Network Configuration

```php
// Generate WiFi credentials
$wifi = DataEncoding::generateWifiData([
    'ssid' => 'MyNetwork',
    'encryption' => 'WPA',
    'password' => 'mypassword123',
    'hidden' => false
]);
// Output: WIFI:T:WPA;S:MyNetwork;P:mypassword123;;

// For open networks
$openWifi = DataEncoding::generateWifiData([
    'ssid' => 'PublicNetwork',
    'encryption' => 'nopass'
]);
// Output: WIFI:T:nopass;S:PublicNetwork;;;
```

### vCard Contact Information

```php
// Generate a vCard (version 3.0 by default)
$vcard = DataEncoding::generateVCardData([
    'firstName' => 'John',
    'lastName' => 'Doe',
    'org' => 'Acme Corp',
    'position' => 'Software Engineer',
    'phoneWork' => '+1234567890',
    'phoneMobile' => '+0987654321',
    'email' => 'john.doe@example.com',
    'website' => 'https://johndoe.com',
    'street' => '123 Main St',
    'city' => 'Springfield',
    'state' => 'IL',
    'zipcode' => '62701',
    'country' => 'USA'
]);

// Generate vCard 4.0
$vcard4 = DataEncoding::generateVCardData([
    'firstName' => 'Jane',
    'lastName' => 'Smith',
    'email' => 'jane@example.com',
    'version' => '4'
]);
```

### Geographic Location

```php
// Generate a geo URI
$location = DataEncoding::generateLocationData([
    'latitude' => 37.7749,
    'longitude' => -122.4194
]);
// Output: geo:37.7749,-122.4194
```

### Calendar Event

```php
// Generate an iCalendar event
$event = DataEncoding::generateEventData([
    'title' => 'Team Meeting',
    'location' => 'Conference Room A',
    'startTime' => '2024-01-15T10:00:00Z',
    'endTime' => '2024-01-15T11:00:00Z'
]);
// Output: BEGIN:VCALENDAR\nVERSION:2.0\n...
```

### Data Type Detection

The library can automatically detect and parse QR code data:

```php
// Detect URL
$result = DataEncoding::detectDataType('https://example.com');
// $result = ['type' => 'url', 'parsedData' => ['url' => 'https://example.com']]

// Detect email
$result = DataEncoding::detectDataType('mailto:test@example.com?subject=Hello');
// $result = ['type' => 'email', 'parsedData' => ['address' => 'test@example.com', 'subject' => 'Hello', ...]]

// Detect WiFi
$result = DataEncoding::detectDataType('WIFI:T:WPA;S:MyNet;P:pass123;;');
// $result = ['type' => 'wifi', 'parsedData' => ['ssid' => 'MyNet', 'encryption' => 'wpa', ...]]

// Detect vCard
$vcard = "BEGIN:VCARD\nVERSION:3.0\nN:Doe;John;;;\nFN:John Doe\nEND:VCARD";
$result = DataEncoding::detectDataType($vcard);
// $result = ['type' => 'vcard', 'parsedData' => ['firstName' => 'John', 'lastName' => 'Doe', ...]]
```

## Supported Data Types

| Type | Generator Method | Detection |
|------|------------------|-----------|
| Text | `generateTextData()` | Default fallback |
| URL | `generateUrlData()` | `http://` or `https://` prefix |
| Email | `generateEmailData()` | `mailto:` prefix |
| Phone | `generatePhoneData()` | `tel:` prefix |
| SMS | `generateSmsData()` | `SMSTO:` or `sms:` prefix |
| WiFi | `generateWifiData()` | `WIFI:` prefix |
| vCard | `generateVCardData()` | `BEGIN:VCARD` |
| Location | `generateLocationData()` | `geo:` prefix |
| Event | `generateEventData()` | `BEGIN:VCALENDAR` or `BEGIN:VEVENT` |

## Special Character Escaping

The library automatically handles special character escaping for different formats:

- **vCard**: Escapes `\`, `,`, `;`
- **WiFi**: Escapes `\`, `;`, `,`, `:`, `"`, `'`
- **iCalendar**: Escapes `\`, `,`, `;`

## Testing

Run the test suite using PHPUnit:

```bash
# Install dependencies
composer install

# Run tests
composer test
# or
./vendor/bin/phpunit
```

## Requirements

- PHP 7.4 or higher
- No external dependencies required for core functionality

## License

GPL-3.0-or-later - See LICENSE file for details

## Credits

This is a PHP port of the [Mini QR](https://github.com/lyqht/mini-qr) JavaScript library.

## Contributing

Contributions are welcome! Please ensure all tests pass before submitting a pull request.

## API Reference

### DataEncoding Class

#### Generator Methods

All generator methods accept an associative array with specific keys and return a formatted string.

##### `generateTextData(array $data): string`
- **Parameters**: `['text' => string]`
- **Returns**: Plain text string

##### `generateUrlData(array $data): string`
- **Parameters**: `['url' => string]`
- **Returns**: URL with https:// prefix if not present

##### `generateEmailData(array $data): string`
- **Parameters**: 
  - `'address'` (required): Email address
  - `'subject'` (optional): Email subject
  - `'body'` (optional): Email body
  - `'cc'` (optional): CC recipients
  - `'bcc'` (optional): BCC recipients
- **Returns**: mailto URI string

##### `generatePhoneData(array $data): string`
- **Parameters**: `['phone' => string]`
- **Returns**: tel URI string

##### `generateSmsData(array $data): string`
- **Parameters**: 
  - `'phone'` (required): Phone number
  - `'message'` (optional): SMS message
- **Returns**: SMSTO format string

##### `generateWifiData(array $data): string`
- **Parameters**:
  - `'ssid'` (required): Network name
  - `'encryption'` (required): 'nopass', 'WEP', or 'WPA'
  - `'password'` (optional): Network password
  - `'hidden'` (optional): Boolean for hidden networks
- **Returns**: WIFI format string

##### `generateVCardData(array $data): string`
- **Parameters**:
  - `'firstName'`, `'lastName'`: Name fields
  - `'org'`: Organization
  - `'position'`: Job title
  - `'phoneWork'`, `'phonePrivate'`, `'phoneMobile'`: Phone numbers
  - `'email'`: Email address
  - `'website'`: Website URL
  - `'street'`, `'city'`, `'state'`, `'zipcode'`, `'country'`: Address fields
  - `'version'`: vCard version ('2', '3', or '4', default: '3')
- **Returns**: vCard format string

##### `generateLocationData(array $data): string`
- **Parameters**: 
  - `'latitude'`: Numeric latitude
  - `'longitude'`: Numeric longitude
- **Returns**: geo URI string

##### `generateEventData(array $data): string`
- **Parameters**:
  - `'title'`: Event title
  - `'location'`: Event location
  - `'startTime'`: Start date/time (DateTime object or string)
  - `'endTime'`: End date/time (DateTime object or string)
- **Returns**: iCalendar format string

#### Detection Methods

##### `detectDataType(string $data): array`
- **Parameters**: Raw QR code data string
- **Returns**: Array with:
  - `'type'`: Detected data type string
  - `'parsedData'`: Associative array of parsed fields

#### Escaping Methods

##### `escapeVCard(string $val): string`
Escapes special characters for vCard format

##### `escapeWiFi(string $val): string`
Escapes special characters for WiFi format

##### `escapeICal(string $val): string`
Escapes special characters for iCalendar format
