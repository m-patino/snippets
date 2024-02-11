<?php

namespace App\Php\Helpers;

use Generator;

class HttpClient
{
    private static ?self $instance;

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new static();
    }

    public function getPosts(): Generator
    {
        $posts = $this->request('GET', 'https://jsonplaceholder.typicode.com/posts');
        foreach ($posts as $post) {
            yield $post;
        }
    }

    private function request(string $method, string $url, ?array $datas = [], ?array $options = []): array
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        foreach ($options as $key => $option) {
            curl_setopt($curl, strtoupper($key), $option);
        }
        if(strtoupper($method) === 'POST') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $datas);
        }

        if(false === ($response = curl_exec($curl))) {
            // @todo log
            return [];
        }

        curl_close($curl);
        return json_decode($response);
    }
}
