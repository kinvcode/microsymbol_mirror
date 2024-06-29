<!DOCTYPE html>
<html>
<head>
    <title>Mirror Site</title>
    <link rel="stylesheet" href="/static/default-DIYeuWpa.css">
    <link rel="stylesheet" href="/static/bs3-polyfill-D-SBFs68.css">
    <link rel="stylesheet" href="/static/fancyIndex-BPBfy95N.css">
    <link rel="stylesheet" href="/static/UpdateField-ZBPf6dyV.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Mirror Site</h1>

    <table id="list" class="table">
        <thead>
        <tr>
            <th style="width:55%"><a href="javascript:void(0);">File Name</a>&nbsp;</th>
            <th style="width:20%"><a href="javascript:void(0);">File Size</a>&nbsp;</th>
            <th style="width:25%"><a href="javascript:void(0);">Date</a>&nbsp;</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="link"><a href="{{ url('/' . dirname($path)) }}">Parent directory/</a></td>
            <td class="size">-</td>
            <td class="date">-</td>
        </tr>

        @foreach ($directories as $directory)
            <tr>
                <td class="link"><a href="{{ url('/' . $directory) }}" title="{{ basename($directory) }}">{{ basename($directory) }}</a></td>
                <td class="size"> - </td>
                <td class="date">{{ app('App\Http\Controllers\MirrorController')->formatDate($directory) }}</td>
            </tr>
        @endforeach

        @foreach ($files as $file)
            <tr>
                <td class="link"><a href="{{ url('/' . $file) }}" title="{{ basename($file) }}">{{ basename($file) }}</a></td>
                <td class="size">{{ app('App\Http\Controllers\MirrorController')->formatFileSize($file) }}</td>
                <td class="date">{{ app('App\Http\Controllers\MirrorController')->formatDate($file) }}</td>
            </tr>
        @endforeach

        </tbody>
    </table>
</div>

</body>
</html>
