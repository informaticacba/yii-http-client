<?php

namespace yii\httpclient\tests\unit;

use yii\httpclient\Response;
use yii\httpclient\XmlParser;

class XmlParserTest extends \yii\tests\TestCase
{
    public function testParse()
    {
        $response = new Response();
        $xml = <<<'XML'
<?xml version="1.0" encoding="utf-8"?>
<main>
    <name1>value1</name1>
    <name2>value2</name2>
</main>
XML;
        $response->getBody()->write($xml);

        $data = [
            'name1' => 'value1',
            'name2' => 'value2',
        ];
        $parser = new XmlParser();
        $this->assertEquals($data, $parser->parse($response));
    }

    /**
     * @depends testParse
     */
    public function testParseCData()
    {
        $response = new Response();
        $xml = <<<'XML'
<?xml version="1.0" encoding="utf-8"?>
<main>
    <name1><![CDATA[<tag>]]></name1>
    <name2><![CDATA[value2]]></name2>
</main>
XML;
        $response->getBody()->write($xml);

        $data = [
            'name1' => '<tag>',
            'name2' => 'value2',
        ];
        $parser = new XmlParser();
        $this->assertEquals($data, $parser->parse($response));
    }

    /**
     * @depends testParse
     */
    public function testParseEncoding()
    {
        $response = new Response();
        $xml = <<<'XML'
<?xml version="1.0" encoding="windows-1251"?>
<main>
    <enname>test</enname>
    <rusname>тест</rusname>
</main>
XML;
        $response->getBody()->write($xml);
        $response->addHeader('content-type', 'text/xml; charset=windows-1251');

        $parser = new XmlParser();
        $data = $parser->parse($response);
        $this->assertEquals('test', $data['enname']);
        $this->assertNotEquals('тест', $data['rusname']); // UTF characters should be broken during parsing by 'windows-1251'
    }

    /**
     * @see https://github.com/yiisoft/yii2-httpclient/issues/102
     *
     * @depends testParse
     */
    public function testParseGroupTag()
    {
        $response = new Response();
        $xml = <<<'XML'
<?xml version="1.0" encoding="utf-8"?>
<items>
    <item>
        <id>1</id>
        <name>item1</name>
    </item>
    <item>
        <id>2</id>
        <name>item2</name>
    </item>
</items>
XML;
        $response->getBody()->write($xml);

        $data = [
            'item' => [
                [
                    'id'   => '1',
                    'name' => 'item1',
                ],
                [
                    'id'   => '2',
                    'name' => 'item2',
                ],
            ],
        ];
        $parser = new XmlParser();
        $this->assertEquals($data, $parser->parse($response));
    }
}
