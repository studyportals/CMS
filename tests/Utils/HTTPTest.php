<?php declare(strict_types=1);

namespace StudyPortals\Tests\Utils;

use PHPUnit\Framework\TestCase;
use StudyPortals\Utils\HTTP;

class HTTPTest extends TestCase
{

    public function testNormaliseURL(): void
    {

        $tests = [
            '//example.invalid'     => 'http://example.invalid/',
            '//example.invalid.'    => 'http://example.invalid/',
            '//example.invalid:80'  => 'http://example.invalid/',
            '//example.invalid/'    => 'http://example.invalid/',
            'http://example.invalid:80'     => 'http://example.invalid/',
            'http://example.invalid:8080'   => 'http://example.invalid:8080/',
            'https://example.invalid:443'   => 'https://example.invalid/',
            'http://example.invalid:443'    => 'http://example.invalid:443/',
            'https://example.invalid:80'    => 'https://example.invalid:80/',
            'http://example.invalid/foo/./bar.html' => 'http://example.invalid/foo/bar.html',
            'http://example.invalid/foo///bar.html' => 'http://example.invalid/foo/bar.html',
            'http://example.invalid/foo\bar.html'   => 'http://example.invalid/foo/bar.html',
            'http://example.invalid/?'          => 'http://example.invalid/',
            'http://example.invalid/?foo=bar'   => 'http://example.invalid/?foo=bar',
            'http://example.invalid/?foo%3Dbar' => 'http://example.invalid/?foo%3Dbar',
            'http://example.invalid//#foo'      => 'http://example.invalid/#foo'
        ];

        foreach ($tests as $given => $expected) {
            $actual = HTTP::normaliseURL($given);
            $this->assertEquals($expected, $actual);
        }
    }

    public function testParseHeader_Status(): void
    {
        $tests = [
            'HTTP/1.1 500 Internal Server Error' => 500,
            'HTTP/1.1 404 Page Not Found' => 404
        ];

        foreach ($tests as $test => $expected) {
            $status = 0;
            $actual = HTTP::parseHeader([$test], $status);
            $this->assertEquals([], $actual);
            $this->assertEquals($status, $expected);
        }
    }

    public function testParseHeader_Raw(): void
    {
        $tests = [
            [
                'given'     => ['Foo: Bar!'],
                'expected'  => ['Foo' => 'Bar!']
            ],
            [
                'given'     => ['Foo: Bar!', 'Hello: World!'],
                'expected'  => ['Foo' => 'Bar!', 'Hello' => 'World!']
            ]
        ];

        foreach ($tests as $test) {
            $actual = HTTP::parseHeader($test['given']);
            $this->assertEquals($test['expected'], $actual);
        }
    }

    public function testParseHeader_Array(): void
    {
        $tests = [
            [
                'given'     => ['Foo' => 'Bar!'],
                'expected'  => ['Foo' => 'Bar!']
            ],
            [
                'given'     => ['Foo' => 'Bar!', 'Hello' => 'World!'],
                'expected'  => ['Foo' => 'Bar!', 'Hello' => 'World!']
            ],
            [
                'given'     => ['<em>Foo</em>' => '<strong>Bar!</strong>'],
                'expected'  => ['Foo' => 'Bar!']
            ],
            [
                'given'     => ['Foo' => '""'],
                'expected'  => ['Foo' => '']
            ],
            [
                'given'     => [
                    'Chunked-0' => 'Hello',
                    'Chunked-7' => 'World!'
                ],
                'expected'  => ['Chunked' => [0 => 'Hello', 7 => 'World!']]
            ],
        ];

        foreach ($tests as $test) {
            $actual = HTTP::parseHeader($test['given']);
            $this->assertEquals($test['expected'], $actual);
        }
    }

    public function testParseHeader_Array_AcceptLanguage(): void
    {

        /*
         * Yes, the parsing-logic for "Accept-Language" is _absolutely_ b0rked
         * inside HTTP::parseHeader()... The below test validates the current
         * behaviour (which is *wrong*) to at least provide a stable starting
         * point for improvements.
         */

        $tests = [
            [
                'given'     => ['Accept-Language' => 'en-GB'],
                'expected'  => ['Accept-Language' => ['en-GB' => '1.0']]
            ],
            [
                'given'     => ['Accept-Language' => 'en-GB,en;q=0.7,nl;q=0.3'],
                'expected'  => ['Accept-Language' => ['en-GB' => '1.0']]
            ]
        ];

        foreach ($tests as $test) {
            $actual = HTTP::parseHeader($test['given']);
            $this->assertEquals($test['expected'], $actual);
        }
    }

    public function testParseHeader_Array_DateTime(): void
    {
        $tests = [
            [
                'given'     => ['Date' => 'Thu, 23 Jul 2020 14:10:18 GMT'],
                'expected'  => ['Date' => 1595513418]
            ],
            [
                'given'     => ['Expires' => 'Thu, 23 Jul 2020 14:10:18 GMT'],
                'expected'  => ['Expires' => 1595513418]
            ],
            [
                'given'     => [
                    'Last-Modified' => 'Thu, 23 Jul 2020 14:10:18 GMT'
                ],
                'expected'  => ['Last-Modified' => 1595513418]
            ]
        ];

        foreach ($tests as $test) {
            $actual = HTTP::parseHeader($test['given']);
            $this->assertEquals($test['expected'], $actual);
        }
    }

    public function testParseHeader_Array_Directives(): void
    {
        $tests = [
            [
                'given'     => [
                    'Cache-Control' => 'public, max-age=0, s-maxage=300'
                ],
                'expected'  => [
                    'Cache-Control' => [
                        'public' => true,
                        'max-age' => 0,
                        's-maxage' => 300
                    ]
                ]
            ],
            [
                'given'     => [
                    'Keep-Alive' => 'timeout=5, max=1000'
                ],
                'expected'  => [
                    'Keep-Alive' => [
                        'timeout' => 5,
                        'max' => 1000
                    ]
                ]
            ]
        ];

        foreach ($tests as $test) {
            $actual = HTTP::parseHeader($test['given']);
            $this->assertEquals($test['expected'], $actual);
        }
    }

    public function testParseHeaderDirectives(): void
    {
        $tests = [
            'public, max-age=0, s-maxage=300,   foo=bar' => [
                'public' => true,
                'max-age' => 0,
                's-maxage' => 300,
                'foo' => 'bar'
            ]
        ];

        foreach ($tests as $test => $expected) {
            $actual = HTTP::parseHeaderDirectives($test);
            $this->assertEquals($expected, $actual);
        }
    }
}
