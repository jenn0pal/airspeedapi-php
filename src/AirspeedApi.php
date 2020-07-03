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
    protected $base_path;


    public function __construct($config = [])
    {
        $this->config = array_merge([
            'url' => self::CONFIG_URL,
            'username' => self::CONFIG_USERNAME,
            'password' => self::CONFIG_PASSWORD,
            'api_key' => self::CONFIG_API_KEY,
            'token' => self::CONFIG_TOKEN,
        ], $config);

        $this->initHttpClient($this->config['url']);
    }

    public function initHttpClient($url)
    {
        $parsedUrl = parse_url($url);
        $this->client = new Client([
            'base_uri' => $parsedUrl['scheme'] . '://' . $parsedUrl['host'],
        ]);
        $this->base_path = $parsedUrl['path'] ?? '';
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
        $timestamp = date('Ymdhis');
        $this->generateAuthHeader($timestamp);
        $payload['timestamp'] = $timestamp;

        $options['headers'] = $this->headers;
        $options['json'] = $payload;

        $path = $this->base_path . '/' . $endpoint;

        try {
            return $this->client->request($method, $path, $options);
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

        $body = ['data' => $pickup_data];

        return $this->send('POST', 'WaybillRequest', $body);
    }


    /**
     * @param array $quote_data - Refer to documentation for the quote body
     **/
    public function quote(array $quote_data)
    {

        $body = ['data' => $quote_data];

        return $this->send('POST', 'PriceQuote', $body);
    }

    /**
     * @param string $trackingNumber - Tracking Number
     */
    public function waybillStatus($trackingNumber)
    {
        $body = ['trackingNumber' => $trackingNumber];

        return $this->send('POST', 'WaybillStatus', $body);
    }

}
