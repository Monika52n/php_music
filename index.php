<?php
require_once "classes/auth.php";
require_once "classes/musicstorage.php";
require_once "classes/playliststorage.php";
session_start();
$auth = new Auth();
$playlist_repositary = new PlaylistRepository();
$music_repositary = new MusicRepository();
$errortocount=0;
$foundtracks=[];
$trackdata=[];
$trackdata['title']="";
$trackdata['musician']="";
$trackdata['length']="";
$trackdata['productionyear']="";
$trackdata['genre']="";
if(count($_POST)!==0) {
    if(isset($_POST['findmusic']) && !empty($_POST['findmusic'])) {
        foreach($music_repositary->getMusicByTitle((string)$_POST['findmusic']) as $track) {
            $foundtracks[]=$track->title;
        }
    } else {
        $errortocount++;
    }

    //adding playlist
    if(isset($_POST['name']) && isset($_POST['ispublic']) && isset($_SESSION['user']) &&
    !empty($_POST['name']) && !empty(isset($_POST['ispublic']))) {
        if($_POST['ispublic']=="Igen") {
        $playlist = new Playlist($_POST['name'], true, $_SESSION['user'], []); 
        } else {
            $playlist = new Playlist($_POST['name'], false, $_SESSION['user'], []); 
        }
        $playlist_repositary->add($playlist);
    } else {
        $errortocount++;
    }

    //adding new track
    $allset=0;
    if(isset($_POST['title'])) {
        $trackdata['title']=$_POST['title'];
        $allset++;
    }
    if(isset($_POST['musician'])) {
        $trackdata['musician']=$_POST['musician'];
        $allset++;
    }
    if(isset($_POST['length'])) {
        $trackdata['length']=$_POST['length'];
        $allset++;
    }
    if(isset($_POST['productionyear'])) {
        $trackdata['productionyear']=$_POST['productionyear'];
        $allset++;
    }
    if(isset($_POST['genre'])) {
        $trackdata['genre']=$_POST['genre'];
        $allset++;
    }
    if($allset==5 && !empty($_POST['title']) && !empty($_POST['musician']) && !empty($_POST['length']) 
        && !empty($_POST['productionyear']) && !empty($_POST['genre']) 
        && is_numeric($_POST['length']) && is_numeric($_POST['productionyear']) 
        && is_integer((int)($_POST['productionyear'])) && is_integer((int)($_POST['length']))) {
        $music = new Music($_POST['title'],$_POST['musician'], (int)($_POST['length']), (int)($_POST['productionyear']), $_POST['genre']);
        $music_repositary->add($music); 
    } else {
        $errortocount++;
    }

    //modifying a track
    if(isset($_POST['modify']) && isset($_POST['m_to']) && !empty($_POST['modify']) && !empty($_POST['m_to'])
    && isset($_POST['tracks']) && !empty($_POST['tracks'])) {
        $music_repositary->modifyMusic($_POST['tracks'], $_POST['modify'], $_POST['m_to']);
    } else {
        $errortocount++;
    }

    //deleting a track
    if(isset($_POST['tracks2']) && !empty($_POST['tracks2'])) {
        $num = $music_repositary->getKey($_POST['tracks2']);
        $playlist_repositary->removeMusicFromLists($num);
        $music_repositary->deleteMusic($_POST['tracks2']);
    } else {
        $errortocount++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Főoldal</title>
    <link rel="stylesheet" href="data/index.css">
</head>
<body>
    <h1>Zenekereső</h1>
    <p>Az oldalon zeneszámokból álló lejátszási listákat lehet böngészni. Megtekinthetjük, hogy a lejátszási listák hány zeneszámból állnak és ki készítette őket. Ha a részletekre kattintunk, akkor megnézhetjük, hogy a milyen zeneszámok vannak benne, és azok adatait. Például hogy mi a címük, előadójuk, hosszuk, kiadási évük és műfajaik. Bejelentkezett felhasználóként lejátszási listákat létrehozni, zeneszámokat hozzáadni és törölni is lehet.</p>
    <?php
        if($auth->is_authenticated()) {
            ?><p><a href="logout.php">Kijelentkezés</a></p><?php
        } else {
            ?> 
            <p><a href="register.php">Regisztráció</a></p>
            <p><a href="login.php">Bejelentkezés</a></p>
            <?php
        }
    ?>
    <?php if ($errortocount==5) {?>
    <p class="error">Üresen hagyott vagy helytelenül kitöltött mező!</p>
    <?php }?>
    <form action="" method="post" novalidate>
        <label for="findmusic">Írjon be egy keresendő zeneszámot:</label>
        <input type="text" name="findmusic"></input>
        <button type="submit">Keres</button>
    </form>
    <?php if ($foundtracks) {?>
    <ul>
        <?php foreach ($foundtracks as $track) {?>
        <li><?=$track?></li>
        <?php }?>
    </ul>
    <?php }?>
    <?php
    //listing playlists
    foreach($playlist_repositary->all() as $playlist) {
        if($playlist->ispublic) {
            echo "<h2>" . $playlist->name . "</h2><ul>";
            echo "<li>Zeneszámok: " . count(($playlist->tracks)) . "db</li>";
            echo "<li>Készítette: " . $playlist->created_by . "</li></ul>";
            echo '<form action="details.php" method="post" novalidate>
            <input type="text" name="name" value="'. $playlist->name .'" hidden></input>
            <button type="submit">Részletek</button>
            </form>';
        }
    }

    if($auth->is_authenticated() && isset($_SESSION['user'])) {
        //own non-public playlists
        echo "<h2>Saját privát lejátszási listák:</h2>";
        $count=0;
        foreach($playlist_repositary->all() as $playlist) {
            if(!$playlist->ispublic &&  $playlist->created_by==$_SESSION['user']) {
                echo "<h2>" . $playlist->name . "</h2><ul>";
                echo "<li>Zeneszámok: " . count(($playlist->tracks)) . "db</li></ul>";
                echo '<form action="details.php" method="post" novalidate>
                <input type="text" name="name" value="'. $playlist->name .'" hidden></input>
                <button type="submit">Részletek</button>
                </form>';
                $count++;
            }
        }
        if($count==0) {
            ?> <p>Nincsenek.</p> <?php
        }
    }
    ?>

    <?php
    if ($auth->is_authenticated()) {?>
        <!--adding playlist-->
        <h2>Lejetszási lista létrehozása</h2>
        <form action="" method="post" novalidate>
            <label for="name">Lejátszási lista neve:</label><br>
            <input type="text" name="name"></input><br>
            <label for="ispublic">Publikus-e?</label><br>
            <input type="radio" name="ispublic" value="Igen" checked>Igen</input><br>
            <input type="radio" name="ispublic" value="Nem">Nem</input><br>
            <button type="submit">Létrehozás</button>
        </form>
    <?php }?>

    <?php
    if($auth->is_authenticated() && $auth->user_isAdmin($_SESSION['user'])) {?>
        <!--adding a new track-->
        <h2>Zeneszám létrehozása</h2>
        <form action="" method="post" novalidate>
            <label for="title">Zeneszám neve:</label><br>
            <input type="text" name="title" value=<?=$trackdata['title']?>></input><br>
            <label for="musician">Előadó:</label><br>
            <input type="text" name="musician" value=<?=$trackdata['musician']?>></input><br>
            <label for="length">Hossz:</label><br>
            <input type="text" name="length" value=<?=$trackdata['length']?>></input><br>
            <label for="productionyear">Kiadás éve:</label><br>
            <input type="text" name="productionyear" value=<?=$trackdata['productionyear']?>></input><br>
            <label for="genre">Műfajok:</label><br>
            <input type="text" name="genre" value=<?=$trackdata['genre']?>></input><br>
            <button type="submit">Létrehozás</button>
        </form>
        <!--modifying a track-->
        <h2>Zeneszám módosítása</h2>
        <form action="" method="post" novalidate>
            <label for="tracks">Ezt a számot módosítom:</label><br>
            <select name="tracks" id="tracks">
                <?php
                    foreach($music_repositary->all() as $track) {
                        echo '<option value="' . $track->title  . '">' . $track->title . '</option>';
                    }
                ?>
            </select><br>
            <label for="modify">Ezt az adatát módosítom:</label><br>
            <select name="modify" id="modify">
                <option value="title">cím</option>
                <option value="musician">eldőadó</option>
                <option value="length">hossz</option>
                <option value="productionyear">kiadás éve</option>
                <option value="genre">műfajok</option>
            </select>
            <br>
            <label for="m_to">Erre módosítom:</label><br>
            <input type="text" name="m_to"></input><br>
            <button type="submit">Módosítás</button>
        </form>
        <!--deleting a track-->
        <h2>Zeneszám törlése</h2>
        <form action="" method="post" novalidate>
            <label for="tracks2">Ezt a számot törlöm:</label><br>
            <select name="tracks2" id="tracks2">
                <?php
                    foreach($music_repositary->all() as $track) {
                        echo '<option value="' . $track->title  . '">' . $track->title . '</option>';
                    }
                ?>
            </select><br>
            <button type="submit">Törlés</button>
        </form>
    <?php }?>
</body>
</html>