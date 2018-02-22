<?php
namespace TraderInteractive\SolveMedia;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \TraderInteractive\SolveMedia\Service
 * @covers ::__construct
 */
class ServiceTest extends TestCase
{
    private $_realGuzzleClient;

    /**
     * @test
     * @dataProvider constructWithInvalidArgumentsData
     * @expectedException Exception
     * @covers ::__construct
     */
    public function constructWithInvalidArguments($pubkey, $privkey, $hashkey)
    {
        new Service($this->getGuzzleClient(), $pubkey, $privkey, $hashkey);
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
     * @covers ::getHtml
     */
    public function getHtmlDefault()
    {
        $pubkey = 'MyTestPubKeyStringToTestFor';
        $service = new Service($this->getGuzzleClient(), $pubkey, 'notest');

        $html = $service->getHtml();
        $this->assertRegExp("/k={$pubkey}/", $html);
        $this->assertNotRegExp('/;error=1/', $html);
        $this->assertRegExp('/' . preg_quote(Service::ADCOPY_API_SERVER, '/') . '/', $html);
    }

    /**
     * @test
     * @covers ::getHtml
     */
    public function getHtmlWithArguments()
    {
        $service = new Service($this->getGuzzleClient(), 'notest', 'notest');
        $html = $service->getHtml('test', true);
        $this->assertRegExp('/;error=1/', $html);
        $this->assertRegExp('/' . preg_quote(Service::ADCOPY_API_SECURE_SERVER, '/') . '/', $html);
    }

    /**
     * @test
     * @dataProvider checkAnswerNoRemoteIpData
     * @expectedException Exception
     * @covers ::checkAnswer
     */
    public function checkAnswerNoRemoteIp($remoteIp)
    {
        $service = new Service($this->getGuzzleClient(), 'notest', 'notest');
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
     * @dataProvider checkAnswerEmptyArgumentsData
     * @covers ::checkAnswer
     */
    public function checkAnswerEmptyArguments($challenge, $response)
    {
        $service = new Service($this->getGuzzleClient(), 'notest', 'notest');
        $response = $service->checkAnswer('notest', $challenge, $response);

        $this->assertInstanceOf('\TraderInteractive\SolveMedia\Response', $response);
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
     * @dataProvider checkAnswerErrorResponseData
     * @covers ::checkAnswer
     */
    public function checkAnswerErrorResponse($hashKey, Response $guzzleResponse, $message)
    {
        $service = new Service($this->getGuzzleClient($guzzleResponse), 'notest', 'notest', $hashKey);
        $response = $service->checkAnswer('notest', 'foo', 'bar');
        $this->assertFalse($response->valid());
        $this->assertSame($message, $response->getMessage());
    }

    public function checkAnswerErrorResponseData()
    {
        return [
            ['', new Response(400), 'Bad Request'],
            ['', new Response(200, [], "false\nfailure-message"), 'failure-message'],
            ['hashKey', new Response(200, [], "true\nfailure-message\nnot-the-right-hash"), 'hash-fail'],
            ['hashKey', new Response(200, [], "false\nfailure-message\nnot-the-right-hash"), 'hash-fail'],
            ['hashKey', new Response(200, [], "false\nfailure-message\n" . sha1('falsefoohashKey')), 'failure-message'],
        ];
    }

    /**
     * @test
     * @dataProvider checkAnswerValidResponseData
     * @covers ::checkAnswer
     */
    public function checkAnswerValidResponse($hashKey, Response $guzzleResponse)
    {
        $service = new Service($this->getGuzzleClient($guzzleResponse), 'notest', 'notest', $hashKey);
        $response = $service->checkAnswer('notest', 'foo', 'bar');
        $this->assertTrue($response->valid());
    }

    public function checkAnswerValidResponseData()
    {
        return [
            ['', new Response(200, [], 'true')],
            ['hashKey', new Response(200, [], "true\n\n" . sha1('truefoohashKey'))],
        ];
    }

    /**
     * @test
     * @covers ::getSignupUrl
     */
    public function getSignupUrl()
    {
        $service = new Service($this->getGuzzleClient(), 'notest', 'notest');
        $this->assertNotEmpty($service->getSignupUrl());
    }

    private function getGuzzleClient(Response $response = null) : ClientInterface
    {
        $mock = $this->getMockBuilder(ClientInterface::class)->getMock();
        $mock->method('request')->willReturn($response);
        return $mock;
    }
}
