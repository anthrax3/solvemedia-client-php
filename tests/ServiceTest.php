<?php
namespace DominionEnterprises\SolveMedia;
use Guzzle\Http\Client as GuzzleClient;
use Guzzle\Http\Message\Response as GuzzleResponse;

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
     * @uses \DominionEnterprises\SolveMedia\Response::__construct
     * @uses \DominionEnterprises\SolveMedia\Response::valid
     * @uses \DominionEnterprises\SolveMedia\Response::getMessage
     * @dataProvider checkAnswerErrorResponseData
     * @covers ::checkAnswer
     */
    public function checkAnswerErrorResponse($hashKey, GuzzleResponse $guzzleResponse, $message)
    {
        $guzzleRequest = $this->getMockForAbstractClass('\Guzzle\Http\Message\RequestInterface');
        $guzzleRequest->expects($this->once())->method('send')->will($this->returnValue($guzzleResponse));

        $guzzleClient = $this->getMockForAbstractClass('\Guzzle\Http\ClientInterface');
        $guzzleClient->expects($this->once())->method('post')->will($this->returnValue($guzzleRequest));

        $service = new Service($guzzleClient, 'notest', 'notest', $hashKey);
        $response = $service->checkAnswer('notest', 'foo', 'bar');
        $this->assertFalse($response->valid());
        $this->assertSame($message, $response->getMessage());
    }

    public function checkAnswerErrorResponseData()
    {
        return [
            ['', new GuzzleResponse(400), 'Bad Request'],
            ['', new GuzzleResponse(200, [], "false\nfailure-message"), 'failure-message'],
            ['hashKey', new GuzzleResponse(200, [], "true\nfailure-message\nnot-the-right-hash"), 'hash-fail'],
            ['hashKey', new GuzzleResponse(200, [], "false\nfailure-message\nnot-the-right-hash"), 'hash-fail'],
            ['hashKey', new GuzzleResponse(200, [], "false\nfailure-message\n" . sha1('falsefoohashKey')), 'failure-message'],
        ];
    }

    /**
     * @test
     * @uses \DominionEnterprises\SolveMedia\Service::__construct
     * @uses \DominionEnterprises\SolveMedia\Response::__construct
     * @uses \DominionEnterprises\SolveMedia\Response::valid
     * @dataProvider checkAnswerValidResponseData
     * @covers ::checkAnswer
     */
    public function checkAnswerValidResponse($hashKey, GuzzleResponse $guzzleResponse)
    {
        $guzzleRequest = $this->getMockForAbstractClass('\Guzzle\Http\Message\RequestInterface');
        $guzzleRequest->expects($this->once())->method('send')->will($this->returnValue($guzzleResponse));

        $guzzleClient = $this->getMockForAbstractClass('\Guzzle\Http\ClientInterface');
        $guzzleClient->expects($this->once())->method('post')->will($this->returnValue($guzzleRequest));

        $service = new Service($guzzleClient, 'notest', 'notest', $hashKey);
        $response = $service->checkAnswer('notest', 'foo', 'bar');
        $this->assertTrue($response->valid());
    }

    public function checkAnswerValidResponseData()
    {
        return [
            ['', new GuzzleResponse(200, [], 'true')],
            ['hashKey', new GuzzleResponse(200, [], "true\n\n" . sha1('truefoohashKey'))],
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
