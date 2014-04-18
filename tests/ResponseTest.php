<?php
namespace DominionEnterprises\SolveMedia;

/**
 * @coversDefaultClass \DominionEnterprises\SolveMedia\Response
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @uses \DominionEnterprises\SolveMedia\Response::valid
     * @uses \DominionEnterprises\SolveMedia\Response::getMessage
     * @covers ::__construct
     */
    public function constructNoArguments()
    {
        $response = new Response();

        $this->assertFalse($response->valid());
        $this->assertNull($response->getMessage());
    }

    /**
     * @test
     * @dataProvider constructWithArgumentsData
     * @covers ::__construct
     * @covers ::valid
     * @covers ::getMessage
     */
    public function constructWithArguments($isValid, $error) {
        $response = new Response($isValid, $error);

        $this->assertSame($isValid, $response->valid());
        $this->assertSame($error, $response->getMessage());
    }

    public function constructWithArgumentsData()
    {
        return [
            [true, ''],
            [false, 'error text'],
        ];
    }
}
