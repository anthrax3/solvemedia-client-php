<?php
/**
 * TraderInteractive\SolveMedia\Service class for accessing the Solve Media API service.
 * This component has been heavily modified from it's original form to
 * encapsulate the functionality in a class based structure that is
 * compatible with class autoloading functionality.
 *
 * @author Chris Ryan <christopher.ryan@dominionenterprises.com>
 */
namespace TraderInteractive\SolveMedia;

use Exception;
use GuzzleHttp\ClientInterface;

final class Service
{
    /**
     * The solvemedia server URL's
     */
    const ADCOPY_API_SERVER = 'http://api.solvemedia.com';
    const ADCOPY_API_SECURE_SERVER = 'https://api-secure.solvemedia.com';
    const ADCOPY_VERIFY_SERVER = 'http://verify.solvemedia.com/papi/verify';
    const ADCOPY_SIGNUP = 'http://api.solvemedia.com/public/signup';

    /**
     * @var ClientInterface
     */
    private $_client;

    /**
     * @var string
     */
    private $_pubkey;

    /**
     * @var string
     */
    private $_privkey;

    /**
     * @var string
     */
    private $_hashkey;

    /**
     * Construct a Service object with the required api key values.
     *
     * @param ClientInterface $client The guzzle client to send the requests over.
     * @param string $pubkey A public key for solvemedia
     * @param string $privkey A private key for solvemedia
     * @param string $hashkey An optional hash key for verification
     * @throws Exception
     */
    public function __construct(ClientInterface $client, string $pubkey, string $privkey, string $hashkey = '')
    {
        if (empty($pubkey) || empty($privkey)) {
            throw new Exception('To use solvemedia you must get an API key from ' . self::ADCOPY_SIGNUP);
        }

        $this->_client = $client;
        $this->_pubkey = $pubkey;
        $this->_privkey = $privkey;
        $this->_hashkey = $hashkey;
    }

    /**
     * Gets the challenge HTML (javascript and non-javascript version).
     * This is called from the browser, and the resulting solvemedia HTML widget
     * is embedded within the HTML form it was called from.
     *
     * @param string $error The error given by solvemedia (optional, default is null)
     * @param boolean $useSsl Should the request be made over ssl? (optional, default is false)
     * @return string The HTML to be embedded in the user's form.
     */
    public function getHtml(string $error = null, bool $useSsl = false) : string
    {
        $server = $useSsl ? self::ADCOPY_API_SECURE_SERVER : self::ADCOPY_API_SERVER;
        $errorpart = $error ? ';error=1' : '';

        return <<<EOS
<script type="text/javascript" src="{$server}/papi/challenge.script?k={$this->_pubkey}{$errorpart}"></script>
<noscript>
    <iframe src="{$server}/papi/challenge.noscript?k={$this->_pubkey}{$errorpart}" height="300" width="500" frameborder="0"></iframe><br/>
    <textarea name="adcopy_challenge" rows="3" cols="40"></textarea>
    <input type="hidden" name="adcopy_response" value="manual_challenge"/>
</noscript>
EOS;
    }

    /**
     * Calls an HTTP POST function to verify if the user's guess was correct
     *
     * @param string $remoteip
     * @param string $challenge
     * @param string $response
     * @throws Exception
     * @return Response
     */
    public function checkAnswer(string $remoteip, string $challenge = null, string $response = null) : Response
    {
        if (empty($remoteip)) {
            throw new Exception('For security reasons, you must pass the remote ip to solvemedia');
        }

        //discard spam submissions
        if (empty($challenge) || empty($response)) {
            return new Response(false, 'incorrect-solution');
        }

        $httpResponse = $this->_client->request(
            'POST',
            self::ADCOPY_VERIFY_SERVER,
            [
                'headers' => ['User-Agent' => 'solvemedia/PHP'],
                'form_params' => [
                    'privatekey' => $this->_privkey,
                    'remoteip' => $remoteip,
                    'challenge' => $challenge,
                    'response' => $response,
                ],
            ]
        );

        if ($httpResponse->getStatusCode() !== 200) {
            return new Response(false, $httpResponse->getReasonPhrase());
        }

        $answers = explode("\n", (string)$httpResponse->getBody());

        if (!empty($this->_hashkey)) {
            // validate message authenticator
            $hash = sha1($answers[0] . $challenge . $this->_hashkey);

            if ($hash !== $answers[2]) {
                return new Response(false, 'hash-fail');
            }
        }

        if (trim($answers[0]) !== 'true') {
            return new Response(false, $answers[1]);
        }

        return new Response(true);
    }

    /**
     * Gets a URL where the user can sign up for solvemedia. If your application
     * has a configuration page where you enter a key, you should provide a link
     * using this function.
     *
     * @param string $domain The domain where the page is hosted
     * @param string $appname The name of your application
     * @return string url for signup page
     */
    public function getSignupUrl(string $domain = null, string $appname = null) : string
    {
        return self::ADCOPY_SIGNUP . '?' . http_build_query(['domain' => $domain, 'app' => $appname]);
    }
}
