<?php

namespace app\components;

use yii\base\Component;
use yii\base\Exception;
use Goutte\Client;

/**
 * Scraper class for scraping web pages.
 */
class Scraper extends Component
{
    public $config = [];
    private $client = null;
    const HTTP_STATUS_CODE_OK = 200;

    /**
     * Constructor, initializes request client.
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $config = $this->config;

        if (!isset($config['endPoint']) || !isset($config['domNodes'])
            || !isset($config['domNodes']['itemNodes'])) {
            throw new Exception('Scraper missing critical settings. Please check the config.');
        }

        parent::init();
    }

    /**
     * Scrapes items from an endpoint.
     *
     * @return array - items array
     *
     * @throws \yii\base\Exception
     */
    public function scrape()
    {
        $crawler = $this->client->request('GET', $this->config['endPoint']);
        $this->checkRequestStatus();
        $result = [];

        $result = $crawler->filter($this->config['domNodes']['itemNodes'])->each(function ($node) {
            $itemLink = $node->filter($this->config['domNodes']['jumpLink'])->attr('href');
            $itemCrawler = $this->client->request('GET', $itemLink);
            $this->checkRequestStatus();
            $item = [];

            foreach ($this->config['domNodes']['itemAttributes'] as $attr => $attrData) {
                if (!empty($attrData['path'])) {
                    $item[$attr] = $itemCrawler->filter($attrData['path'])->text();
                }

                if (!empty($attrData['processor']) && method_exists($this, $attrData['processor'])) {
                    $attrProcessor = $attrData['processor'];
                    $item[$attr] = $this->$attrProcessor();
                }
            }

            return $item;
        });

        if (method_exists($this, $this->config['domNodes']['processor'])) {
            $resultProcessor = $this->config['domNodes']['processor'];
            $result = $this->$resultProcessor($result);
        }

        return $result;
    }

    /**
     * Checks that the endpoint returns an "OK" status.
     *
     * @throws \yii\base\Exception
     */
    private function checkRequestStatus()
    {
        $statusCode = $this->client->getResponse()->getStatus();

        if ($statusCode !== self::HTTP_STATUS_CODE_OK) {
            throw new Exception('Endpoint: '.$this->config['endPoint'].' is not reachable or gives error. Returns status code: '.$statusCode);
        }
    }

    /**
     * Gets an endpoint content length.
     *
     * @return string
     */
    protected function getContentSize()
    {
        $size = 0;
        $contentLength = (int) $this->client->getResponse()->getHeader('Content-Length', true);

        if ($contentLength) {
            $size = number_format($contentLength / 1024, 2);
        }

        return $size.'kb';
    }

    /**
     * Calculates cummuative sum of all items. Processor method that injects the 
     * cummulative sum in the result.
     *
     * @return array
     */
    protected function sumUp($result)
    {
        $processedResult = [];
        $cummSum = 0;
        if (!is_array($result)) {
            return $processedResult;
        }

        foreach ($result as $item) {
            if (!isset($item['unit_price'])) {
                continue;
            }

            $itemPrice = 0;
            if (preg_match('#\d+(?:\.\d{1,2})?#', $item['unit_price'], $match)) {
                $itemPrice = $match[0];
            }

            $item['unit_price'] = $itemPrice;
            $processedResult[] = $item;
            $cummSum += $itemPrice;
        }

        $processedResult['total'] = number_format($cummSum, 2);

        return $processedResult;
    }
}
