<?php


namespace jenn0pal\Api;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;

class AirspeedApi
{
    const CONFIG_URL = 'API_URL';
    const CONFIG_USERNAME = 'USERNAME';
    const CONFIG_PASSWORD = 'PASSWORD';
    const CONFIG_API_KEY = 'API_KEY';
    const CONFIG_TOKEN = 'TOKEN';


    protected $config;
    protected $headers = [
        'Content-Type' => 'application/json'
    ];
    protected $client;


    public function __construct($config = [])
    {
        $this->config = array_merge([
            'url' => self::CONFIG_URL,
            'username' => self::CONFIG_USERNAME,
            'password' => self::CONFIG_PASSWORD,
            'api_key' => self::CONFIG_API_KEY,
            'token' => self::CONFIG_TOKEN
        ], $config);

        $this->client = new Client([
            'base_uri' => $this->config['url'],
            'verify' => false
        ]);
    }

    public function generateAuthHeader($timestamp)
    {
        $username = $this->config['username'];
        $password = $this->config['password'];
        $api_key = $this->config['api_key'];

        $this->headers['x-PIXSELL-auth-key'] = hash('sha256', $username.$password.$timestamp.$api_key);
    }


    public function send($method, $endpoint, $payload, $options = [])
    {
        $options['headers'] = $this->headers;
        $options['json'] = $payload;

        try {
            return $this->client->request($method, $endpoint, $options);
        }  catch (ClientException $e) {
            echo Psr7\str($e->getRequest());
            return $e->getResponse();
        } catch (RequestException $e) {
            echo Psr7\str($e->getRequest());
            if ($e->hasResponse()) {
                return $e->getResponse();
            }
        }
    }

    /**
     * @param array $pickup_data - Refer to documentation for the pickup body
     **/

    public function pickup(array $pickup_data)
    {
        $timestamp = date('Ymdhis');
        $this->generateAuthHeader($timestamp);
        $body = [
            'timestamp' => $timestamp,
            'data' => $pickup_data
        ];


        return $this->send('POST', '/AirspeedAPI/pixsellWaybillProcessor/WaybillRequest', $body);
    }


    /**
     * @param array $quote_data - Refer to documentation for the quote body
     **/
    public function quote(array $quote_data)
    {
        $timestamp = date('Ymdhis');
        $this->generateAuthHeader($timestamp);
        $body = [
            'timestamp' => $timestamp,
            'data' => $quote_data
        ];

        return $this->send('POST', '/AirspeedAPI/pixsellWaybillProcessor/PriceQuote', $body);
    }

}
