<?php

namespace MiniQR;

/**
 * DataEncoding - Utilities for encoding various data types into QR code formats
 * 
 * This class provides methods to encode different types of data (text, URLs, emails,
 * phone numbers, WiFi credentials, vCards, locations, and calendar events) into
 * standardized formats suitable for QR code generation.
 */
class DataEncoding
{
    /**
     * Generic function to escape special characters in a string
     * 
     * @param string $val The string to escape
     * @param string $charsToEscape Characters that need to be escaped
     * @return string The escaped string
     */
    private static function escapeSpecialChars(string $val, string $charsToEscape): string
    {
        if (empty($val)) {
            return '';
        }
        
        $chars = str_split($charsToEscape);
        $result = $val;
        
        foreach ($chars as $char) {
            $result = str_replace($char, '\\' . $char, $result);
        }
        
        return $result;
    }

    /**
     * Escapes special characters for vCard format: \ , ;
     * Based on RFC 6350 (vCard 4.0) and RFC 2426 (vCard 3.0)
     * 
     * @param string $val The string to escape
     * @return string The escaped string
     * @see https://datatracker.ietf.org/doc/html/rfc6350
     * @see https://datatracker.ietf.org/doc/html/rfc2426
     */
    public static function escapeVCard(string $val): string
    {
        return self::escapeSpecialChars($val, '\\,;');
    }

    /**
     * Escapes special characters for WiFi format: \ ; , : " '
     * Based on WPA/WPA2 Enterprise Configuration Specification
     * 
     * @param string $val The string to escape
     * @return string The escaped string
     * @see https://github.com/zxing/zxing/wiki/Barcode-Contents#wi-fi-network-config-android-ios-11
     */
    public static function escapeWiFi(string $val): string
    {
        return self::escapeSpecialChars($val, '\\;,:"\'');
    }

    /**
     * Escapes special characters for iCalendar format: \ , ;
     * Based on RFC 5545 (iCalendar)
     * 
     * @param string $val The string to escape
     * @return string The escaped string
     * @see https://datatracker.ietf.org/doc/html/rfc5545
     */
    public static function escapeICal(string $val): string
    {
        return self::escapeSpecialChars($val, '\\,;');
    }

