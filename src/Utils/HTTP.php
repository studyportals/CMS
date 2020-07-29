<?php declare(strict_types=1);

namespace StudyPortals\Utils;

/**
 * @SuppressWarnings(PHPMD)
 * @codeCoverageIgnore
 */
abstract class HTTP
{

    public const OK = 200;
    public const CREATED = 201;
    public const NO_CONTENT = 204;

    public const MULTIPLE_CHOICES = 300;
    public const MOVED_PERMANENTLY = 301;
    public const FOUND = 302;
    public const SEE_OTHER = 303;
    public const NOT_MODIFIED = 304;
    public const TEMPORARY_REDIRECT = 307;

    public const BAD_REQUEST = 400;
    public const UNAUTHORIZED = 401;
    public const FORBIDDEN = 403;
    public const NOT_FOUND = 404;
    public const METHOD_NOT_ALLOWED = 405;
    public const CONFLICT = 409;
    public const GONE = 410;

    public const INTERNAL_SERVER_ERROR = 500;
    public const NOT_IMPLEMENTED = 501;
    public const SERVICE_UNAVAILABLE = 503;

    /**
     * @var array<int, string>
     */
    protected static $messages = [
        self::OK => 'OK',
        self::CREATED => 'Created',
        self::NO_CONTENT => 'No Content',

        self::MULTIPLE_CHOICES => 'Multiple Choices',
        self::MOVED_PERMANENTLY => 'Moved Permanently',
        self::FOUND => 'Found',
        self::SEE_OTHER => 'See Other',
        self::NOT_MODIFIED => 'Not Modified',
        self::TEMPORARY_REDIRECT => 'Temporary Redirect',

        self::BAD_REQUEST => 'Bad Request',
        self::UNAUTHORIZED => 'Unauthorized',
        self::FORBIDDEN => 'Forbidden',
        self::NOT_FOUND => 'Not Found',
        self::METHOD_NOT_ALLOWED => 'Method Not Allowed',
        self::CONFLICT => 'Conflict',
        self::GONE => 'Gone',

        self::INTERNAL_SERVER_ERROR => 'Internal Server Error',
        self::NOT_IMPLEMENTED => 'Not Implemented',
        self::SERVICE_UNAVAILABLE => 'Service Unavailable',
    ];

    /**
     * Set HTTP status-code.
     *
     * Sets a HTTP/1.1 compliant header for the requested {@link $status}
     * and returns a short message (e.g. "Not Found") that goes along with the
     * status-code.
     *
     * @param integer $status
     * @param boolean $suppress
     *
     * @return string
     * @see _messages::$_codes
     * @see HTTP::message()
     */

    public static function status($status, $suppress = false): string
    {

        $message = self::getStatusMessage($status);

        if ($suppress) {
            @header("HTTP/1.1 $status $message");
        } else {
            header("HTTP/1.1 $status $message");
        }

        return $message;
    }

    /**
     * Get the HTTP status-message.
     *
     * @param int $status
     *
     * @return string
     * @see $messages
     */

    public static function getStatusMessage(int $status): string
    {
        return self::$messages[$status] ?? '';
    }

    /**
     * Normalise a URL.
     *
     * This function attempts to normalise a URL as best as possible. This
     * function requires any URL passed to at least contain a host name and
     * an indication of the scheme (HTTP or HTTPS) to be used.
     *
     * The optional, pass-by-reference, parameter {@link $components} is set
     * to the result of the internal call to {@link parse_url()} done by this
     * method (with a little bit of post-processing applied). This information
     * can be used to, for example, retrieve the hostname from the URL.
     *
     *  1. Invalid characters are removed from the URL;
     *  2. If no scheme information is present, "http://" is prepended;
     *  3. If the scheme and port match, the port is removed;
     *  4. If present, the trailing dot is removed from the FQDN hostname;
     *  5. The hostname is made all lowercase;
     *  6. Optional path, query and fragment are appended to the URL;
     *  7. The path component is cleaned of unnecessary and incorrect slashes.
     *
     * @param string        $url
     * @param array<string> $components
     *
     * @return string
     * @see parse_url()
     */

