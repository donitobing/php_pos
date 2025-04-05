<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($data['title']) ? $data['title'] : 'POS System'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <?php echo $content; ?>
</body>
</html>
