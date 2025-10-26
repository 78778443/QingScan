<?php

namespace app\model;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use think\Model;


class ChatGPT extends Model
{

    /**
     * 阿里千问通义
     * @param $content
     * @return string
     */
    public static function tongyi($content): string
    {
        $sk = env('ai.tongyi_sk', 'zh-cn');
        $client = new Client([
            'base_uri' => 'https://dashscope.aliyuncs.com',
            'headers' => ['Authorization' => "Bearer {$sk}", 'Content-Type' => 'application/json',],
            'timeout' => 300, // 设置请求超时时间（秒）
        ]);


        $requestData = [
            'model' => 'qwen-turbo',
            'input' => [
                'messages' => [
                    ['role' => 'system', 'content' => '你是一个技术专家'],
                    ['role' => 'user', 'content' => $content]
                ]
            ],
            'parameters' => ['result_format' => 'message'],
        ];

        try {
            $response = $client->post('/api/v1/services/aigc/text-generation/generation', ['json' => $requestData]);
            $responseBody = (string)$response->getBody();
            return $responseBody;
        } catch (GuzzleException $e) {
            echo 'Error: ' . $e->getMessage();
            return '';
        }
    }
}