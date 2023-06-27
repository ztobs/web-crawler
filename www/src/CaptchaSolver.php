<?php

namespace Ztobs\Gec;

class CaptchaSolver
{
    /** @var string */
    private $apiKey = '';

    /** @var array */
    private $result = [];
    private const API_URL = 'https://api.capsolver.com/';
    private const WAIT = 10; // seconds
    private const ATTEMPTS = 5;

    public function __construct()
    {
        $this->apiKey = getenv('CAPSOLVER_API_KEY');
    }

    public function createCaptchaTask(string $base64Image): string
    {
        $handle = curl_init();
        $data = [
            'clientKey' => $this->apiKey,
            'task' => [
                'type' => 'ImageToTextTask',
                'module' => 'common',
                'body' => $base64Image
            ]
        ];
        $data = json_encode($data);

        curl_setopt_array(
            $handle,
            [
                CURLOPT_URL => self::API_URL . 'createTask',
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_RETURNTRANSFER => true,
            ]
        );

        $response = curl_exec($handle);
        curl_close($handle);

        $this->result = json_decode($response, true);
        return $this->result['solution']['text'] ?? '';
    }

    public function getLastResultDetails(): array
    {
        return $this->result;
    }

    public function getCaptcha(string $requestId): string
    {
        return '';
    }
}