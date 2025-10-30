# Using Mini QR PHP Library - Standalone Guide

## Quick Start (Copy PHP Directory Only)

The PHP library is completely standalone and located in the `/php` directory. You can extract just this directory and use it in any PHP project.

### Option 1: Copy the PHP Directory

```bash
# Copy the entire php directory to your project
cp -r php/ /path/to/your/project/mini-qr-php/
```

### Option 2: Use Composer (Recommended)

If you want to use Composer autoloading:

```bash
cd /path/to/your/project
composer require mini-qr/mini-qr-php
```

Or add to your `composer.json`:

```json
{
    "require": {
        "mini-qr/mini-qr-php": "*"
    }
}
```

## Installation Without Composer

If you don't use Composer, simply include the main file:

```php
<?php
// Include the DataEncoding class
require_once '/path/to/php/src/DataEncoding.php';

use MiniQR\DataEncoding;

// Now you can use all functions
$url = DataEncoding::generateUrlData(['url' => 'example.com']);
echo $url; // https://example.com
```

## Complete Usage Examples

### 1. Generate URL for QR Code

```php
<?php
require_once 'php/src/DataEncoding.php';
use MiniQR\DataEncoding;

$url = DataEncoding::generateUrlData(['url' => 'mywebsite.com']);
// Output: https://mywebsite.com

// Use this string with any QR code generator library
// For example with endroid/qr-code:
// $qrCode->setText($url);
```

### 2. WiFi QR Code

```php
<?php
require_once 'php/src/DataEncoding.php';
use MiniQR\DataEncoding;

$wifi = DataEncoding::generateWifiData([
    'ssid' => 'MyHomeNetwork',
    'encryption' => 'WPA',
    'password' => 'SecurePassword123'
]);
// Output: WIFI:T:WPA;S:MyHomeNetwork;P:SecurePassword123;;

// Scan this QR code to connect to WiFi automatically
```

### 3. Contact Card (vCard)

```php
<?php
require_once 'php/src/DataEncoding.php';
use MiniQR\DataEncoding;

$vcard = DataEncoding::generateVCardData([
    'firstName' => 'John',
    'lastName' => 'Doe',
    'email' => 'john@example.com',
    'phoneWork' => '+1-555-0100',
    'phoneMobile' => '+1-555-0123',
    'org' => 'My Company',
    'position' => 'CEO'
]);

// This generates a complete vCard that can be scanned and imported to contacts
```

### 4. Email QR Code

```php
<?php
require_once 'php/src/DataEncoding.php';
use MiniQR\DataEncoding;

$email = DataEncoding::generateEmailData([
    'address' => 'contact@example.com',
    'subject' => 'Hello',
    'body' => 'I scanned your QR code!'
]);
// Output: mailto:contact@example.com?subject=Hello&body=I%20scanned%20your%20QR%20code%21
```

### 5. SMS/Text Message

```php
<?php
require_once 'php/src/DataEncoding.php';
use MiniQR\DataEncoding;

$sms = DataEncoding::generateSmsData([
    'phone' => '+1-555-0123',
    'message' => 'Hello from QR code'
]);
// Output: SMSTO:+1-555-0123:Hello from QR code
```

### 6. Geographic Location

```php
<?php
require_once 'php/src/DataEncoding.php';
use MiniQR\DataEncoding;

$location = DataEncoding::generateLocationData([
    'latitude' => 37.7749,
    'longitude' => -122.4194
]);
// Output: geo:37.7749,-122.4194
```

### 7. Calendar Event

```php
<?php
require_once 'php/src/DataEncoding.php';
use MiniQR\DataEncoding;

$event = DataEncoding::generateEventData([
    'title' => 'Team Meeting',
    'location' => 'Conference Room A',
    'startTime' => '2024-12-25T10:00:00Z',
    'endTime' => '2024-12-25T11:00:00Z'
]);
// Creates an iCalendar event that can be imported
```

## Integration with QR Code Generators

The Mini QR PHP library generates the **data/text** for QR codes. To create actual QR code images, you need a QR code rendering library:

### With endroid/qr-code

```bash
composer require endroid/qr-code
```

