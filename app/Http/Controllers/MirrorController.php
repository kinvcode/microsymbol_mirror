<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use App\Jobs\CreateFileJob;

class MirrorController extends Controller
{
    public function MultipleTasks(Request $request)
    {
        if(!$request->exists('MultipleTasks'))
        {
            response()->json(['message' => 'Required field does not exist.'], 400);
        }

        $symbols = $request->input('MultipleTasks');

        foreach ($symbols as $symbol)
        {
            CreateFileJob::dispatch($symbol)->onQueue('default');
        }

        return response()->json(['message' => 'Task Executed.']);
    }

    public function show($path = '')
    {
        // 获取完整的路径
        $fullPath = $path ? storage_path('app/public/' . $path) : storage_path('app/public');
        $storage_path = $path ? 'public/' . $path : 'public';

        // 检查路径是否存在
        if (!file_exists($fullPath)) {
            // 请求微软服务器，下载文件
            if (!is_dir($fullPath)) {
                // 创建队列任务，后台下载符号文件
                CreateFileJob::dispatch($path)->onQueue('default');

                // 同时，将符号文件转发给用户
                $url = 'https://msdl.microsoft.com/download/symbols/' . $path; // 需要替换为实际的网络资源路径
                $client = new Client(['verify' => false]);
                try {
                    $response = $client->get($url);

                    if ($response->getStatusCode() == 200) {
                        $fileContents = $response->getBody()->getContents();
                        $tempFilePath = storage_path('app/temp/' . basename($path));
                        file_put_contents($tempFilePath, $fileContents);

                        return response()->download($tempFilePath)->deleteFileAfterSend(true);
                    } else {
                        return response('error2:Resource not found on server and failed to download from network.', 404);
                    }
                } catch (\Exception $e) {
                    return response('error1:Resource not found on server and failed to download from network.', 404);
                }
            } else {
                abort(404);
            }
        }

        // 判断路径是文件还是目录
        if (is_file($fullPath)) {
            return response()->download($fullPath);
        }

        // 获取文件和目录列表
        $files = Storage::files($storage_path);
        $directories = Storage::directories($storage_path);

        // 格式化文件和目录的路径
        $files = array_map(function ($file) {
            return str_replace('public/', '', $file);
        }, $files);

        $directories = array_map(function ($directory) {
            return str_replace('public/', '', $directory);
        }, $directories);

        return view('mirror.index', [
            'files' => $files,
            'directories' => $directories,
            'path' => $path
        ]);
    }

    // 辅助函数
    public function formatFileSize($filePath)
    {
        $fullPath = storage_path('app/public/' . $filePath);
        if (file_exists($fullPath)) {
            $bytes = filesize($fullPath);
            if ($bytes == 0) return '0 B';
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $i = floor(log($bytes, 1024));
            return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
        }
        return '0 B';
    }

    public function formatDate($filePath)
    {
        $fullPath = storage_path('app/public/' . $filePath);
        if (file_exists($fullPath)) {
            $timestamp = filemtime($fullPath);
            return date('Y-m-d H:i:s', $timestamp);
        }
        return 'Unknown';
    }
}
