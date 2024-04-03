<?php
require_once "classes/auth.php";
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
    if (is_empty($input, "password2")) {
        $errors[] = "Jelszó megadása kötelező";
    }
    if($input['password']!=$input['password2']) {
        $errors[] = "A két jelszónak meg kell egyeznie";
    }
    if (is_empty($input, "email")) {
        $errors[] = "Email megadása kötelező";
    } else if(!preg_match("/^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $input['email'])) {
        $errors[] = "Nem email formátum!";
    }
    if (count($errors) == 0) {
        if ($auth->user_exists($input['username'])) {
            $errors[] = "A felhasználó már létezik";
        }
    }

    return !(bool) $errors;
}

$errors = [];
$userdata=[];
$password2="";
$userdata['username']="";
$userdata['password']="";
$userdata['email']="";
if (count($_POST) != 0) {
    $userdata['username']=$_POST['username'];
    $userdata['password']=$_POST['password'];
    $userdata['email']=$_POST['email'];
    $password2=$_POST['password2'];
    if (validate($_POST, $errors, $auth)) {
        if($_POST['username']==='admin') {
            $userdata['isAdmin']=true;
        } else {
            $userdata['isAdmin']=false;
        }
        $auth->register($userdata);
        header('Location: login.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regisztráció</title>
    <link rel="stylesheet" href="data/index.css">
</head>

<body>
    <h2>Regisztráció</h2>
    <?php if ($errors) {?>
    <ul class="error">
        <?php foreach ($errors as $error) {?>
        <li><?=$error?></li>
        <?php }?>
    </ul>
    <?php }?>
    <form action="" method="post" novalidate>
        <label for="username">Felhasználó: </label>
        <input id="username" name="username" type="text" value=<?=$userdata['username']?>><br>
        <label for="password">Jelszó: </label>
        <input id="password" name="password" type="password" value=<?=$userdata['password']?>><br>
        <label for="password2">Jelszó másodszor: </label>
        <input id="password2" name="password2" type="password" value=<?=$password2?>><br>
        <label for="email">Email: </label>
        <input id="email" name="email" type="text" value=<?=$userdata['email']?>><br>
        <input type="submit" value="Regisztráció">
    </form>
    <p><a href="login.php">Bejelentkezés</a></p>
</body>

</html>