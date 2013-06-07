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

    public function testWithKey1()
    {
        $parser = new ForEachParser();

        $result = $parser->parse('foo as bim => bar');

        $this->assertArrayHasKey('array', $result);
        $this->assertEquals('foo', $result['array']);

        $this->assertArrayHasKey('value', $result);
        $this->assertEquals('bar', $result['value']);

        $this->assertArrayHasKey('key', $result);
        $this->assertEquals('bim', $result['key']);
    }

    public function testWithKey2()
    {
        $parser = new ForEachParser();

        $result = $parser->parse('foo as bim=>bar');

        $this->assertArrayHasKey('array', $result);
        $this->assertEquals('foo', $result['array']);

        $this->assertArrayHasKey('value', $result);
        $this->assertEquals('bar', $result['value']);

        $this->assertArrayHasKey('key', $result);
        $this->assertEquals('bim', $result['key']);
    }

    public function testWithKey3()
    {
        $parser = new ForEachParser();

        $result = $parser->parse('  foo.blah[bleh]  as      bim  =>    bar ');

        $this->assertArrayHasKey('array', $result);
        $this->assertEquals('foo.blah[bleh]', $result['array']);

        $this->assertArrayHasKey('value', $result);
        $this->assertEquals('bar', $result['value']);

        $this->assertArrayHasKey('key', $result);
        $this->assertEquals('bim', $result['key']);
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

    /**
     * @expectedException \Xport\SpreadsheetModel\Parser\ParsingException
     */
    public function testInvalidString4()
    {
        $parser = new ForEachParser();
        $parser->parse('foo => bar as test');
    }

    /**
     * @expectedException \Xport\SpreadsheetModel\Parser\ParsingException
     */
    public function testInvalidString5()
    {
        $parser = new ForEachParser();
        $parser->parse('foo as');
    }

    /**
     * @expectedException \Xport\SpreadsheetModel\Parser\ParsingException
     */
    public function testInvalidString6()
    {
        $parser = new ForEachParser();
        $parser->parse('foo as bar =>');
    }

    /**
     * @expectedException \Xport\SpreadsheetModel\Parser\ParsingException
     */
    public function testInvalidString7()
    {
        $parser = new ForEachParser();
        $parser->parse('foo bar');
    }
}
