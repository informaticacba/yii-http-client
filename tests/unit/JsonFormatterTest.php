<?php

namespace yii\httpclient\tests\unit;

use yii\helpers\Json;
use yii\httpclient\JsonFormatter;
use yii\httpclient\Request;

class JsonFormatterTest extends \yii\tests\TestCase
{
    public function testFormat()
    {
        $request = new Request();
        $data = [
            'name1' => 'value1',
            'name2' => 'value2',
        ];
        $request->setParams($data);

        $formatter = new JsonFormatter();
        $formatter->format($request);
        $this->assertEquals(Json::encode($data), $request->getBody()->__toString());
        $this->assertEquals('application/json; charset=UTF-8', $request->getHeaderLine('Content-Type'));
    }

    /**
     * @depends testFormat
     */
    public function testFormatEmpty()
    {
        $request = new Request();

        $formatter = new JsonFormatter();
        $formatter->format($request);
        $this->assertFalse($request->hasBody());
    }
}
