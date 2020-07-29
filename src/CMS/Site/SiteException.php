<?php declare(strict_types=1);

namespace StudyPortals\CMS\Site;

use AssertionError;
use RuntimeException;
use StudyPortals\Cache\CacheException;
use StudyPortals\CMS\Definitions\IInputHandler;
use StudyPortals\Template\Node;
use Throwable;

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class SiteException extends RuntimeException
{

    /**
     * Display an HTML page with information about the Exception.
     *
     * @param Throwable     $Throwable
     *
     * @param IInputHandler $inputHandler
     *
     * @return string
     */
    public static function displayException(
        Throwable $Throwable,
        IInputHandler $inputHandler
    ): string {
        $muted = false;

        if (
            $Throwable instanceof CacheException
        ) {
            $muted = true;
        }

        $renderPageTitle = self::renderPageTitle($Throwable);
        $renderJavascript = self::renderJavascript();
        $renderStyle = self::renderStyle();
        $renderHeader = self::renderHeader($Throwable);
        $renderException = self::renderException($Throwable);
        $renderPreviousExceptions = self::renderPreviousExceptions($Throwable);
        $renderStackTrace = self::renderStackTrace(
            $Throwable,
            $muted,
            $inputHandler
        );
        $PHP_VERSION = PHP_VERSION;
        $PHP_SAPI = PHP_SAPI;
        $renderServerSoftware = self::renderServerSoftware($inputHandler);
        $date = date('d-m-Y H:i:s');
        return <<<EOT
            <!DOCTYPE HTML public "-//W3C//DTD HTML 4.01//EN"
                "http://www.w3.org/TR/html4/strict.dtd">

            <!--suppress HtmlUnknownTag -->
            <html lang="en">
            <head>
                <title>$renderPageTitle</title>
                $renderJavascript
                $renderStyle
            </head>

            <body>
                $renderHeader
                $renderException
                $renderPreviousExceptions
                $renderStackTrace
                <h2>Environment</h2>

                <table class="ErrorMessage">
                    <tr>
                        <td>PHP:</td><td>$PHP_VERSION ($PHP_SAPI)</td>
                    </tr>
                    $renderServerSoftware
                    <tr>
                    <td>Time:</td>
                    <td>$date</td>
                </tr>
            </table>
            </body>
         </html>
EOT;
    }

    private static function renderPageTitle(Throwable $Throwable): string
    {
        $file = basename($Throwable->getFile());
        $line = $Throwable->getLine();
        if ($Throwable instanceof AssertionError) {
            return "Assertion Failed in $file on line $line";
        }

        $exception_parts = explode(
            '\\',
            get_class(
                $Throwable
            )
        );

        $exceptionClass = end($exception_parts);
        return "$exceptionClass in $file on line $line";
    }

    /**
     * @return string
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private static function renderJavascript(): string
    {
        return <<<EOT
        <script type="text/javascript"
                src="https://ajax.googleapis.com/ajax/libs/mootools/1.4.5/mootools-yui-compressed.js"></script>
        <script type="text/javascript">

            document.addEvent('domready', function(){

                var toggle = function(){

                    var emote = $('Emote');

                    var mouth = emote.get('text').charAt(0);
                    var eyes = emote.get('text').charAt(1);

                    if(eyes === ';'){

                        eyes = ':';
                    }else{

                        eyes = ';';
                    }

                    emote.set('text', mouth + eyes);
                };

                var blink = function(){

                    toggle();
                    toggle.delay(120 + (240 * Math.random()));
                };

                var animate = function(){

                    blink();

                    animate.delay((4200 * Math.random()) + 1200);
                };

                animate.delay(1500);
            });

            document.addEvent('click:relay(span.Arg)', function(event){

                var element = $(event.target).getParent().getElement('code');

                if(element.retrieve('forced')){

                    element.store('forced', false);

                    element.setStyle('display', 'none');
                    element.setStyle('overflow', 'hidden');
                }else{

                    element.store('forced', true);

                    element.setStyle('display', 'block');
                    element.setStyle('overflow', 'scroll');
                }
            });

            var hoverToggle = function(event){

                return function(element){

                    if(event === 'mouseover'){

                        element.setStyle('display', 'block');
                    }else
                        if(event === 'mouseout'){

                            element.setStyle('display', 'none');
                        }
                };
            };

            var hoverOver = hoverToggle('mouseover');
            var hoverOut = hoverToggle('mouseout');

            document.addEvent('mouseover:relay(span.Function)', function(event){

                var element = $(event.target).getParent().getElement('table');

                hoverOver(element);
            });

            document.addEvent('mouseout:relay(span.Function)', function(event){

                var element = $(event.target).getParent().getElement('table');

                hoverOut(element);
            });

            document.addEvent('mouseover:relay(span.Arg)', function(event){

                var element = $(event.target).getParent().getElement('code');

                if(element.retrieve('forced')) return;

                hoverOver(element);
            });

            document.addEvent('mouseout:relay(span.Arg)', function(event){

                var element = $(event.target).getParent().getElement('code');

                if(element.retrieve('forced')) return;

                hoverOut(element);
            });

        </script>
EOT;
    }

    /**
     * @return string
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    private static function renderStyle(): string
    {
        return <<<EOT
        <style type="text/css">
            body {
                font-family: Segoe UI, Arial, sans-serif;
                cursor: default;
            }

            h1 {
                margin-top: 5px;
                font-size: 48px;
            }

            h1 .Emote {
                color: maroon;
                position: relative;
                left: 8px;
                top: 1px;
                font-size: 72px;
                margin-right: 16px;
            }

            h1 em {
                color: graytext;
                font-style: normal;
                font-weight: normal;
            }

            h2 {
                font-size: 20px;
            }

            a {
                text-decoration: none;
            }

            a:hover {
                text-decoration: underline;
                color: blue;
            }

            td {
                vertical-align: top;
            }

            .Hidden {
                display: none;
            }

            span.Boolean {
                font-style: italic;
            }

            .ErrorMessage {
                margin-left: -20px;
                font-size: 12px;
                border-spacing: 20px 5px;
            }

            .ErrorMessage td:nth-child(1),
            .Environment td:nth-child(1) {
                font-weight: bold;
                white-space: nowrap;
            }

            .ErrorMessage ul.ExceptionTree {
                margin-top: 0;
                margin-left: -40px;
                list-style-type: none;
            }

            .ErrorMessage ul.ExceptionTree li {
                float: left;
            }

            .ErrorMessage ul.ExceptionTree li.Parent:after {
                content: "»";
                padding-left: 4px;
                padding-right: 4px;
                font-weight: bold;
                color: graytext;
            }

            .ErrorMessage ul.ExceptionTree li.Exception {
                color: red;
                text-decoration: underline;
                font-weight: bold;
            }

            .ErrorMessage ul.ExceptionTree li.Exception strong {
                color: black;
            }

            .ErrorMessage tr.Message td {
                max-width: 720px;
            }

            ul.TraceItem {
                font-size: 12px;
            }

            ul.TraceItem li.Function {
                float: left;
                list-style-type: circle;
                padding: 0;
                margin-left: -10px;
            }

            ul.TraceItem li.Arg {
                float: left;
                list-style-type: none;
                padding: 0 5px;
            }

            ul.TraceItem .Elem {
                margin-top: 10px;
                margin-left: 10px;
                font-size: 11px;
                padding: 0;
                list-style-type: square;
                display: none;
                color: black;
                position: absolute;
                border: 1px solid black;
                background-color: infobackground;
                border-spacing: 10px 2px;
                max-width: 60%;
                max-height: 40%;
                overflow: hidden;
            }

            ul.TraceItem code.Elem {
                font-family: Lucida Console, monospace;
                margin: 0;
                font-size: 10px;
                padding: 4px 6px;
                white-space: pre;
            }

            ul.TraceItem table td:nth-child(1) {
                color: grey;
            }

            ul.TraceItem li.Arg {
                color: maroon;
            }

            ul.TraceItem li.Arg:after {
                content: ", ";
            }

            ul.TraceItem li.Arg:last-child:after {
                content: none;
            }

            ul.TraceItem li.Arg:hover span.Arg {
                color: red;
                cursor: pointer;
            }

            ul.TraceItem li.Function {
                color: navy;
            }

            ul.TraceItem li.Function:hover span.Function {
                color: blue;
            }

            li.Function:after {
                content: " (";
            }

            ul.TraceItem:after {
                content: " )";
            }
        </style>
EOT;
    }

    private static function renderHeader(Throwable $Throwable): string
    {
        if ($Throwable instanceof AssertionError) {
            return <<<EOT
                <h1>
                    <strong id="Emote" class="Emote">\:</strong>
                    Assertion&middot;Failed
                </h1>
EOT;
        }

        $exception_parts = explode(
            '\\',
            get_class(
                $Throwable
            )
        );
        $base = (string) end($exception_parts);

        // Exception

        // Split name at capital letter boundaries
        $renderName = self::renderName($base);
        return <<<EOT
            <h1>
                <strong id="Emote" class="Emote">):</strong>
                $renderName
            </h1>
EOT;
    }

    private static function renderException(Throwable $Throwable): string
    {
        $file = $Throwable->getFile();
        $line = $Throwable->getLine();
        $renderNonAssertion = self::renderNonAssertion($Throwable);
        $renderMessage = self::renderMessage($Throwable);
        return <<<EOT
        <table class="ErrorMessage">
            <tr>
                <td>Origin:</td>
                <td>
                    $file
                    <strong>&lt;$line&gt;</strong>
                </td>
            </tr>
            $renderNonAssertion
            $renderMessage
        </table>
EOT;
    }

    private static function renderPreviousExceptions(
        Throwable $Throwable
    ): string {
        $Previous = $Throwable->getPrevious();
        if ($Previous === null) {
            return '';
        }

        $renderException = self::renderException($Previous);
        $renderPreviousExceptions = self::renderPreviousExceptions($Previous);
        return <<<EOT
        <h2>Previous Exception</h2>
        $renderException
        $renderPreviousExceptions
EOT;
    }

    private static function renderStackTrace(
        Throwable $Throwable,
        bool $muted,
        IInputHandler $inputHandler
    ): string {
        // Only show stack-trace on local system (IPv4, IPv6, DNS)
        if (
            !$inputHandler->has(INPUT_SERVER, 'REMOTE_ADDR') ||
            !in_array(
                $inputHandler->get(INPUT_SERVER, 'REMOTE_ADDR'),
                [
                    '127.0.0.1',
                    '10.0.2.2',
                    '::1',
                    'localhost',
                ]
            )
        ) {
            return '';
        }

        $renderFunctions = self::renderFunctions(
            $muted,
            array_reverse($Throwable->getTrace())
        );
        return <<<EOT
    <h2>Stack Trace</h2>
    $renderFunctions
EOT;
    }

    private static function renderServerSoftware(
        IInputHandler $inputHandler
    ): string {
        if ($inputHandler->has(INPUT_SERVER, 'SERVER_SOFTWARE')) {
            $software = $inputHandler->get(INPUT_SERVER, 'SERVER_SOFTWARE');
            return <<<EOT
            <tr>
                <td>Server:</td>
                <td>$software</td>
            </tr>
EOT;
        }

        return '';
    }

    private static function renderName(string $base): string
    {
        $base_parts = preg_split(
            '/([A-Z]+[a-z]+)/',
            $base,
            -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );

        if ($base_parts !== false) {
            $final = (string) array_pop($base_parts);

            if (!empty($base_parts)) {
                $implode = implode('&middot;', $base_parts);
                return <<<EOT
                $implode
                <em>&middot;$final</em>
EOT;
            }

            return $final;
        }
        return '';
    }

    /**
     * @param Throwable $Throwable
     *
     * @return string
     */
    private static function renderNonAssertion(Throwable $Throwable): string
    {
        if ($Throwable instanceof AssertionError) {
            return '';
        }

        // Exception Tree
        $exception_tree = self::getExceptionTree($Throwable);

        $output = <<<EOT
        <tr>
            <td>Type:</td>
            <td>
                <ul class="ExceptionTree">
EOT;

        foreach ($exception_tree as $key => $exception) {
            // Exception thrown

            if ($key + 1 === count($exception_tree)) {
                $output .= <<<EOT
                    <li class="Exception">
                        <strong>$exception</strong>
                    </li>
EOT;
                continue;
            }

            // Parent exceptions
            $shortName = self::getShortName($exception);
            $output .= <<<EOT
                <li class="Parent">
                    $shortName
                </li>
EOT;
        }
        $output .= <<<EOT
                </ul>
            </td>
        </tr>
EOT;
        return $output;
    }

    /**
     * @param Throwable $Throwable
     *
     * @return string
     */
    private static function renderMessage(Throwable $Throwable): string
    {
        $message = htmlspecialchars(
            $Throwable->getMessage(),
            ENT_COMPAT | ENT_HTML401,
            'UTF-8'
        );
        return <<<EOT
        <tr class="Message">
            <td>Message:</td>
            <td>$message</td>
        </tr>
EOT;
    }

    /**
     * @param bool                 $muted
     * @param array<array<string>> $trace
     *
     * @return string
     */
    private static function renderFunctions(bool $muted, array $trace): string
    {
        $output = '';

        foreach ($trace as $trace_item) {
            if (strpos($trace_item['function'], '{closure}') !== false) {
                $trace_item['function'] = '{closure}';
            }

            $class = isset($trace_item['class']) ?
                self::getShortName($trace_item['class']) : '';
            $fqn = $trace_item['class'] ?? '';
            $type = $trace_item['type'] ?? '';
            $function = $trace_item['function'] ?? '';
            $file = isset($trace_item['file']) ?
                basename($trace_item['file']) :
                '';
            $dir = isset($trace_item['file']) ?
                dirname($trace_item['file']) :
                '';
            $line = $trace_item['line'] ?? '';

            $renderArguments = self::renderArguments($muted, $trace_item);
            $output .= <<<EOT
            <ul class="TraceItem">
                <li class="Function">
                    <span class="Function">
                        {$class}{$type}{$function}
                    </span>
                    <table class="Elem">
                        <tr>
                            <td>FQN:</td>
                            <td>{$fqn}{$type}{$function}()
                            </td>
                        </tr>
                        <tr>
                            <td>File:</td>
                            <td>$file</td>
                        </tr>
                        <tr>
                            <td>Line:</td>
                            <td>$line</td>
                        </tr>
                        <tr>
                            <td>Directory:</td>
                            <td>$dir</td>
                        </tr>
                    </table>
                </li>
                $renderArguments
            </ul>
            <br class="Hidden">
EOT;
        }
        return $output;
    }

    /**
     * Provide an array of all parent classes for the provided Exception.
     *
     * @param Throwable $Throwable
     *
     * @return array<int, class-string>
     */
    private static function getExceptionTree(Throwable $Throwable): array
    {
        $ex = get_class($Throwable);

        // Build the "tree"

        for ($tree[] = $ex; $ex = get_parent_class($ex); $tree[] = $ex) {
            // Empty statement
        }

        $tree = array_reverse($tree);

        if (count($tree) > 1) {
            array_shift($tree);
        }

        return $tree;
    }

    /**
     * Get the "short" version of a fully-qualified class name.
     *
     * <p>This method compresses the namespace part of a fully-qualified class
     * name to only capital letters (so, "StudyPortals\Framework" becomes
     * "SP\F"). The "actual" class name is left untouched.<br>
     * This creates short, but still readable, class names (including their
     * namespace) for use in places were space is limited (c.q. console errors,
     * file names and lines with many class names).</p>
     *
     * @param string $fqn
     *
     * @return string
     */
    private static function getShortName($fqn): string
    {
        $fqn_parts = explode('\\', $fqn);
        $final = (string) array_pop($fqn_parts);

        if (empty($fqn_parts)) {
            return $final;
        }

        $fqn_caps = preg_replace('/[a-z]+/', '', $fqn_parts);

        if ($fqn_caps === null) {
            return $final;
        }
        return implode('\\', $fqn_caps) . '\\' . $final;
    }

    /**
     * @param bool          $muted
     * @param array<string> $trace_item
     *
     * @return string
     */
    private static function renderArguments(
        bool $muted,
        array $trace_item
    ): string {
        $output = '';

        foreach ((array) $trace_item['args'] as $arg) {
            $arg_boolean = false;
            $arg_black = '';
            $arg_red = '';
            self::setArgSettings(
                $arg,
                $arg_black,
                $arg_red,
                $arg_boolean
            );

            $class = $muted ? '' : 'Arg';

            if ($arg_boolean) {
                $arg_black = "<span class=\"Boolean\">$arg_black</span>";
            }

            $details = '';
            if ($arg_red && !$muted) {
                $details = htmlspecialchars(
                    print_r($arg, true),
                    ENT_COMPAT | ENT_HTML401,
                    'UTF-8'
                );
                $details = "<code class=\"Elem\">$details</code>";
            }

            $output .= <<<EOT
            <li class="Arg">
                <span class="$class">
                    $arg_red
                </span>
                $arg_black
                $details
            </li>
EOT;
        }

        return $output;
    }

    /**
     * @param mixed   $arg
     * @param string  $arg_black
     * @param string  $arg_red
     * @param boolean $arg_boolean
     *
     * @return void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private static function setArgSettings(
        $arg,
        string &$arg_black,
        string &$arg_red,
        bool &$arg_boolean
    ): void {
        // String

        if (is_string($arg)) {
            $arg_black = '[' . strlen($arg) . ']';
            $arg_red = gettype($arg);
            return;
        }

        if (is_array($arg)) {
            // Array
            if (count($arg) > 0) {
                $arg_black = '[' . count($arg) . ']';
                $arg_red = gettype($arg);
                return;
            }

            $arg_black = 'array[0]';
            return;
        }

        if (is_bool($arg)) {
            // Boolean
            $arg_boolean = true;
            $arg_black = ($arg ? 'true' : 'false');
            return;
        }

        if ($arg === null) {
            // NULL
            $arg_boolean = true;
            $arg_black = 'null';
            return;
        }

        if (is_int($arg) || is_float($arg)) {
            // Number
            $arg_black = (string) $arg;
            return;
        }

        if (is_object($arg)) {
            // Object
            $arg_black = '';
            $shortName = self::getShortName(get_class($arg));

            // Treat "Node" classes as a scalar value
            if ($arg instanceof Node) {
                $arg_black = $shortName;
            }

            if ($arg_black === '') {
                $arg_red = $shortName;
            }
            return;
        }

        // Other
        $arg_red = gettype($arg);
    }
}
