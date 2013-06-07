<?php

namespace XportTest\SpreadsheetModel\Parser;

use Xport\SpreadsheetModel\Parser\ForEachParser;

class ForEachParserTest extends \PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $parser = new ForEachParser();

        $result = $parser->parse('foo as bar');

        $this->assertArrayHasKey('array', $result);
        $this->assertEquals('foo', $result['array']);

        $this->assertArrayHasKey('value', $result);
        $this->assertEquals('bar', $result['value']);
    }

    /**
     * @expectedException \Xport\SpreadsheetModel\Parser\ParsingException
     */
    public function testInvalidString1()
    {
        $parser = new ForEachParser();
        $parser->parse('');
    }

    /**
     * @expectedException \Xport\SpreadsheetModel\Parser\ParsingException
     */
    public function testInvalidString2()
    {
        $parser = new ForEachParser();
        $parser->parse('foobar');
    }

    /**
     * @expectedException \Xport\SpreadsheetModel\Parser\ParsingException
     */
    public function testInvalidString3()
    {
        $parser = new ForEachParser();
        $parser->parse('foo as bar as test');
    }
}
