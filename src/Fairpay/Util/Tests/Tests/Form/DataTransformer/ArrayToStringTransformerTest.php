<?php


namespace Fairpay\Util\Tests\Tests\Form\DataTransformer;


use Fairpay\Util\Form\DataTransformer\ArrayToStringTransformer;

class ArrayToStringTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**  @var ArrayToStringTransformer */
    static private $transformer;

    static public function setUpBeforeClass()
    {
        self::$transformer = new ArrayToStringTransformer();
    }

    public function transformProvider()
    {
        return array(
            [null, ''],
            [array(), ''],
            [array('item1', 'item2', 'item3'), 'item1, item2, item3'],
        );
    }

    public function reverseTransformProvider()
    {
        return array(
            ['', array()],
            ['item1', array('item1')],
            ['   item1    ', array('item1')],
            ['item1,item2,item3', array('item1', 'item2', 'item3')],
            ['  item1   , item2   , item3   ', array('item1', 'item2', 'item3')],
        );
    }

    /**
     * @dataProvider transformProvider
     * @param $array
     * @param $expected
     */
    public function testTransform($array, $expected)
    {
        $this->assertEquals($expected, self::$transformer->transform($array));
    }

    /**
     * @dataProvider reverseTransformProvider
     * @param $value
     * @param $expected
     */
    public function testReverseTransform($value, $expected)
    {
        $this->assertEquals($expected, self::$transformer->reverseTransform($value));
    }
}