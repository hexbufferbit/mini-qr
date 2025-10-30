<?php

namespace MiniQR\Tests;

use MiniQR\DataEncoding;
use PHPUnit\Framework\TestCase;

class DataEncodingTest extends TestCase
{
    public function testGenerateTextDataReturnsCorrectString(): void
    {
        $this->assertEquals('Hello World', DataEncoding::generateTextData(['text' => 'Hello World']));
        $this->assertEquals('', DataEncoding::generateTextData(['text' => '']));
    }

    public function testGenerateUrlDataFormatsUrlCorrectly(): void
    {
        $this->assertEquals('https://example.com', DataEncoding::generateUrlData(['url' => 'example.com']));
        $this->assertEquals('http://example.com', DataEncoding::generateUrlData(['url' => 'http://example.com']));
        $this->assertEquals('https://example.com', DataEncoding::generateUrlData(['url' => 'https://example.com']));
        $this->assertEquals('', DataEncoding::generateUrlData(['url' => '']));
    }

    public function testGenerateEmailDataFormatsMailtoStringCorrectly(): void
    {
        $this->assertEquals('mailto:test@example.com', DataEncoding::generateEmailData(['address' => 'test@example.com']));
        $this->assertEquals('mailto:test@example.com?subject=Hi', DataEncoding::generateEmailData(['address' => 'test@example.com', 'subject' => 'Hi']));
        $this->assertEquals('mailto:test@example.com?body=Hello%20there', DataEncoding::generateEmailData(['address' => 'test@example.com', 'body' => 'Hello there']));
        $this->assertEquals('mailto:test@example.com?cc=a%40test.com&bcc=b%40test.com', DataEncoding::generateEmailData(['address' => 'test@example.com', 'cc' => 'a@test.com', 'bcc' => 'b@test.com']));
        $this->assertEquals('mailto:test@example.com?subject=Hi%20%26%20Bye&body=Line%201%0ALine%202', DataEncoding::generateEmailData(['address' => 'test@example.com', 'subject' => 'Hi & Bye', 'body' => "Line 1\nLine 2"]));
        $this->assertEquals('mailto:test@example.com?subject=Hello&cc=a%40test.com', DataEncoding::generateEmailData(['address' => 'test@example.com', 'subject' => 'Hello', 'cc' => 'a@test.com']));
        $this->assertEquals('', DataEncoding::generateEmailData(['address' => '']));
    }

    public function testGenerateEmailDataHandlesMultipleEmailsInCcBccCorrectly(): void
    {
        $this->assertEquals(
            'mailto:test@example.com?cc=cc1%40test.com%2Ccc2%40test.com&bcc=bcc1%40test.com%2Cbcc2%40test.com',
            DataEncoding::generateEmailData([
                'address' => 'test@example.com',
                'cc' => 'cc1@test.com,cc2@test.com',
                'bcc' => 'bcc1@test.com,bcc2@test.com'
            ])
        );
    }

    public function testGeneratePhoneDataFormatsTelStringCorrectly(): void
    {
        $this->assertEquals('tel:+123456789', DataEncoding::generatePhoneData(['phone' => '+123456789']));
        $this->assertEquals('', DataEncoding::generatePhoneData(['phone' => '']));
    }

    public function testGenerateSmsDataFormatsSmstoStringCorrectly(): void
    {
        $this->assertEquals('SMSTO:+12345:', DataEncoding::generateSmsData(['phone' => '+12345']));
        $this->assertEquals('SMSTO:+12345:Hello', DataEncoding::generateSmsData(['phone' => '+12345', 'message' => 'Hello']));
        $this->assertEquals('', DataEncoding::generateSmsData(['phone' => '']));
    }

    public function testGenerateWifiDataFormatsWifiStringCorrectly(): void
    {
        $this->assertEquals('WIFI:T:WPA;S:MyNet;P:pass123;;', DataEncoding::generateWifiData(['ssid' => 'MyNet', 'encryption' => 'WPA', 'password' => 'pass123']));
        $this->assertEquals('WIFI:T:WPA;S:MyNet;P:pass\\;123\\";;', DataEncoding::generateWifiData(['ssid' => 'MyNet', 'encryption' => 'WPA', 'password' => 'pass;123"']));
        $this->assertEquals('WIFI:T:nopass;S:MyNet;;;', DataEncoding::generateWifiData(['ssid' => 'MyNet', 'encryption' => 'nopass']));
        $this->assertEquals('WIFI:T:WPA;S:HiddenNet;P:abc;H:true;;', DataEncoding::generateWifiData(['ssid' => 'HiddenNet', 'encryption' => 'WPA', 'password' => 'abc', 'hidden' => true]));
        $this->assertEquals('', DataEncoding::generateWifiData(['ssid' => '', 'encryption' => 'WPA']));
    }

