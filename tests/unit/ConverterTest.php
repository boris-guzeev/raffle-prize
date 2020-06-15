<?php
use \app\models\Converter;
use app\tests\fixtures\MoneyPrizeFixture;

class ConverterTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function _fixtures()
    {
        return [
            'profiles' => [
                'class' => MoneyPrizeFixture::class,
            ]
        ];
    }

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testConvertToPoints()
    {
        
    }
}