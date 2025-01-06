<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <form action="{{ url('post-data') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <p>total amount of april : {{ $totalAprilAmount }}</p>
        <p>total amount of May : {{ $totalMayAmount }}</p>
        <p>total amount of Jun : {{ $totalJunAmount }}</p>
        <button type="submit">click data</button>
    </form>
</body>
</html>