```php
<?php
require_once 'php/src/DataEncoding.php';
use MiniQR\DataEncoding;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// Generate WiFi data
$wifiData = DataEncoding::generateWifiData([
    'ssid' => 'MyNetwork',
    'encryption' => 'WPA',
    'password' => 'mypassword'
]);

// Create QR code
$qrCode = QrCode::create($wifiData)
    ->setSize(300)
    ->setMargin(10);

$writer = new PngWriter();
$result = $writer->write($qrCode);

// Save to file
$result->saveToFile('wifi-qr.png');

// Or output directly
header('Content-Type: ' . $result->getMimeType());
echo $result->getString();
```

### With bacon/bacon-qr-code

```bash
composer require bacon/bacon-qr-code
```

```php
<?php
require_once 'php/src/DataEncoding.php';
use MiniQR\DataEncoding;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

// Generate vCard data
$vcardData = DataEncoding::generateVCardData([
    'firstName' => 'Jane',
    'lastName' => 'Smith',
    'email' => 'jane@example.com'
]);

// Create QR code
$renderer = new ImageRenderer(
    new RendererStyle(400),
    new ImagickImageBackEnd()
);
$writer = new Writer($renderer);

// Save to file
$writer->writeFile($vcardData, 'vcard-qr.png');
```

## Real-World Example: Contact Form

```php
<?php
require_once 'php/src/DataEncoding.php';
use MiniQR\DataEncoding;

// Assuming you have form data
$formData = [
    'firstName' => $_POST['first_name'] ?? '',
    'lastName' => $_POST['last_name'] ?? '',
    'email' => $_POST['email'] ?? '',
    'phone' => $_POST['phone'] ?? '',
];

// Generate vCard
$vcard = DataEncoding::generateVCardData([
    'firstName' => $formData['firstName'],
    'lastName' => $formData['lastName'],
    'email' => $formData['email'],
    'phoneMobile' => $formData['phone']
]);

// Now use $vcard with your QR code generator
// Example: Save to database or generate QR image
echo "vCard QR data generated successfully!";
```

## File Structure for Your Project

When using just the PHP library, your project structure could look like:

```
your-project/
├── vendor/              # If using Composer
├── mini-qr-php/        # Copied from php/ directory
│   ├── src/
│   │   └── DataEncoding.php
│   └── examples/
│       ├── basic_usage.php
│       └── advanced_usage.php
├── index.php           # Your main file
└── composer.json       # If using Composer
```

Or even simpler:

```
your-project/
├── lib/
│   └── DataEncoding.php  # Copy from php/src/DataEncoding.php
└── index.php
```

## Testing Your Integration

Run the included examples:

```bash
cd php/examples
php basic_usage.php
php advanced_usage.php
```

## All Available Methods

| Method | Purpose | Example |
|--------|---------|---------|
| `generateTextData()` | Plain text | `['text' => 'Hello']` |
| `generateUrlData()` | Website URL | `['url' => 'example.com']` |
| `generateEmailData()` | Email with optional subject/body | `['address' => 'test@example.com', 'subject' => 'Hi']` |
| `generatePhoneData()` | Phone number | `['phone' => '+1234567890']` |
| `generateSmsData()` | SMS message | `['phone' => '+1234567890', 'message' => 'Hello']` |
| `generateWifiData()` | WiFi credentials | `['ssid' => 'MyNet', 'encryption' => 'WPA', 'password' => 'pass']` |
| `generateVCardData()` | Contact card | `['firstName' => 'John', 'lastName' => 'Doe', 'email' => 'john@example.com']` |
| `generateLocationData()` | GPS coordinates | `['latitude' => 37.7749, 'longitude' => -122.4194]` |
| `generateEventData()` | Calendar event | `['title' => 'Meeting', 'location' => 'Office', 'startTime' => '2024-01-01T10:00:00Z']` |
| `detectDataType()` | Auto-detect QR data type | `detectDataType('https://example.com')` |

## Requirements

- PHP 7.4 or higher
- No dependencies required for the core library
- PHPUnit (optional, only for running tests)

## Support

For more examples, see:
- `php/examples/basic_usage.php` - Basic examples
- `php/examples/advanced_usage.php` - Advanced integration patterns
- `php/README.md` - Complete API reference

## Notes

- The JS/Vue.js web application is separate and not needed for PHP usage
- This library only generates **data** for QR codes, not the images themselves
- Combine with a QR code rendering library (like endroid/qr-code) to create actual QR code images
- All generated data follows international standards (RFC 6350 for vCard, RFC 5545 for iCalendar, etc.)