    /**
     * Formats a DateTime object or date string into YYYYMMDDTHHMMSSZ format for iCalendar
     * 
     * @param \DateTime|string $dateTime The date/time to format
     * @return string The formatted date string, or empty string on error
     */
    private static function formatICalDateTime($dateTime): string
    {
        try {
            if (is_string($dateTime)) {
                $date = new \DateTime($dateTime);
            } elseif ($dateTime instanceof \DateTime) {
                $date = $dateTime;
            } else {
                return '';
            }
            
            return $date->format('Ymd\THis\Z');
        } catch (\Exception $e) {
            error_log('Error formatting iCal date: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Generates plain text data for QR code
     * 
     * @param array $data Array with 'text' key
     * @return string Formatted text string
     */
    public static function generateTextData(array $data): string
    {
        return $data['text'] ?? '';
    }

    /**
     * Generates a URL string for QR code, ensuring proper http/https prefix
     * 
     * @param array $data Array with 'url' key
     * @return string Formatted URL string with protocol
     */
    public static function generateUrlData(array $data): string
    {
        $url = $data['url'] ?? '';
        if (empty($url)) {
            return '';
        }
        
        if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
            return $url;
        }
        
        return 'https://' . $url;
    }

    /**
     * Generates a mailto URI string for email QR codes
     * 
     * @param array $data Array with 'address' and optional 'subject', 'body', 'cc', 'bcc' keys
     * @return string Formatted mailto URI string
     */
    public static function generateEmailData(array $data): string
    {
        $address = $data['address'] ?? '';
        if (empty($address)) {
            return '';
        }
        
        $parts = [];
        if (!empty($data['subject'])) {
            $parts[] = 'subject=' . rawurlencode($data['subject']);
        }
        if (!empty($data['body'])) {
            $parts[] = 'body=' . rawurlencode($data['body']);
        }
        if (!empty($data['cc'])) {
            $parts[] = 'cc=' . rawurlencode($data['cc']);
        }
        if (!empty($data['bcc'])) {
            $parts[] = 'bcc=' . rawurlencode($data['bcc']);
        }
        
        $query = count($parts) > 0 ? '?' . implode('&', $parts) : '';
        return 'mailto:' . $address . $query;
    }

    /**
     * Generates a tel URI string for phone number QR codes
     * 
     * @param array $data Array with 'phone' key
     * @return string Formatted tel URI string
     */
    public static function generatePhoneData(array $data): string
    {
        $phone = $data['phone'] ?? '';
        return !empty($phone) ? 'tel:' . $phone : '';
    }

    /**
     * Generates an SMS string for SMS QR codes
     * 
     * @param array $data Array with 'phone' and optional 'message' keys
     * @return string Formatted SMS URI string
     */
    public static function generateSmsData(array $data): string
    {
        $phone = $data['phone'] ?? '';
        if (empty($phone)) {
            return '';
        }
        
        $message = $data['message'] ?? '';
        return 'SMSTO:' . $phone . ':' . $message;
    }

    /**
     * Generates a WiFi network string for WiFi QR codes
     * 
     * @param array $data Array with 'ssid', 'encryption', optional 'password', 'hidden' keys
     * @return string Formatted WiFi string
     */
    public static function generateWifiData(array $data): string
    {
        $ssid = $data['ssid'] ?? '';
        if (empty($ssid)) {
            return '';
        }
        
        $ssid = self::escapeWiFi($ssid);
        $encryption = $data['encryption'] ?? 'nopass';
        $hidden = isset($data['hidden']) && $data['hidden'] ? 'H:true;' : '';
        
        if ($encryption === 'nopass') {
            return "WIFI:T:nopass;S:{$ssid};;{$hidden};";
        } else {
            $password = self::escapeWiFi($data['password'] ?? '');
            return "WIFI:T:{$encryption};S:{$ssid};P:{$password};{$hidden};";
        }
    }

    /**
     * Generates a vCard format string from contact information
     * 
     * @param array $data Array with contact information fields
     * @return string Formatted vCard string
     */
    public static function generateVCardData(array $data): string
    {
        $lines = [];
        $lines[] = 'BEGIN:VCARD';
        
        $version = $data['version'] ?? '3';
        if ($version === '2') {
            $lines[] = 'VERSION:2.1';
        } elseif ($version === '4') {
            $lines[] = 'VERSION:4.0';
        } else {
            $lines[] = 'VERSION:3.0';
        }
        
        $firstName = self::escapeVCard($data['firstName'] ?? '');
        $lastName = self::escapeVCard($data['lastName'] ?? '');
        
        if (!empty($firstName) || !empty($lastName)) {
            $lines[] = "N:{$lastName};{$firstName};;;";
            $lines[] = 'FN:' . trim("{$firstName} {$lastName}");
        }
        
        if (!empty($data['org'])) {
            $lines[] = 'ORG:' . self::escapeVCard($data['org']);
        }
        if (!empty($data['position'])) {
            $lines[] = 'TITLE:' . self::escapeVCard($data['position']);
        }
        
        // Format telephone entries based on vCard version
        if (!empty($data['phoneWork'])) {
            $phone = self::escapeVCard($data['phoneWork']);
            if ($version === '2') {
                $lines[] = "TEL;WORK;VOICE:{$phone}";
            } elseif ($version === '4') {
                $lines[] = "TEL;TYPE=work,voice;VALUE=uri:tel:{$phone}";
            } else {
                $lines[] = "TEL;TYPE=WORK,VOICE:{$phone}";
            }
        }
        
        if (!empty($data['phonePrivate'])) {
            $phone = self::escapeVCard($data['phonePrivate']);
            if ($version === '2') {
                $lines[] = "TEL;HOME;VOICE:{$phone}";
            } elseif ($version === '4') {
                $lines[] = "TEL;TYPE=home,voice;VALUE=uri:tel:{$phone}";
            } else {
                $lines[] = "TEL;TYPE=HOME,VOICE:{$phone}";
            }
        }
        
        if (!empty($data['phoneMobile'])) {
            $phone = self::escapeVCard($data['phoneMobile']);
            if ($version === '2') {
                $lines[] = "TEL;CELL;VOICE:{$phone}";
            } elseif ($version === '4') {
                $lines[] = "TEL;TYPE=cell,voice;VALUE=uri:tel:{$phone}";
            } else {
                $lines[] = "TEL;TYPE=CELL,VOICE:{$phone}";
            }
        }
        
        // Email format differs by version
        if (!empty($data['email'])) {
            $email = self::escapeVCard($data['email']);
            if ($version === '2') {
                $lines[] = "EMAIL;INTERNET:{$email}";
            } elseif ($version === '4') {
                $lines[] = "EMAIL;TYPE=work:{$email}";
            } else {
                $lines[] = "EMAIL:{$email}";
            }
        }
        
        // URL format
        if (!empty($data['website'])) {
            $website = self::escapeVCard($data['website']);
            if ($version === '4') {
                $lines[] = "URL;TYPE=work:{$website}";
            } else {
                $lines[] = "URL:{$website}";
            }
        }
        
        $street = self::escapeVCard($data['street'] ?? '');
        $city = self::escapeVCard($data['city'] ?? '');
        $state = self::escapeVCard($data['state'] ?? '');
        $zipcode = self::escapeVCard($data['zipcode'] ?? '');
        $country = self::escapeVCard($data['country'] ?? '');
        
        $addressComponents = [$street, $city, $state, $zipcode, $country];
        
        // Only add ADR if at least one address component is present
        if (!empty(array_filter($addressComponents))) {
            $adrString = "{$street};{$city};{$state};{$zipcode};{$country}";
            if ($version === '2') {
                $lines[] = "ADR;WORK:;;{$adrString}";
            } elseif ($version === '4') {
                $lines[] = "ADR;TYPE=work:;;{$adrString}";
            } else {
                $lines[] = "ADR;TYPE=WORK:;;{$adrString}";
            }
        }
        
        $lines[] = 'END:VCARD';
        return implode("\n", $lines);
    }

    /**
     * Generates a geographic location URI string for location QR codes
     * 
     * @param array $data Array with 'latitude' and 'longitude' keys
     * @return string Formatted geo URI string
     */
    public static function generateLocationData(array $data): string
    {
        $latitude = (string)($data['latitude'] ?? '');
        $longitude = (string)($data['longitude'] ?? '');
        
        // Basic validation
        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return '';
        }
        
        return "geo:{$latitude},{$longitude}";
    }

    /**
     * Generates a calendar event string in iCalendar format for event QR codes
     * 
     * @param array $data Array with 'title', 'location', 'startTime', 'endTime' keys
     * @return string Formatted iCalendar string
     */
    public static function generateEventData(array $data): string
    {
        $lines = [];
        $lines[] = 'BEGIN:VEVENT';
        
        if (!empty($data['title'])) {
            $lines[] = 'SUMMARY:' . self::escapeICal($data['title']);
        }
        if (!empty($data['location'])) {
            $lines[] = 'LOCATION:' . self::escapeICal($data['location']);
        }
        
        $dtStart = !empty($data['startTime']) ? self::formatICalDateTime($data['startTime']) : '';
        $dtEnd = !empty($data['endTime']) ? self::formatICalDateTime($data['endTime']) : '';
        
        if (!empty($dtStart)) {
            $lines[] = "DTSTART:{$dtStart}";
        }
        if (!empty($dtEnd)) {
            $lines[] = "DTEND:{$dtEnd}";
        }
        
        // Optionally add DTSTAMP (creation timestamp)
        $lines[] = 'DTSTAMP:' . self::formatICalDateTime(new \DateTime());
        
        $lines[] = 'END:VEVENT';
        
        // Wrap in VCALENDAR
        return "BEGIN:VCALENDAR\nVERSION:2.0\n" . implode("\n", $lines) . "\nEND:VCALENDAR";
    }

    /**
     * Detect data type from a string and parse it into structured data
     * 
     * @param string $data The input string to detect and parse
     * @return array Array with 'type' and 'parsedData' keys
     */
    public static function detectDataType(string $data): array
    {
        // Default result
        $result = [
            'type' => 'text',
            'parsedData' => ['text' => $data]
        ];
        
        if (empty($data)) {
            return $result;
        }
        
        // vCard detection
        if (preg_match('/^BEGIN:VCARD/i', $data)) {
            $result['type'] = 'vcard';
            $result['parsedData'] = [];
            
            $fullContent = str_replace("\r", '', $data);
            $lines = explode("\n", $fullContent);
            
            // Detect vCard version
            foreach ($lines as $line) {
                if (preg_match('/^VERSION:/i', $line)) {
                    $versionValue = trim(substr($line, 8));
                    if ($versionValue === '2.1') {
                        $result['parsedData']['version'] = '2';
                    } elseif ($versionValue === '3.0') {
                        $result['parsedData']['version'] = '3';
                    } elseif ($versionValue === '4.0') {
                        $result['parsedData']['version'] = '4';
                    }
                    break;
                }
            }
            
            // Default to v3 if no version found
            if (!isset($result['parsedData']['version'])) {
                $result['parsedData']['version'] = '3';
            }
            
            // Find the N: field
            foreach ($lines as $line) {
                if (preg_match('/^N:/i', $line)) {
                    $nameParts = explode(';', substr($line, 2));
                    if (count($nameParts) >= 2) {
                        $result['parsedData']['lastName'] = trim($nameParts[0]);
                        $result['parsedData']['firstName'] = trim($nameParts[1]);
                    }
                    break;
                }
            }
            
            // Extract formatted name if no name found
            if (empty($result['parsedData']['firstName']) && empty($result['parsedData']['lastName'])) {
                foreach ($lines as $line) {
                    if (preg_match('/^FN:/i', $line)) {
                        $fnValue = trim(substr($line, 3));
                        $parts = explode(' ', $fnValue);
                        if (count($parts) > 1) {
                            $result['parsedData']['firstName'] = $parts[0];
                            $result['parsedData']['lastName'] = implode(' ', array_slice($parts, 1));
                        } else {
                            $result['parsedData']['firstName'] = $fnValue;
                        }
                        break;
                    }
                }
            }
            
            // Extract other fields
            foreach ($lines as $line) {
                if (preg_match('/^ORG:/i', $line)) {
                    $result['parsedData']['org'] = trim(substr($line, 4));
                } elseif (preg_match('/^TITLE:/i', $line)) {
                    $result['parsedData']['position'] = trim(substr($line, 6));
                } elseif (preg_match('/^TEL[^:]*(?:TYPE=WORK|WORK)[^:]*:/i', $line)) {
                    $phoneValue = trim(substr($line, strpos($line, ':') + 1));
                    if (strpos($phoneValue, 'tel:') === 0) {
                        $phoneValue = substr($phoneValue, 4);
                    }
                    $result['parsedData']['phoneWork'] = $phoneValue;
                } elseif (preg_match('/^TEL[^:]*(?:TYPE=HOME|HOME)[^:]*:/i', $line)) {
                    $phoneValue = trim(substr($line, strpos($line, ':') + 1));
                    if (strpos($phoneValue, 'tel:') === 0) {
                        $phoneValue = substr($phoneValue, 4);
                    }
                    $result['parsedData']['phonePrivate'] = $phoneValue;
                } elseif (preg_match('/^TEL[^:]*(?:TYPE=CELL|CELL|TYPE=MOBILE|MOBILE)[^:]*:/i', $line)) {
                    $phoneValue = trim(substr($line, strpos($line, ':') + 1));
                    if (strpos($phoneValue, 'tel:') === 0) {
                        $phoneValue = substr($phoneValue, 4);
                    }
                    $result['parsedData']['phoneMobile'] = $phoneValue;
                } elseif (preg_match('/^TEL[^:]*/i', $line) && 
                          empty($result['parsedData']['phoneWork']) &&
                          empty($result['parsedData']['phonePrivate']) &&
                          empty($result['parsedData']['phoneMobile'])) {
                    $phoneValue = trim(substr($line, strpos($line, ':') + 1));
                    if (strpos($phoneValue, 'tel:') === 0) {
                        $phoneValue = substr($phoneValue, 4);
                    }
                    $result['parsedData']['phoneMobile'] = $phoneValue;
                } elseif (preg_match('/^EMAIL[^:]*:/i', $line)) {
                    $result['parsedData']['email'] = trim(substr($line, strpos($line, ':') + 1));
                } elseif (preg_match('/^URL[^:]*:/i', $line)) {
                    $result['parsedData']['website'] = trim(substr($line, strpos($line, ':') + 1));
                } elseif (preg_match('/^ADR[^:]*:/i', $line)) {
                    $addressParts = explode(';', substr($line, strpos($line, ':') + 1));
                    if (count($addressParts) >= 7) {
                        $result['parsedData']['street'] = trim($addressParts[2]);
                        $result['parsedData']['city'] = trim($addressParts[3]);
                        $result['parsedData']['state'] = trim($addressParts[4]);
                        $result['parsedData']['zipcode'] = trim($addressParts[5]);
                        $result['parsedData']['country'] = trim($addressParts[6]);
                    }
                }
            }
            
            return $result;
        }
        
        // URL detection
        if (preg_match('/^https?:\/\//i', $data)) {
            $result['type'] = 'url';
            $result['parsedData'] = ['url' => $data];
            return $result;
        }
        
        // Email detection
        if (preg_match('/^mailto:/i', $data)) {
            $result['type'] = 'email';
            $result['parsedData'] = [];
            
            $emailParts = explode('?', preg_replace('/^mailto:/i', '', $data));
            $result['parsedData']['address'] = $emailParts[0] ?? '';
            
            if (isset($emailParts[1])) {
                parse_str($emailParts[1], $params);
                $result['parsedData']['subject'] = $params['subject'] ?? '';
                $result['parsedData']['body'] = $params['body'] ?? '';
                $result['parsedData']['cc'] = $params['cc'] ?? '';
                $result['parsedData']['bcc'] = $params['bcc'] ?? '';
            }
            
            return $result;
        }
        
        // Phone detection
        if (preg_match('/^tel:/i', $data)) {
            $result['type'] = 'phone';
            $result['parsedData'] = ['phone' => preg_replace('/^tel:/i', '', $data)];
            return $result;
        }
        
        // SMS detection
        if (preg_match('/^SMSTO:/i', $data) || preg_match('/^sms:/i', $data)) {
            $result['type'] = 'sms';
            $result['parsedData'] = [];
            
            if (strpos($data, 'SMSTO:') === 0) {
                $smsParts = explode(':', preg_replace('/^SMSTO:/i', '', $data));
                $result['parsedData']['phone'] = trim($smsParts[0] ?? '');
                $result['parsedData']['message'] = trim($smsParts[1] ?? '');
            } elseif (strpos(strtolower($data), 'sms:') === 0) {
                $phone = preg_replace('/^sms:/i', '', $data);
                
                if (strpos($phone, '?') !== false) {
                    list($phoneNumber, $queryString) = explode('?', $phone, 2);
                    $result['parsedData']['phone'] = trim($phoneNumber);
                    parse_str($queryString, $params);
                    $result['parsedData']['message'] = $params['body'] ?? '';
                } else {
                    $result['parsedData']['phone'] = trim($phone);
                }
            }
            
            return $result;
        }
        
        // WiFi detection
        if (preg_match('/^WIFI:/i', $data)) {
            $result['type'] = 'wifi';
            $result['parsedData'] = [];
            
            // Extract SSID
            if (preg_match('/S:([^;]*);/i', $data, $matches)) {
                $result['parsedData']['ssid'] = $matches[1] ?? '';
            }
            
            // Extract encryption type
            if (preg_match('/T:([^;]*);/i', $data, $matches)) {
                $encType = strtoupper($matches[1]);
                $result['parsedData']['encryption'] = 
                    ($encType === 'NOPASS' || $encType === 'WEP' || $encType === 'WPA')
                    ? strtolower($encType) : 'nopass';
            } else {
                $result['parsedData']['encryption'] = 'nopass';
            }
            
            // Extract password
            if (preg_match('/P:([^;]*);/i', $data, $matches)) {
                $result['parsedData']['password'] = $matches[1] ?? '';
            }
            
            // Extract hidden flag
            if (preg_match('/H:(true|false);/i', $data, $matches)) {
                $result['parsedData']['hidden'] = strtolower($matches[1]) === 'true';
            } else {
                $result['parsedData']['hidden'] = false;
            }
            
            return $result;
        }
        
        // Location detection
        if (preg_match('/^geo:/i', $data)) {
            $result['type'] = 'location';
            $result['parsedData'] = [];
            
            $coords = explode(',', preg_replace('/^geo:/i', '', $data));
            if (count($coords) >= 2) {
                $result['parsedData']['latitude'] = $coords[0] ?? '';
                $result['parsedData']['longitude'] = $coords[1] ?? '';
            }
            
            return $result;
        }
        
        // Calendar/Event detection
        if (preg_match('/BEGIN:VCALENDAR/i', $data) || preg_match('/BEGIN:VEVENT/i', $data)) {
            $result['type'] = 'event';
            $result['parsedData'] = [];
            
            if (preg_match('/SUMMARY:([^\n\r]*)/i', $data, $matches)) {
                $result['parsedData']['title'] = $matches[1] ?? '';
            }
            
            if (preg_match('/LOCATION:([^\n\r]*)/i', $data, $matches)) {
                $result['parsedData']['location'] = $matches[1] ?? '';
            }
            
            if (preg_match('/DTSTART(?:[^:]*):([^\n\r]*)/i', $data, $matches)) {
                if (!empty($matches[1])) {
                    $result['parsedData']['startTime'] = self::formatDateFromICal($matches[1]);
                }
            }
            
            if (preg_match('/DTEND(?:[^:]*):([^\n\r]*)/i', $data, $matches)) {
                if (!empty($matches[1])) {
                    $result['parsedData']['endTime'] = self::formatDateFromICal($matches[1]);
                }
            }
            
            return $result;
        }
        
        // Default to text
        return $result;
    }

    /**
     * Converts an iCalendar format date to an ISO string
     * 
     * @param string $iCalDate Date in iCalendar format (e.g., "20230101T120000Z")
     * @return string ISO date string, or original string if invalid
     */
    private static function formatDateFromICal(string $iCalDate): string
    {
        // Handle basic format: YYYYMMDDTHHMMSSZ
        if (preg_match('/^(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})(\d{2})Z?$/', $iCalDate, $matches)) {
            try {
                list(, $year, $month, $day, $hour, $minute, $second) = $matches;
                $suffix = (substr($iCalDate, -1) === 'Z') ? 'Z' : '';
                return "{$year}-{$month}-{$day}T{$hour}:{$minute}:{$second}{$suffix}";
            } catch (\Exception $e) {
                error_log('Error parsing iCal date: ' . $e->getMessage());
            }
        }
        
        return $iCalDate; // Return as is if not parseable
    }
}
