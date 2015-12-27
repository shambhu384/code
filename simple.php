<?php
/**
 * Example: SimpleXML tell apart a single element and a list of elements with a single element
 *
 * @link https://hakre.wordpress.com/2013/02/12/simplexml-type-cheatsheet/
 * @libk http://stackoverflow.com/a/14829309/367456
 */
$buffer = <<<XML
<root>
    <result>the one childre</result>
</root>
XML;
$xml = simplexml_load_string($buffer);
/**
 * @param SimpleXMLElement $element
 */
function sxml_show_info(SimpleXMLElement $element)
{
    $isSingleElement  = $element[0] == $element;
    $isListOfElements = $element[0] != $element
                        and $element->attributes() !== NULL;
    printf("  Is single-element?   - %s\n", $isSingleElement ? 'Yes' : 'No');
    printf("  Is list-of-elements? - %s\n", $isListOfElements ? 'Yes' : 'No');
}
echo "For the single element:\n";
sxml_show_info($xml->result[0]);
echo "\n";
echo "For the list of elements with a single item:\n";
sxml_show_info($xml->result);
echo "\n";