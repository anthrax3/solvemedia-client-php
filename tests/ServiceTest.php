<?php
namespace DominionEnterprises\SolveMedia;
use Guzzle\Http\Client as GuzzleClient;

/**
 * @coversDefaultClass \DominionEnterprises\SolveMedia\Service
 */
class ServiceTest extends \PHPUnit_Framework_TestCase
{
    private $_realGuzzleClient;

    public function setUp()
    {
        $this->_realGuzzleClient = new GuzzleClient();
    }

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
        new Service($this->_realGuzzleClient, $pubkey, $privkey, $hashkey);
    }

    public function constructWithInvalidArgumentsData()
    {
        return [
            [$this->_realGuzzleClient, null, null, null],
            [$this->_realGuzzleClient, '', null, null],
            [$this->_realGuzzleClient, 0, null, null],
            [$this->_realGuzzleClient, false, null, null],
            [$this->_realGuzzleClient, 'test', null, null],
            [$this->_realGuzzleClient, 'test', '', null],
            [$this->_realGuzzleClient, 'test', 0, null],
            [$this->_realGuzzleClient, 'test', false, null],
        ];
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function constructWithValidArguments()
    {
        $this->assertNotNull(new Service($this->_realGuzzleClient, 'test', 'test'));
        $this->assertNotNull(new Service($this->_realGuzzleClient, 'test', 'test', 'test'));
    }

    /**
     * @test
     * @uses \DominionEnterprises\SolveMedia\Service::__construct
     * @covers ::getHtml
     */
    public function getHtmlDefault()
    {
        $client = new GuzzleClient();
        $pubkey = 'MyTestPubKeyStringToTestFor';
        $service = new Service($this->_realGuzzleClient, $pubkey, 'notest');

        $html = $service->getHtml();
        $this->assertRegExp("/k={$pubkey}/", $html);
        $this->assertNotRegExp('/;error=1/', $html);
        $this->assertRegExp('/' . preg_quote(Service::ADCOPY_API_SERVER, '/') . '/', $html);
    }

    /**
     * @test
     * @uses \DominionEnterprises\SolveMedia\Service::__construct
     * @covers ::getHtml
     */
    public function getHtmlWithArguments()
    {
        $service = new Service($this->_realGuzzleClient, 'notest', 'notest');
        $html = $service->getHtml('test', true);
        $this->assertRegExp('/;error=1/', $html);
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
        $service = new Service($this->_realGuzzleClient, 'notest', 'notest');
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
        $service = new Service($this->_realGuzzleClient, 'notest', 'notest');
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
        $service = new Service($this->_realGuzzleClient, 'notest', 'notest');
        $this->assertNotEmpty($service->getSignupUrl());
    }
}
