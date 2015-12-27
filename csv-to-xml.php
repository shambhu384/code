<?php
/*
 * Converting a massive CSV to a specific XML format
 *
 * @link http://stackoverflow.com/a/32034227/367456
 */
/**
 * Class XmlEncoder
 *
 * @author hakre <http://hakre.wordpress.com/>
 */
class XmlEncoder
{
    /**
     * @var string
     */
    private $path;
    /**
     * @var Traversable
     */
    private $records;
    /**
     * XmlEncoder constructor.
     *
     * @param $path
     */
    public function __construct($path, Traversable $records)
    {
        $this->path = $path;
        $this->records = $records;
    }
    public function encode()
    {
        $writer = new XMLWriter();
        $writer->openURI('php://stdout');
        $writer->setIndent(true);
        $writer->startDocument();
        $writer->startElement('Module');
        foreach($this->records as $record) {
            // filter out unexpected data from encoding
            if (
                !is_array($record)
                or array_keys($record) !== ['country', 'handling', 'ceiling', 'rate']
            ) {
                continue;
            }
            $writer->startElement('Method_Add');
            $writer->writeElement('Method', $record['country']);
            $writer->writeElement('Handling', $record['handling']);
            $writer->startElement('Range');
            $writer->writeElement('Ceiling', $record['ceiling']);
            $writer->writeElement('Rate', $record['rate']);
            $writer->endElement();
            $writer->endElement();
        }
        $writer->endElement();
        $writer->endDocument();
    }
}
/**
 * @author hakre <http://hakre.wordpress.com>
 */
class CsvParser implements IteratorAggregate
{
    private $path;
    private $headers;
    /**
     * CsvParser constructor.
     *
     * @param $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }
    /**
     * @return Generator
     */
    public function getIterator()
    {
        $file = new SplFileObject($this->path);
        $file->setFlags(SplFileObject::READ_CSV);
        $file->rewind();
        $records = new NoRewindIterator($file);
        $this->headers = $records->current();
        $records->next();
        foreach ($records as $line => $record) {
            $count = count($record);
            // logical check, also filters empty lines at the end of file
            if (3 > $count) {
                continue;
            }
            if ($count != count($this->headers)) {
                throw new UnexpectedValueException(
                    sprintf('Count %d of record #%d is inconsistent with headers', $count, $line)
                );
            }
            foreach (range(2, $count - 1) as $i) {
                $result = [
                    'country' =>  $this->headers[$i],
                    'handling' => '0.00',
                    'ceiling' => $record[0],
                    'rate' => $record[$i]
                ];
                yield $result;
            }
        }
    }
}
$parser = new CsvParser('php://stdin');
$encoder = new XmlEncoder('php://stdout', $parser);
$encoder->encode();