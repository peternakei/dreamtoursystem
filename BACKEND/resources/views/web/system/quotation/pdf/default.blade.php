<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ $version->reference_number }}</title>
</head>

<body style="margin: 0; padding: 0; background: #ffffff;">
    @php($includeDocumentStyles = true)
    @include('web.system.quotation.partials.document')
</body>

</html>