    public static function normaliseURL(
        string $url,
        array &$components = []
    ): string {

        $url = filter_var(trim($url), FILTER_SANITIZE_URL);
        $components = (array) @parse_url((string) $url);

        if (!isset($components['host'])) {
            return '';
        }

        // Scheme

        if (
            !isset($components['scheme']) ||
            !preg_match('/^https?$/i', $components['scheme'])
        ) {
            $components['scheme'] = 'http';

            if (isset($components['port']) && $components['port'] == 443) {
                $components['scheme'] = 'https';
            }
        }

        // Remove default ports

        if (isset($components['port']) && isset($components['scheme'])) {
            if (
                $components['scheme'] === 'http' &&
                $components['port'] === 80
            ) {
                unset($components['port']);
            } elseif (
                $components['scheme'] === 'https' &&
                $components['port'] === 443
            ) {
                unset($components['port']);
            }
        }

        // Remove trailing dot from FQDN

        if (substr($components['host'], -1) === '.') {
            $components['host'] = substr($components['host'], 0, -1);
        }

        // Reconstruct URL

        $normalised =
            strtolower("{$components['scheme']}://{$components['host']}/");

        if (isset($components['port'])) {
            $normalised =
                substr($normalised, 0, -1) . ":{$components['port']}/";
        }

        if (isset($components['path'])) {
            // Clean path

            $components['path'] =
                str_replace(['\\', '/./'], '/', $components['path']);
            $components['path'] =
                preg_replace('/[\/]+/', '/', $components['path']);

            $normalised .= ltrim((string) $components['path'], '/');
        }

        if (isset($components['query'])) {
            $normalised .= "?{$components['query']}";
        }

        if (isset($components['fragment'])) {
            $normalised .= "#{$components['fragment']}";
        }

        return $normalised;
    }

    /**
     * Format string for use as part of of URL-path (e.g. "index").
     *
     * Intelligently reduces the string to contain only lower-case alphanumeric
     * characters and hyphens. The result can safely be used as part of a URL
     * path name.
     *
     * @param string $name
     * @return string
     */

    public static function formatURLPathPart($name): string
    {

        // Ensure that lower case string preserves UTF-8 encoding

        $name = mb_strtolower(trim($name), 'UTF-8');

        // Approximate all non-ASCII characters with their ASCII counterpart

        $name = (string) @iconv('UTF-8', 'ASCII//TRANSLIT', $name);

        // If text starts and ends with a parenthesis, remove these first

        if (
            !empty($name) && strpos($name, '(') === 0 &&
            $name[strlen($name) - 1] === ')'
        ) {
            $name = substr($name, 1, -1);
        }

        // Remove text in parentheses

        $name = preg_replace('/\(.*?\)/', '', (string) $name);

        // Remove all remaining invalid characters

        $name = preg_replace('/[^a-z0-9 \-]+/', '', (string) $name);
        $name = preg_replace('/[ \-]+/', '-', trim((string) $name));

        if (empty($name)) {
            $name = 'blank';
        }

        return $name;
    }

