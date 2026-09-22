<?php

    declare (strict_types = 1);

    if (session_status() === PHP_SESSION_NONE) {
    session_start();
    }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plantera | Sair</title>
</head>
<body>
<script>
    fetch('index.php?resource=auth&action=logout', { method: 'POST' })
        .finally(function () {
            window.location.replace('login.php');
        });
</script>
</body>
</html>
