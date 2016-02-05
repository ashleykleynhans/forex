<?php

use Phalcon\Logger\Adapter\File as FileAdapter;

/**
 * Class ImportTask
 * @author Ashley Kleynhans <ashley.kleynhans@gmail.com>
 */
class ImportTask extends BaseTask
{
    /**
     * Import Exchange Rates
     */
    public function ratesAction()
    {
        $lock = $this->getLock('/tmp/rates-import.lock');

        if ($lock !== false) {
            $logger = new FileAdapter(realpath(__DIR__ . '/../logs') . '/rates-import.log');

            $di = \Phalcon\DI::getDefault();
            $guzzle = $di['guzzle'];
            $config = $di['config']['rates_api'];

            $url = $config['url'] .'?access_key='. $config['access_key'] .'&currencies='. $config['currencies'] .'&source='. $config['source'];

            try {
                $request = $guzzle->createRequest('GET', $url);
                $response = $guzzle->send($request);
                $response = (string)$response->getBody();
                $rates = json_decode($response);

                if ($rates->success == true) {
                    foreach ($rates->quotes as $currencyCode => $exchangeRate) {
                        $currencyCode = preg_replace('/^USD/', '', $currencyCode);

                        $currency = Rate::updateRate($currencyCode, $exchangeRate);

                        if (!$currency) {
                            $logger->log('Failed to update currency : '. $currencyCode .' to rate: '. $exchangeRate, \Phalcon\Logger::ERROR);
                        }
                    }
                } else {
                    $logger->log('Rates import failed : '. $response, \Phalcon\Logger::ERROR);
                }

            } catch (Exception $e) {
                $logger->log('Rates import failed : '. $e->getMessage(), \Phalcon\Logger::ERROR);
            }

            $this->releaseLock($lock);
        }
    }

}