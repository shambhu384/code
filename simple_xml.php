<?php
/**
 * @link http://stackoverflow.com/questions/28520983/php-simplexml-xpath-does-not-keep-the-namespaces-when-returns-data
 */
/**
 * Class SimpleXpath
 *
 * DOMXpath wrapper for SimpleXMLElement
 *
 * Allows assignment of one DOMXPath instance to the document of a SimpleXMLElement so that all nodes of that
 * SimpleXMLElement have access to it.
 */
class SimpleXpath
{
    /**
     * @var DOMXPath
     */
    private $xpath;
    /**
     * @var SimpleXMLElement
     */
    private $xml;
    /**
     * @param SimpleXMLElement $xml
     *
     * @return SimpleXpath
     */
    public static function of(SimpleXMLElement $xml)
    {
        $self = new self($xml);
        return $self;
    }
    /**
     * @param SimpleXMLElement $xml
     */
    public function __construct(SimpleXMLElement $xml)
    {
        $doc = dom_import_simplexml($xml)->ownerDocument;
        if (!isset($doc->xpath)) {
            $doc->xpath   = new DOMXPath($doc);
            $doc->circref = $doc;
        }
        $this->xpath = $doc->xpath;
        $this->xml   = $xml;
    }
    /**
     * Evaluates the given XPath expression and returns a typed result if possible.
     *
     * @param string  $expression  The XPath expression to execute.
     * @param DOMNode $contextnode [optional] The optional contextnode
     *
     * @return mixed a typed result if possible or a DOMNodeList containing all nodes matching the given XPath expression.
     */
    public function evaluate($expression, $contextnode = null)
    {
        return $this->back($this->xpath->evaluate($expression, $contextnode));
    }
    /**
     * Evaluates the given XPath expression
     *
     * @param string  $expression  The XPath expression to execute.
     * @param DOMNode $contextnode [optional] <The optional contextnode
     *
     * @return array
     */
    public function query($expression, SimpleXMLElement $contextnode = null)
    {
        return $this->back($this->xpath->query($expression, dom_import_simplexml($contextnode)));
    }
    /**
     * back to SimpleXML (if applicable)
     *
     * @param $mixed
     *
     * @return array
     */
    public function back($mixed)
    {
        if (!$mixed instanceof DOMNodeList) {
            return $mixed; // technically not possible with std. SimpleXMLElement
        }
        $result = [];
        $class  = get_class($this->xml);
        foreach ($mixed as $node) {
            $result[] = simplexml_import_dom($node, $class);
        }
        return $result;
    }
    /**
     * Registers the namespace with the DOMXPath object
     *
     * @param string $prefix       The prefix.
     * @param string $namespaceURI The URI of the namespace.
     *
     * @return bool true on success or false on failure.
     */
    public function registerNamespace($prefix, $namespaceURI)
    {
        return $this->xpath->registerNamespace($prefix, $namespaceURI);
    }
    /**
     * Register PHP functions as XPath functions
     *
     * @link http://php.net/manual/en/domxpath.registerphpfunctions.php
     *
     * @param mixed $restrict [optional] Use this parameter to only allow certain functions to be called from XPath.
     *                        This parameter can be either a string (a function name) or an array of function names.
     *
     * @return void
     */
    public function registerPhpFunctions($restrict = null)
    {
        $this->xpath->registerPhpFunctions($restrict);
    }
}
/**
 * Class SimpleXpathXMLElement
 */
class SimpleXpathXMLElement extends SimpleXMLElement
{
    /**
     * Creates a prefix/ns context for the next XPath query
     *
     * @param string $prefix      The namespace prefix to use in the XPath query for the namespace given in ns.
     * @param string $ns          The namespace to use for the XPath query. This must match a namespace in use by the XML
     *                            document or the XPath query using prefix will not return any results.
     *
     * @return bool TRUE on success or FALSE on failure.
     */
    public function registerXPathNamespace($prefix, $ns)
    {
        return SimpleXpath::of($this)->registerNamespace($prefix, $ns);
    }
    /**
     * Runs XPath query on XML data
     *
     * @param string $path An XPath path
     *
     * @return SimpleXMLElement[] an array of SimpleXMLElement objects or FALSE in case of an error.
     */
    public function xpath($path)
    {
        return SimpleXpath::of($this)->query($path, $this);
    }
}
$buffer = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
    <epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
       <response>
          <extension>
             <xyz:form xmlns:xyz="urn:company">
                <xyz:formErrorData>
                   <xyz:field name="field">
                      <xyz:error>REQUIRED</xyz:error>
                      <xyz:value>username</xyz:value>
                   </xyz:field>
                </xyz:formErrorData>
             </xyz:form>
          </extension>
       </response>
    </epp>
XML;
/** @var SimpleXpathXMLElement $xmlObject */
$xmlObject = simplexml_load_string($buffer, 'SimpleXpathXMLElement');
$xmlObject->registerXPathNamespace('ns', 'urn:company');
$fields = $xmlObject->xpath("//ns:field");
foreach ($fields as $field) {
    $errors = $field->xpath("//ns:error"); // no issue
    var_dump((string)current($errors));
}