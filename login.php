<?php
require_once "classes/auth.php";
session_start();
$auth = new Auth();
function is_empty($input, $key)
{
    return !(isset($input[$key]) && trim($input[$key]) !== "");
}
function validate($input, &$errors, $auth)
{

    if (is_empty($input, "username")) {
        $errors[] = "Felhasználónév megadása kötelező";
    }
    if (is_empty($input, "password")) {
        $errors[] = "Jelszó megadása kötelező";
    }
    if (count($errors) == 0) {
        if (!$auth->check_credentials($input['username'], $input['password'])) {
            $errors[] = "Hibás felhasználónév vagy jelszó";
        }
    }

    return !(bool) $errors;
}

$errors = [];
if (count($_POST) != 0) {
    if (validate($_POST, $errors, $auth)) {
        $auth->login($_POST);
        header('Location: index.php');
        die();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés</title>
    <link rel="stylesheet" href="data/index.css">
</head>

<body>
    <h2>Bejelentkezés</h2>
    <?php if ($errors) {?>
    <ul class="error">
        <?php foreach ($errors as $error) {?>
        <li><?=$error?></li>
        <?php }?>
    </ul>
    <?php }?>
    <form action="" method="post" novalidate>
        <label for="username">Felhasználó: </label>
        <input id="username" name="username" type="text"><br>
        <label for="password">Jelszó: </label>
        <input id="password" name="password" type="password"><br>
        <input type="submit" value="Bejelentkezés">
    </form>
    <p><a href="register.php">Regisztáció</a></p>
</body>

</html>