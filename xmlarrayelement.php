<?php
/**
 * Class ArrayXmlElement
 *
 * Use an array definition stored as XML to convert a tree structure from another DOMDocument
 *
 * @author hakre <http://hakre.wordpress.com>
 */
class XmlArrayElement extends SimpleXMLElement
{
    /**
     * setter for named fields on SimpleXMLElements
     *
     * @link https://hakre.wordpress.com/2015/03/27/the-simplexmlelement-magic-wonder-world-in-php/
     *
     * @param string $name  of the field
     * @param mixed  $value to store
     */
    public function setData($name, $value)
    {
        $element              = dom_import_simplexml($this);
        $element->data[$name] = $value;
        $element->circref     = $element;
    }
    /**
     * getter for named fields on SimpleXMLElements
     *
     * @link https://hakre.wordpress.com/2015/03/27/the-simplexmlelement-magic-wonder-world-in-php/
     *
     * @param string $name of the field
     *
     * @return null|mixed retrieved value or null if field is not set
     */
    public function getData($name)
    {
        $element = dom_import_simplexml($this);
        if (!isset($element->data[$name])) {
            return null;
        }
        return $element->data[$name];
    }
    /**
     * assign a document that is processed
     *
     * this also registers all xpath namespaces
     *
     * @param DOMDocument $doc to process
     */
    public function assignDocument(DOMDocument $doc)
    {
        /** @var self $root */
        $root  = $this->xpath('/*')[0];
        $xpath = new DOMXPath($doc);
        foreach ($root->xml->namespace as $namespace) {
            $xpath->registerNamespace($namespace['prefix'], $namespace['uri']);
        }
        $root->setData('xpath', $xpath);
    }
    public function toArray(DOMNode $context = null)
    {
        /** @var self $root */
        $root = $this->xpath('/*')[0];
        if ($root == $this) {
            return $this->array[0]->toArray();
        }
        /** @var DOMXPath $xpath */
        $xpath = $root->getData('xpath');
        $return = [];
        foreach ($this as $child) {
            $result = $evaluated = $xpath->evaluate($child['expr'], $context);
            if ($evaluated instanceof DOMNodeList) {
                if (1 === $evaluated->length) {
                    $result = $child->toArray($evaluated->item(0));
                } else {
                    $result = [];
                    foreach ($evaluated as $node) {
                        $result[] = $child->toArray($node);
                    }
                }
            }
            $name          = (string)($child['alias'] ?: $child->getName());
            $return[$name] = $result;
        }
        // single elements can be casted to string
        if ((1 === count($this)) and ('string' === (string)$child['cast'])) {
            $return = reset($return);
        }
        return $return;
    }
}