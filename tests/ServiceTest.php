<?php
namespace DominionEnterprises\SolveMedia;

/**
 * @coversDefaultClass \DominionEnterprises\SolveMedia\Service
 */
class ServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException Exception
     * @covers ::__construct
     */
    public function constructNoArguments()
    {
        new Service();
    }

    /**
     * @test
     * @dataProvider constructWithInvalidArgumentsData
     * @expectedException Exception
     * @covers ::__construct
     */
    public function constructWithInvalidArguments($pubkey, $privkey, $hashkey)
    {
        new Service($pubkey, $privkey, $hashkey);
    }

    public function constructWithInvalidArgumentsData()
    {
        return [
            [null, null, null],
            ['', null, null],
            [0, null, null],
            [false, null, null],
            ['test', null, null],
            ['test', '', null],
            ['test', 0, null],
            ['test', false, null],
        ];
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function constructWithValidArguments()
    {
        $this->assertNotNull(new Service('test', 'test'));
        $this->assertNotNull(new Service('test', 'test', 'test'));
    }

    /**
     * @test
     * @uses \DominionEnterprises\SolveMedia\Service::__construct
     * @covers ::getHtml
     */
    public function getHtmlDefault()
    {
        $pubkey = 'MyTestPubKeyStringToTestFor';
        $service = new Service($pubkey, 'notest');

        $html = $service->getHtml();
        $this->assertRegExp("/k=$pubkey/", $html);
        $this->assertNotRegExp("/;error=1/", $html);
        $this->assertRegExp('/' . preg_quote(Service::ADCOPY_API_SERVER, '/') . '/', $html);
    }

    /**
     * @test
     * @uses \DominionEnterprises\SolveMedia\Service::__construct
     * @covers ::getHtml
     */
    public function getHtmlWithArguments()
    {
        $service = new Service('notest', 'notest');
        $html = $service->getHtml('test', true);
        $this->assertRegExp("/;error=1/", $html);
        $this->assertRegExp('/' . preg_quote(Service::ADCOPY_API_SECURE_SERVER, '/') . '/', $html);
    }

    /**
     * @test
     * @uses \DominionEnterprises\SolveMedia\Service::__construct
     * @dataProvider checkAnswerNoRemoteIpData
     * @expectedException Exception
     * @covers ::checkAnswer
     */
    public function checkAnswerNoRemoteIp($remoteIp)
    {
        $service = new Service('notest', 'notest');
        $service->checkAnswer($remoteIp, null, null);
    }

    public function checkAnswerNoRemoteIpData()
    {
        return [
            [null],
            [''],
        ];
    }

    /**
     * @test
     * @uses \DominionEnterprises\SolveMedia\Service::__construct
     * @uses \DominionEnterprises\SolveMedia\Response::<public>
     * @dataProvider checkAnswerEmptyArgumentsData
     * @covers ::checkAnswer
     */
    public function checkAnswerEmptyArguments($challenge, $response)
    {
        $service = new Service('notest', 'notest');
        $response = $service->checkAnswer('notest', $challenge, $response);

        $this->assertInstanceOf('\DominionEnterprises\SolveMedia\Response', $response);
        $this->assertFalse($response->valid());
        $this->assertSame('incorrect-solution', $response->getMessage());
    }

    public function checkAnswerEmptyArgumentsData()
    {
        return [
            [null, null],
            ['', null],
            [0, null],
            [false, null],
            ['test', null],
            ['test', ''],
            ['test', 0],
            ['test', false],
        ];
    }

    /**
     * @test
     * @uses \DominionEnterprises\SolveMedia\Service::__construct
     * @covers ::getSignupUrl
     */
    public function getSignupUrl()
    {
        $service = new Service('notest', 'notest');
        $this->assertNotEmpty($service->getSignupUrl());
    }
}