    public function testGenerateVCardDataFormatsVCardStringCorrectly(): void
    {
        $this->assertEquals(
            "BEGIN:VCARD\nVERSION:3.0\nN:Doe;John;;;\nFN:John Doe\nEND:VCARD",
            DataEncoding::generateVCardData(['firstName' => 'John', 'lastName' => 'Doe'])
        );
        $this->assertEquals(
            "BEGIN:VCARD\nVERSION:3.0\nEMAIL:j.doe@example.com\nEND:VCARD",
            DataEncoding::generateVCardData(['email' => 'j.doe@example.com'])
        );
    }

    public function testGenerateVCardDataWithVersion2(): void
    {
        $result = DataEncoding::generateVCardData([
            'firstName' => 'John',
            'lastName' => 'Doe',
            'phoneWork' => '+1234567890',
            'email' => 'john@example.com',
            'version' => '2'
        ]);
        
        $this->assertStringContainsString('VERSION:2.1', $result);
        $this->assertStringContainsString('TEL;WORK;VOICE:+1234567890', $result);
        $this->assertStringContainsString('EMAIL;INTERNET:john@example.com', $result);
    }

    public function testGenerateVCardDataWithVersion4(): void
    {
        $result = DataEncoding::generateVCardData([
            'firstName' => 'Jane',
            'lastName' => 'Smith',
            'phoneMobile' => '+9876543210',
            'website' => 'https://example.com',
            'version' => '4'
        ]);
        
        $this->assertStringContainsString('VERSION:4.0', $result);
        $this->assertStringContainsString('TEL;TYPE=cell,voice;VALUE=uri:tel:+9876543210', $result);
        $this->assertStringContainsString('URL;TYPE=work:https://example.com', $result);
    }

    public function testGenerateVCardDataWithAddress(): void
    {
        $result = DataEncoding::generateVCardData([
            'firstName' => 'Bob',
            'lastName' => 'Jones',
            'street' => '123 Main St',
            'city' => 'Springfield',
            'state' => 'IL',
            'zipcode' => '62701',
            'country' => 'USA'
        ]);
        
        $this->assertStringContainsString('ADR;TYPE=WORK:;;123 Main St;Springfield;IL;62701;USA', $result);
    }

    public function testGenerateLocationDataFormatsGeoUriCorrectly(): void
    {
        $this->assertEquals('geo:37.7749,-122.4194', DataEncoding::generateLocationData(['latitude' => 37.7749, 'longitude' => -122.4194]));
        $this->assertEquals('geo:37.7749,-122.4194', DataEncoding::generateLocationData(['latitude' => '37.7749', 'longitude' => '-122.4194']));
        $this->assertEquals('', DataEncoding::generateLocationData(['latitude' => 'invalid', 'longitude' => 'invalid']));
    }

    public function testGenerateEventDataFormatsIcalendarStringCorrectly(): void
    {
        $result = DataEncoding::generateEventData([
            'title' => 'Team Meeting',
            'location' => 'Conference Room',
            'startTime' => '2024-01-15T10:00:00Z',
            'endTime' => '2024-01-15T11:00:00Z'
        ]);
        
        $this->assertStringContainsString('BEGIN:VCALENDAR', $result);
        $this->assertStringContainsString('VERSION:2.0', $result);
        $this->assertStringContainsString('BEGIN:VEVENT', $result);
        $this->assertStringContainsString('SUMMARY:Team Meeting', $result);
        $this->assertStringContainsString('LOCATION:Conference Room', $result);
        $this->assertStringContainsString('DTSTART:20240115T100000Z', $result);
        $this->assertStringContainsString('DTEND:20240115T110000Z', $result);
        $this->assertStringContainsString('END:VEVENT', $result);
        $this->assertStringContainsString('END:VCALENDAR', $result);
    }

    public function testDetectDataTypeDetectsText(): void
    {
        $result = DataEncoding::detectDataType('Hello World');
        $this->assertEquals('text', $result['type']);
        $this->assertEquals('Hello World', $result['parsedData']['text']);
    }

    public function testDetectDataTypeDetectsUrl(): void
    {
        $result = DataEncoding::detectDataType('https://example.com');
        $this->assertEquals('url', $result['type']);
        $this->assertEquals('https://example.com', $result['parsedData']['url']);
        
        $result = DataEncoding::detectDataType('http://example.com');
        $this->assertEquals('url', $result['type']);
        $this->assertEquals('http://example.com', $result['parsedData']['url']);
    }

