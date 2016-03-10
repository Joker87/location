<?php
namespace LocationBundle\Component;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class CurlClient
 * @package LocationBundle
 */
class CurlClient
{

    const DEFAULT_ENCODING = 'UTF-8';
    const
        BUILD_QUERY = 'q',
        BUILD_JSON = 'j'
    ;

    /** @var string */
    protected $url;

    /** @var \resource */
    protected $curl;

    /** @var string */
    protected $method;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * @var string
     */
    public $api;

    /**
     * @var bool
     */
    public $responseLoggingDisabled = false;

    /**
     * @var bool
     */
    public $useJson = false;

    /**
     * @var string
     */
    public $lastRequest;

    /**
     * @var string
     */
    public $lastRequestHeaders;

    /**
     * @var string
     */
    public $lastResponse;

    /**
     * @var string
     */
    public $lastResponseHeaders;

    /**
     * @var array
     */
    private $optHeaders = array();

    /**
     * @var string
     */
    private $encoding = self::DEFAULT_ENCODING;

    /**
     * @param LoggerInterface $logger
     * @param string $url
     * @param string $method
     * @param array $options
     */
    public function __construct(LoggerInterface $logger, $options = array(), $method = 'post', $url = '')
    {
        $this->logger = $logger;
        $this->curl = curl_init();
        $this->method = strtolower($method);

        if (!is_array($options)) {
            $options = array();
        }
        foreach ($options as $key => $value) {
            if (is_null($value)) {
                unset($options[$key]);
            }
        }
        $options += [
            CURLOPT_CONNECTTIMEOUT => 60,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_POST => ($this->method == 'post'),
            CURLOPT_FAILONERROR => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_VERBOSE => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLINFO_HEADER_OUT => true,
        ];
        if (!in_array($method, ['get', 'post'])) {
            $options[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
        }

        $this->setUrl($url);

        curl_setopt_array($this->curl, $options);
    }

    /**
     * Добавляет к исходящим заголовкам новый
     *
     * @param string $header
     */
    public function addHeader($header)
    {
        $this->optHeaders[] = $header;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
        $options = array(CURLOPT_URL => $url);

        if (0 === strpos($url, 'https')) {
            $options += array(
                CURLOPT_SSLVERSION => 3,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
            );
        }

        curl_setopt_array($this->curl, $options);
    }

    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * Close the opened resource
     */
    public function __destruct()
    {
        if ($this->curl) {
            curl_close($this->curl);
        }
    }

    /**
     * @param array $data
     * @param bool $build
     * @param string $buildMethod
     * @return string
     * @throws HttpException
     * @throws \Exception
     */
    public function exec(array $data = array(), $build = true, $buildMethod = self::BUILD_QUERY)
    {
        $this->lastRequest = null;
        $this->lastRequestHeaders = null;
        $requestBodyForLog = null;
        if ($this->method == 'get') {
            if (!$build) {
                curl_setopt($this->curl, CURLOPT_URL, $this->url);
            } else {
                curl_setopt($this->curl, CURLOPT_URL, $this->url . '?' . http_build_query($data));
            }
        } else {
            if (!$build) {
                $requestBodyForLog = $this->lastRequest = array_shift($data);
            } else {
                $this->lastRequest = $buildMethod === self::BUILD_JSON ? json_encode($data) : http_build_query($data);
                $requestBodyForLog = $this->lastRequest;
            }
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->lastRequest);
        }

        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->optHeaders);

        $created = microtime(true);
        $response = curl_exec($this->curl);

        $headers = trim(curl_getinfo($this->curl, CURLINFO_HEADER_OUT));
        if (false === $this->lastResponse) {
            if (!$headers) {
                $headers = 'url: ' . $this->url;
            }

            $this->logger->error(
                "CURL-Error:\n\n" . $headers . "\n\n" . curl_error($this->curl) . ':' . curl_errno($this->curl),
                [
                    'api' => $this->api,
                    'time' => $created,
                ]
            );
        } else {
            $this->logger->info(
                "CURL-Request:\n\n" . $headers . "\n\n" . $requestBodyForLog . "\n",
                [
                    'api' => $this->api,
                    'time' => $created,
                ]
            );

            if (!$this->responseLoggingDisabled) {
                $responseDebug = $response;
                if (self::DEFAULT_ENCODING !== $this->encoding) {
                    try {
                        $responseDebug = iconv($this->encoding, self::DEFAULT_ENCODING, $responseDebug);
                    } catch (\Exception $e) {
                        $this->logger->error($e->getMessage(), ['api' => $this->api]);
                    }
                }

                $interval = number_format(microtime(true) - $created, 4, '.', '');
                $this->logger->info(
                    "CURL-Response [{$interval}]:\n\n" . $responseDebug,
                    [
                        'api' => $this->api,
                    ]
                );
            }
        }

        $this->lastRequestHeaders = $headers;
        $this->lastResponseHeaders = substr($response, 0, curl_getinfo($this->curl, CURLINFO_HEADER_SIZE));
        $this->lastResponse = substr($response, curl_getinfo($this->curl, CURLINFO_HEADER_SIZE));

        if (CURLE_OK != curl_errno($this->curl)) {
            throw new \Exception(
                curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL) . ' : ' . curl_error($this->curl),
                curl_errno($this->curl)
            );
        }

        $code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

        if (400 <= $code) {
            throw new HttpException('HTTP error occurs: ' . $this->lastResponse, $code);
        }

        return $this->lastResponse;
    }

    /**
     * Возвращает последний отправленный запрос
     *
     * @return string
     */
    public function getLastRequest()
    {
        return $this->lastRequest;
    }

    /**
     * Возвращает заголовки, которые были отправлены в последнем запросом
     *
     * @return string
     */
    public function getLastRequestHeaders()
    {
        return $this->lastRequestHeaders;
    }

    /**
     * Возвращает ответ на последний отправленный запрос
     *
     * @return string|null
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Возвращает заголовки ответа на последний отправленный запрос
     *
     * @return string|null
     */
    public function getLastResponseHeaders()
    {
        return $this->lastResponseHeaders;
    }

    /**
     * @param int $name
     * @param mixed $value
     * @return bool
     */
    public function setOpt($name, $value)
    {
        if ($name === CURLOPT_HTTPHEADER) {
            $this->optHeaders = array_merge($this->optHeaders, (array)$value);
            return true;
        }
        return curl_setopt($this->curl, $name, $value);
    }

    /**
     * @param string $api
     */
    public function setLogApi($api)
    {
        $this->api = $api;
    }

    /**
     * Выключает логирование ответа от сервера
     */
    public function disableResponseLogging()
    {
        $this->responseLoggingDisabled = true;
    }

    /**
     * Возвращает заголовки запроса
     *
     * @return string
     */
    public function getRequestHeaders()
    {
        return curl_getinfo($this->curl, CURLINFO_HEADER_OUT);
    }
}