<?php
/**
 * Class CurlDebug
 *
 * drop-in class to add verbose information to curl requests and to display trouble-shooting information
 *
 * @author hakre <http://hakre.wordpress.com/>
 * @link   http://stackoverflow.com/a/14436877/367456
 */
class CurlDebug
{
    private $handle;
    private $verbose;
    public static function debug($handle)
    {
        return new self($handle);
    }
    public function __construct($handle)
    {
        $this->handle = $handle;
        // CURLOPT_VERBOSE: TRUE to output verbose information. Writes output to STDERR,
        // or the file specified using CURLOPT_STDERR.
        curl_setopt($handle, CURLOPT_VERBOSE, true);
        $verbose = fopen('php://temp', 'rw+');
        curl_setopt($handle, CURLOPT_STDERR, $verbose);
        $this->verbose = $verbose;
    }
    public function result($result)
    {
        if ($result === false) {
            printf("cUrl error (#%d): %s\n", curl_errno($this->handle), curl_error($this->handle));
        }
        return $this;
    }
    public function printVerbose($silent = false)
    {
        if ($silent) {
            return;
        }
        rewind($this->verbose);
        $verboseLog = stream_get_contents($this->verbose);
        echo "Verbose information:\n", $verboseLog, "\n";
    }
}