    public function testDetectDataTypeDetectsEmail(): void
    {
        $result = DataEncoding::detectDataType('mailto:test@example.com?subject=Hello&body=Test');
        $this->assertEquals('email', $result['type']);
        $this->assertEquals('test@example.com', $result['parsedData']['address']);
        $this->assertEquals('Hello', $result['parsedData']['subject']);
        $this->assertEquals('Test', $result['parsedData']['body']);
    }

    public function testDetectDataTypeDetectsPhone(): void
    {
        $result = DataEncoding::detectDataType('tel:+123456789');
        $this->assertEquals('phone', $result['type']);
        $this->assertEquals('+123456789', $result['parsedData']['phone']);
    }

    public function testDetectDataTypeDetectsSms(): void
    {
        $result = DataEncoding::detectDataType('SMSTO:+12345:Hello');
        $this->assertEquals('sms', $result['type']);
        $this->assertEquals('+12345', $result['parsedData']['phone']);
        $this->assertEquals('Hello', $result['parsedData']['message']);
    }

    public function testDetectDataTypeDetectsWifi(): void
    {
        $result = DataEncoding::detectDataType('WIFI:T:WPA;S:MyNetwork;P:password123;H:true;');
        $this->assertEquals('wifi', $result['type']);
        $this->assertEquals('MyNetwork', $result['parsedData']['ssid']);
        $this->assertEquals('wpa', $result['parsedData']['encryption']);
        $this->assertEquals('password123', $result['parsedData']['password']);
        $this->assertTrue($result['parsedData']['hidden']);
    }

    public function testDetectDataTypeDetectsVCard(): void
    {
        $vcard = "BEGIN:VCARD\nVERSION:3.0\nN:Doe;John;;;\nFN:John Doe\nTEL;TYPE=WORK,VOICE:+1234567890\nEMAIL:john@example.com\nEND:VCARD";
        $result = DataEncoding::detectDataType($vcard);
        
        $this->assertEquals('vcard', $result['type']);
        $this->assertEquals('John', $result['parsedData']['firstName']);
        $this->assertEquals('Doe', $result['parsedData']['lastName']);
        $this->assertEquals('+1234567890', $result['parsedData']['phoneWork']);
        $this->assertEquals('john@example.com', $result['parsedData']['email']);
        $this->assertEquals('3', $result['parsedData']['version']);
    }

    public function testDetectDataTypeDetectsLocation(): void
    {
        $result = DataEncoding::detectDataType('geo:37.7749,-122.4194');
        $this->assertEquals('location', $result['type']);
        $this->assertEquals('37.7749', $result['parsedData']['latitude']);
        $this->assertEquals('-122.4194', $result['parsedData']['longitude']);
    }

    public function testDetectDataTypeDetectsEvent(): void
    {
        $ical = "BEGIN:VCALENDAR\nVERSION:2.0\nBEGIN:VEVENT\nSUMMARY:Meeting\nLOCATION:Office\nDTSTART:20240115T100000Z\nDTEND:20240115T110000Z\nEND:VEVENT\nEND:VCALENDAR";
        $result = DataEncoding::detectDataType($ical);
        
        $this->assertEquals('event', $result['type']);
        $this->assertEquals('Meeting', $result['parsedData']['title']);
        $this->assertEquals('Office', $result['parsedData']['location']);
        $this->assertEquals('2024-01-15T10:00:00Z', $result['parsedData']['startTime']);
        $this->assertEquals('2024-01-15T11:00:00Z', $result['parsedData']['endTime']);
    }

    public function testEscapeVCardEscapesSpecialCharacters(): void
    {
        $this->assertEquals('test\\;value', DataEncoding::escapeVCard('test;value'));
        $this->assertEquals('test\\,value', DataEncoding::escapeVCard('test,value'));
        $this->assertEquals('test\\\\value', DataEncoding::escapeVCard('test\\value'));
    }

    public function testEscapeWiFiEscapesSpecialCharacters(): void
    {
        $this->assertEquals('test\\;value', DataEncoding::escapeWiFi('test;value'));
        $this->assertEquals('test\\,value', DataEncoding::escapeWiFi('test,value'));
        $this->assertEquals('test\\"value', DataEncoding::escapeWiFi('test"value'));
        $this->assertEquals('test\\\'value', DataEncoding::escapeWiFi('test\'value'));
    }

    public function testEscapeICalEscapesSpecialCharacters(): void
    {
        $this->assertEquals('test\\;value', DataEncoding::escapeICal('test;value'));
        $this->assertEquals('test\\,value', DataEncoding::escapeICal('test,value'));
        $this->assertEquals('test\\\\value', DataEncoding::escapeICal('test\\value'));
    }
}
