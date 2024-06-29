<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

class MirrorController extends Controller
{
    public function show($path = '')
    {
        // 获取完整的路径
        $fullPath = $path ? storage_path('app/public/' . $path) : storage_path('app/public');
        $storage_path = $path ? 'public/' . $path : 'public';

        // 检查路径是否存在
        if (!file_exists($fullPath)) {
            abort(404);
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
