<?php

namespace XportTest\SpreadsheetModel\Parser;

use Xport\SpreadsheetModel\Parser\ForEachParser;

class ForEachParserTest extends \PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $parser = new ForEachParser();

        $result = $parser->parse('foo as bar');

        $this->assertCount(2, $result);
        
        $this->assertArrayHasKey('array', $result);
        $this->assertEquals('foo', $result['array']);
        
        $this->assertArrayHasKey('value', $result);
        $this->assertEquals('bar', $result['value']);
    }

    public function testWithKey1()
    {
        $parser = new ForEachParser();

        $result = $parser->parse('foo as bim => bar');

        $this->assertCount(3, $result);
        
        $this->assertArrayHasKey('array', $result);
        $this->assertEquals('foo', $result['array']);

        $this->assertArrayHasKey('key', $result);
        $this->assertEquals('bim', $result['key']);
        
        $this->assertArrayHasKey('value', $result);
        $this->assertEquals('bar', $result['value']);
    }

    public function testWithKey2()
    {
        $parser = new ForEachParser();

        $result = $parser->parse('foo as bim=>bar');

        $this->assertCount(3, $result);

        $this->assertArrayHasKey('array', $result);
        $this->assertEquals('foo', $result['array']);

        $this->assertArrayHasKey('key', $result);
        $this->assertEquals('bim', $result['key']);

        $this->assertArrayHasKey('value', $result);
        $this->assertEquals('bar', $result['value']);
    }

    public function testWithKey3()
    {
        $parser = new ForEachParser();

        $result = $parser->parse('  foo.blah[bleh]  as      bim  =>    bar ');

        $this->assertCount(3, $result);

        $this->assertArrayHasKey('array', $result);
        $this->assertEquals('foo.blah[bleh]', $result['array']);

        $this->assertArrayHasKey('key', $result);
        $this->assertEquals('bim', $result['key']);

        $this->assertArrayHasKey('value', $result);
        $this->assertEquals('bar', $result['value']);
    }

    public function testWithFunction1()
    {
        $parser = new ForEachParser();

        $result = $parser->parse('foo as bim => foo(bar)');

        $this->assertCount(3, $result);

        $this->assertArrayHasKey('array', $result);
        $this->assertEquals('foo', $result['array']);

        $this->assertArrayHasKey('key', $result);
        $this->assertEquals('bim', $result['key']);

        $this->assertArrayHasKey('value', $result);
        $this->assertEquals('foo(bar)', $result['value']);
    }

    public function testWithFunction2()
    {
        $parser = new ForEachParser();

        $result = $parser->parse('foo as foo(bim) => foo(bar)');

        $this->assertCount(3, $result);

        $this->assertArrayHasKey('array', $result);
        $this->assertEquals('foo', $result['array']);

        $this->assertArrayHasKey('key', $result);
        $this->assertEquals('foo(bim)', $result['key']);

        $this->assertArrayHasKey('value', $result);
        $this->assertEquals('foo(bar)', $result['value']);
    }

    public function testWithFunction3()
    {
        $parser = new ForEachParser();

        $result = $parser->parse('blah(foo) as bleh(bim) => bleh(bar)');

        $this->assertCount(3, $result);

        $this->assertArrayHasKey('array', $result);
        $this->assertEquals('blah(foo)', $result['array']);

        $this->assertArrayHasKey('key', $result);
        $this->assertEquals('bleh(bim)', $result['key']);

        $this->assertArrayHasKey('value', $result);
        $this->assertEquals('bleh(bar)', $result['value']);
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

    /**
     * @expectedException \Xport\SpreadsheetModel\Parser\ParsingException
     */
    public function testInvalidString8()
    {
        $parser = new ForEachParser();
        $parser->parse('func() as bar');
    }

    /**
     * @expectedException \Xport\SpreadsheetModel\Parser\ParsingException
     */
    public function testInvalidString9()
    {
        $parser = new ForEachParser();
        $parser->parse('(foo) as bar');
    }

    /**
     * @expectedException \Xport\SpreadsheetModel\Parser\ParsingException
     */
    public function testInvalidString10()
    {
        $parser = new ForEachParser();
        $parser->parse('foo as bar()');
    }

    /**
     * @expectedException \Xport\SpreadsheetModel\Parser\ParsingException
     */
    public function testInvalidString11()
    {
        $parser = new ForEachParser();
        $parser->parse('foo as (bar)');
    }

    /**
     * @expectedException \Xport\SpreadsheetModel\Parser\ParsingException
     */
    public function testInvalidString12()
    {
        $parser = new ForEachParser();
        $parser->parse('foo as bar => bam()');
    }

    /**
     * @expectedException \Xport\SpreadsheetModel\Parser\ParsingException
     */
    public function testInvalidString13()
    {
        $parser = new ForEachParser();
        $parser->parse('foo(bar as bam) => bim');
    }

    /**
     * @expectedException \Xport\SpreadsheetModel\Parser\ParsingException
     */
    public function testInvalidString14()
    {
        $parser = new ForEachParser();
        $parser->parse('foo as bar(bam => bim)');
    }
}
