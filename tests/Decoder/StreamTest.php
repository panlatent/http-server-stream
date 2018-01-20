<?php

namespace Decoder;

use Aurora\Http\Message\Decoder\MessageBodyException;
use Aurora\Http\Message\Decoder\Stream;
use PHPUnit\Framework\TestCase;

class StreamTest extends TestCase
{
    public function testWriteViaGet()
    {
        $stream = $this->getStream();
        $this->assertEquals(Stream::MSG_HEAD_DOING, $stream->getMessageStatus());
        $this->assertAttributeNotEmpty('lineBuffer', $stream);
        $this->assertAttributeNotEmpty('headerBuffer', $stream);
        $this->assertAttributeEmpty('bodyBuffer', $stream);
    }

    public function testGetMethod()
    {
        $stream = $this->getStream();
        $this->assertEquals('GET', $stream->getMethod());
    }

    public function testGetUri()
    {
        $stream = $this->getStream();
        $this->assertEquals('/', $stream->getUri());
    }

    public function testGetVersion()
    {
        $stream = $this->getStream();
        $this->assertEquals('HTTP/1.1', $stream->getVersion());
    }

    public function testGetHeaders()
    {
        $stream = $this->getStream();
        $headers = $stream->getHeaders();
        $this->assertCount(9, $headers);
    }

    public function testGetStandardHeaders()
    {
        $stream = $this->getStream();
        $headers = $stream->getStandardHeaders();
        $this->assertArrayHasKey('Host', $headers);
        $this->assertEquals('www.laruence.com', $headers['Host']);
    }

    public function testGetBodyContent()
    {
        $stream = new Stream();
        $this->assertEquals('', $stream->getBodyContent());
    }

    public function testGetBodyStreamContent()
    {
        $this->expectException(MessageBodyException::class);
        $stream = new Stream();
        $this->assertEquals('', $stream->getBodyStream());
    }

    /**
     * @return Stream
     */
    private function getStream()
    {
        $stream = new Stream();
        $fp = fopen(__DIR__ . '/../_data/request_get.http', 'r');
        for (; ! feof($fp);) {
            $part = fread($fp, 30);
            $length = 0;
            for (; $length != strlen($part);) {
                $length += $successLength = $stream->write(substr($part, $length));

            }
        }

        return $stream;
    }
}