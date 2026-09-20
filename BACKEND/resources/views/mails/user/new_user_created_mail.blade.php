<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SBS SYSTEM</title>
</head>

<body>
    Dear <span style="font-weight: bold;">{{ $data['name'] }}</span>, <br><br> Your account was created successfully at
    <span style="font-weight: bold;">{{ $data['date'] }}</span> with role of <span
        style="font-weight: bold;">{{ $data['role'] }}</span> for property <span style="font-weight: bold;">{{ $data['property'] }}</span>. <br /><br /> Use this password <span
        style="font-weight: bold;">{{ $data['password'] }} </span>to login into the system.
    <br><br>
    Thank you.
    <br><br>
    <span style="font-weight: bold;">Tanzania Road Haulage (1980) Ltd.</span>
</body>

</html>
