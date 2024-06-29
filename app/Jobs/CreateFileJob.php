<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class CreateFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $path;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 获取完整的路径
        $fullPath = storage_path('app/public/' . $this->path);

        // 判断文件是否存在
        if(file_exists($fullPath))
        {
            return;
        }

        // 获取微软服务器文件
        $symbol_domain = 'https://msdl.microsoft.com/download/symbols/';

        // 确保目录存在
        $directory = dirname($fullPath);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $symbol_url = $symbol_domain . $this->path;
        $client = new Client([
            'verify' => false, // 忽略 SSL 证书验证
        ]);

        $response = $client->get($symbol_url);

        if ($response->getStatusCode() == 200) {
            // 获取文件内容
            $fileContents = $response->getBody()->getContents();

            // 确保目录存在
            $directory = dirname(storage_path('app/public/' . $this->path));
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // 将文件保存到本地存储
            file_put_contents($fullPath, $fileContents);
        }
    }
}
