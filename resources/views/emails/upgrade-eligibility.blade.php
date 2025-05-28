<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Upgrade Eligibility</title>
</head>
<body>
    <h2>Hi {{ $user->name }},</h2>

    <p>You are now eligible for a laptop upgrade based on our 5-year replacement policy.</p>

    <p><strong>Assigned Laptop:</strong> {{ $laptop->asset_tag }} - {{ $laptop->brand }} {{ $laptop->model }}</p>
    <p><strong>Purchase Date:</strong> {{ \Carbon\Carbon::parse($laptop->purchase_date)->format('d M Y') }}</p>

    <p>Please log in to the system and submit your upgrade request at your convenience.</p>

    <p>Thank you,<br>IT Department</p>
</body>
</html>
