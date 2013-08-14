<?php

namespace XportTest\Parser;

use Xport\Parser\ForEachParser;

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

    public function testWithFunction()
    {
        $parser = new ForEachParser();

        $result = $parser->parse('blah(foo) as bim => bar');

        $this->assertCount(3, $result);

        $this->assertArrayHasKey('array', $result);
        $this->assertEquals(['functionName' => 'blah', 'parameters' => ['foo']], $result['array']);

        $this->assertArrayHasKey('key', $result);
        $this->assertEquals('bim', $result['key']);

        $this->assertArrayHasKey('value', $result);
        $this->assertEquals('bar', $result['value']);
    }

    public function testWithFunctionMultipleParameters()
    {
        $parser = new ForEachParser();

        $result = $parser->parse('blah(bim, bar) as foo');

        $this->assertCount(2, $result);

        $this->assertArrayHasKey('array', $result);
        $this->assertEquals(['functionName' => 'blah', 'parameters' => ['bim', 'bar']], $result['array']);

        $this->assertArrayHasKey('value', $result);
        $this->assertEquals('foo', $result['value']);
    }

    public function testWithMethod()
    {
        $parser = new ForEachParser();

        $result = $parser->parse('foo.blah() as bim => bar');

        $this->assertCount(3, $result);

        $this->assertArrayHasKey('array', $result);
        $this->assertEquals('foo.blah()', $result['array']);

        $this->assertArrayHasKey('key', $result);
        $this->assertEquals('bim', $result['key']);

        $this->assertArrayHasKey('value', $result);
        $this->assertEquals('bar', $result['value']);
    }

    /**
     * @expectedException \Xport\Parser\ParsingException
     */
    public function testInvalidString1()
    {
        $parser = new ForEachParser();
        $parser->parse('');
    }

    /**
     * @expectedException \Xport\Parser\ParsingException
     */
    public function testInvalidString2()
    {
        $parser = new ForEachParser();
        $parser->parse('foobar');
    }

    /**
     * @expectedException \Xport\Parser\ParsingException
     */
    public function testInvalidString3()
    {
        $parser = new ForEachParser();
        $parser->parse('foo => bar as test');
    }

    /**
     * @expectedException \Xport\Parser\ParsingException
     */
    public function testInvalidString4()
    {
        $parser = new ForEachParser();
        $parser->parse('foo as');
    }

    /**
     * @expectedException \Xport\Parser\ParsingException
     */
    public function testInvalidString5()
    {
        $parser = new ForEachParser();
        $parser->parse('foo as bar =>');
    }

    /**
     * @expectedException \Xport\Parser\ParsingException
     */
    public function testInvalidString6()
    {
        $parser = new ForEachParser();
        $parser->parse('foo bar');
    }

    /**
     * @expectedException \Xport\Parser\ParsingException
     */
    public function testInvalidString7()
    {
        $parser = new ForEachParser();
        $parser->parse('(foo) as bar');
    }

    /**
     * @expectedException \Xport\Parser\ParsingException
     */
    public function testInvalidString8()
    {
        $parser = new ForEachParser();
        $parser->parse('foo as bar()');
    }

    /**
     * @expectedException \Xport\Parser\ParsingException
     */
    public function testInvalidString9()
    {
        $parser = new ForEachParser();
        $parser->parse('foo as (bar)');
    }

    /**
     * @expectedException \Xport\Parser\ParsingException
     */
    public function testInvalidString10()
    {
        $parser = new ForEachParser();
        $parser->parse('foo as bar => bam()');
    }

    /**
     * @expectedException \Xport\Parser\ParsingException
     */
    public function testInvalidString11()
    {
        $parser = new ForEachParser();
        $parser->parse('foo(bar as bam) => bim');
    }

    /**
     * @expectedException \Xport\Parser\ParsingException
     */
    public function testInvalidString12()
    {
        $parser = new ForEachParser();
        $parser->parse('foo as bar(bam => bim)');
    }
}