    /**
     * Parse (raw) HTTP-headers into a key-value array.
     *
     * Supports both raw HTTP-headers (passed in as an array of header lines)
     * and key-value based headers (as generated for example by {@link
     * apache_request_headers()}.
     * When raw HTTP-headers are provided the optional, passed-by-reference,
     * {@link $status} argument is set to the HTTP-status code.
     *
     * Even though the HTTP RFC allows "empty" headers it is apparently not a
     * best-practice (as not all clients deal with this properly). So, {@link
     * HTTP::buildHeader()} uses pseudo-empty headers in the form of "" (two
     * double-quotes without content) which are translated back to actual
     * empty strings by this function.
     * This function furthermore supports "chunked" headers (e.g.
     * "X-Something-1", X-Something-2", ...). When chunks are detected they are
     * folded into a single element in the result, containing a key-value pair
     * for each chunk encountered (taking the numeric suffix of the chunk as its
     * array key, so don't make any assumptions about a continuous set of chunk
     * keys!).
     *
     * N.B.: For some (common) header fields additional processing is performed.
     * These fields are interpreted and returned as  different types (e.g. an
     * array or an integer) depending on their content. All other,
     * "unsupported", header fields have their name and value trimmed and
     * sanitised, but are otherwise left untouched.
     * A note to future maintainers: Try not to turn this method into the place
     * where esoteric header-fields go to die. Only add support for common
     * HTTP/1.1 header-fields here; add application specific stuff to your
     * actual application...
     *
     * @param array<string>  $lines
     * @param integer $status
     *
     * @return array<mixed>
     * @see HTTP::buildHeader()
     */

    public static function parseHeader(array $lines, int &$status = 0): array
    {

        $headers = [];

        foreach ($lines as $name => $value) {
            // HTTP status

            if (strpos($value, 'HTTP/1') === 0) {
                $status = (int) explode(' ', (string) $value, 3)[1];

                continue;
            }

            // Transform raw HTTP headers into key-value pairs

            if (is_int($name)) {
                [
                    $name,
                    $value,
                ] = explode(':', trim((string) $value), 2);
            }

            $name = trim((string) $name);
            $name = filter_var(
                $name,
                FILTER_SANITIZE_STRING,
                FILTER_FLAG_NO_ENCODE_QUOTES
            );

            $value = trim((string) $value);
            $value = filter_var(
                $value,
                FILTER_SANITIZE_STRING,
                FILTER_FLAG_NO_ENCODE_QUOTES
            );

            // Deal with pseudo-empty headers

            if ($value === '""') {
                $value = '';
            }

            // Deal with "chunked" headers

            $name_parts = explode('-', (string) $name);
            $index = end($name_parts);

            if (ctype_digit((string) $index)) {
                $index = (int) $index;

                array_pop($name_parts);
                $name = implode('-', $name_parts);
            } else {
                unset($index);
            }

            // Case-insensitive header match for additional processing

            switch (strtolower((string) $name)) {
                // Date(time) elements

                case 'date':
                case 'expires':
                case 'last-modified':
                    $value = strtotime((string) $value);

                    break;

                // Lists of directives

                case 'keep-alive':
                case 'cache-control':
                    $value = self::parseHeaderDirectives((string) $value);

                    break;

                // Accept-Language
                // XXX: [Thijs] This is *very* broken

                case 'accept-language':
                    $locales = [];

                    foreach (explode(',', (string) $value) as $locale) {
                        $locale = trim($locale);

                        if (strpos($locale, '-') !== false) {
                            [
                                $language,
                                $culture,
                            ] = explode('-', $locale, 2);
                            $culture = strtoupper($culture);

                            $locales["{$language}-{$culture}"] = 1.0;
                        }
                    }

                    $value = $locales;

                    break;

                // All other, "unsupported", fields

                default:
                    // NOP

                    break;
            }

            if (isset($index)) {
                assert('!isset($headers[$name][$index])');
                $headers[$name][$index] = $value;
            } else {
                assert('!isset($headers[$name])');
                $headers[$name] = $value;
            }
        }

        return $headers;
    }

    /**
     * @param string $value
     * @return array<mixed>
     */

    public static function parseHeaderDirectives(string $value): array
    {

        $directives = [];

        foreach (explode(',', (string) $value) as $directive) {
            $directive = trim($directive);

            if (strpos($directive, '=') !== false) {
                [
                    $directive_name,
                    $directive_value,
                ] = explode('=', $directive, 2);

                if (ctype_digit($directive_value)) {
                    $directive_value = (int) $directive_value;
                }

                $directives[$directive_name] = $directive_value;
            } else {
                $directives[$directive] = true;
            }
        }

        return $directives;
    }
}
