<?php
// app/helpers.php 或其他自定义辅助函数文件

/**
* 格式化文件大小
*
* @param string $filePath 文件路径
* @return string 格式化后的文件大小
*/
function formatFileSize($filePath)
{
// 检查文件是否存在于 storage/app 目录下
    if (Storage::exists($filePath)) {
        // 获取文件大小，返回人类可读格式
        $bytes = Storage::size($filePath);
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        // 避免除以零错误
        if ($bytes > 0) {
            $i = floor(log($bytes, 1024));
            $formattedSize = round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
        } else {
            $formattedSize = '0 B'; // 文件大小为零的情况
        }

        return $formattedSize;
    }

    return 'N/A';
}

/**
* 格式化文件日期
*
* @param string $filePath 文件路径
* @return string 格式化后的文件日期
*/
function formatDate($filePath)
{

    if (Storage::exists($filePath)) {
        // 获取文件的最后修改时间
        $timestamp = Storage::lastModified($filePath);

        // 格式化时间戳为可读的日期时间格式
        $formattedDateTime = date('Y-m-d H:i:s', $timestamp);

        return $formattedDateTime;
    }

    return 'N/A';
}
