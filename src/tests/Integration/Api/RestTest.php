<?php

namespace PhpTest\tests\Integration\Api;

use PHPUnit\Framework\TestCase;

class RestTest extends TestCase
{
    public function testApiMustReturnAuctionsArray(): void
    {
        $answer = file_get_contents("http://localhost:8080/php-test/rest.php");

        self::assertStringContainsString("200 OK", $http_response_header[0]);
        self::assertIsArray(json_decode($answer));
    }
}