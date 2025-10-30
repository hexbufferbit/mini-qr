# Mini QR - PHP Library

[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)

A PHP library for generating QR code data in various formats.

## Overview

Mini QR is a PHP library that provides utilities for generating properly formatted data for various QR code types. This library handles the data encoding and formatting, which can then be used with any PHP QR code generator library.

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
- ✅ Zero dependencies for core functionality
- ✅ PHP 7.4+ compatible
- ✅ Comprehensive test suite (25 tests, 89 assertions)

## Installation

### Option 1: Copy the PHP Directory

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

## Quick Start

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

// Generate WiFi credentials
$wifi = DataEncoding::generateWifiData([
    'ssid' => 'MyNetwork',
    'encryption' => 'WPA',
    'password' => 'mypassword123',
    'hidden' => false
]);
// Output: WIFI:T:WPA;S:MyNetwork;P:mypassword123;;

// Generate a vCard (version 3.0 by default)
$vcard = DataEncoding::generateVCardData([
    'firstName' => 'John',
    'lastName' => 'Doe',
    'org' => 'Acme Corp',
    'email' => 'john.doe@example.com',
    'phoneWork' => '+1234567890'
]);
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
```

## Documentation

For complete documentation, examples, and API reference, see:

- **[PHP Library Documentation](php/README.md)** - Complete guide with all features and examples
- **[Standalone Usage Guide](php/STANDALONE_USAGE.md)** - How to use the library in your projects
- **[Port Summary](php/PORT_SUMMARY.md)** - Details about the PHP port

## Supported Data Types

| Type     | Generator Method          | Detection                              |
| -------- | ------------------------- | -------------------------------------- |
| Text     | `generateTextData()`      | Default fallback                       |
| URL      | `generateUrlData()`       | `http://` or `https://` prefix         |
| Email    | `generateEmailData()`     | `mailto:` prefix                       |
| Phone    | `generatePhoneData()`     | `tel:` prefix                          |
| SMS      | `generateSmsData()`       | `SMSTO:` or `sms:` prefix              |
| WiFi     | `generateWifiData()`      | `WIFI:` prefix                         |
| vCard    | `generateVCardData()`     | `BEGIN:VCARD`                          |
| Location | `generateLocationData()`  | `geo:` prefix                          |
| Event    | `generateEventData()`     | `BEGIN:VCALENDAR` or `BEGIN:VEVENT`    |

## Testing

Run the test suite using PHPUnit:

```bash
# From the php directory
cd php

# Install dependencies
composer install

# Run tests
composer test
# or
./vendor/bin/phpunit
```

All tests pass with 100% success rate (25 tests, 89 assertions).

## Requirements

- PHP 7.4 or higher
- No external dependencies required for core functionality
- PHPUnit 9.5+ (for testing only)

## Project Structure

```
php/
├── .gitignore              # Git ignore rules for PHP
├── README.md               # Detailed PHP library documentation
├── STANDALONE_USAGE.md     # Usage guide for standalone projects
├── PORT_SUMMARY.md         # PHP port details
├── composer.json           # Composer configuration
├── phpunit.xml            # PHPUnit configuration
├── src/
│   └── DataEncoding.php   # Main library class
├── tests/
│   └── DataEncodingTest.php # Complete test suite
└── examples/
    └── basic_usage.php    # Usage examples
```

## Contributing

Contributions are welcome! Please ensure all tests pass before submitting a pull request.

See [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines.

## License

GPL-3.0-or-later - See [LICENSE](LICENSE) file for details.

## Code of Conduct

Please review our [Code of Conduct](CODE_OF_CONDUCT.md) before contributing.

## Credits

This library provides data encoding utilities for QR code generation. Use it with any PHP QR code generator library to create complete QR codes.
