<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
    Swal.fire({
        icon: 'info',
        title: 'Thank you!',
        text: 'Thank you for using our website. Come back again!',
        timer: 1500,
        showConfirmButton: false
    }).then(function() {
        window.location.href='./customers/customers-page.php';
    });
</script>
</body>
</html>
