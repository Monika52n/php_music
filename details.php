<?php
    require_once "classes/musicstorage.php";
    require_once "classes/playliststorage.php";
    require_once "classes/auth.php";
    $music_repositary = new MusicRepository();
    $playlist_repositary = new PlaylistRepository();
    session_start();
    if(count($_POST)!==0 && isset($_POST['name'])) $_SESSION['name']=$_POST['name'];
        $name=$_SESSION['name'];

    //adding track to playlist
    if(count($_POST)!==0 && isset($_POST['tracks'])) {
        $track=$_POST['tracks'];
        $num = $music_repositary->getKey($track);
        $playlist_repositary->addMusicToUser((string)( $_SESSION['name']), $num);
    } 
    //removing track from playlist
    if(count($_POST)!==0 && isset($_POST['tracks2'])) {
        $track=$_POST['tracks2'];
        $num = $music_repositary->getKey($track);
        $playlist_repositary->removeMusicFromUser((string)( $_SESSION['name']), $num);
    } 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Részletek</title>
    <link rel="stylesheet" href="data/index.css">
</head>
<body>
    <?php
    //listing tracks
    echo "<h1>" . $name . "</h1>";
    $playlist = $playlist_repositary->getPlaylistByName((string)($name));
    $totalLength=0;
    for($i=0; $i<count(($playlist->tracks)); $i++) {
        $track=($music_repositary->all())[$playlist->tracks[$i]];
        $totalLength=$totalLength+$track->length;
    }
    echo "<p>Teljes hossz: " . $totalLength . " sec</p>";
    for($i=0; $i<count(($playlist->tracks)); $i++) {
        $track=($music_repositary->all())[$playlist->tracks[$i]];
        echo "<h2>" . $track->title . "</h2><ul>";
        echo "<li>Előadó: " . $track->musician . "</li>";
        echo "<li>Hossz: " . $track->length . " sec</li>";
        echo "<li>Kiadás éve: " . $track->productionyear . "</li>";
        echo "<li>Műfaj: " . $track->genre . "</li></ul>";
    }

    $auth = new Auth();
    if(isset($_SESSION['name']) && isset($_SESSION['user'])) {
        $playlist = $playlist_repositary->getPlaylistByName($_SESSION['name']);
        if ($auth->is_authenticated() && $_SESSION['user']==$playlist->created_by) {
            //adding track to playlist
            echo '<form action="" method="post" novalidate>';
            echo '<label for="tracks">Szám hozzáadása:<label><br><select name="tracks" id="tracks" >';
            foreach($music_repositary->all() as $track) {
                $key=$music_repositary->getKey($track->title);
                if($key!==null && !$playlist->isTrackInit($key)) {
                    echo '<option value="' . $track->title  . '">' . $track->title . '</option>';
                }
            }
            echo "</select><br>";
            echo "<button>Hozzáad</button></form>";
            //removing track from playlist
            echo '<form action="" method="post" novalidate>';
            echo '<label for="tracks2">Szám törlése:</label><br><select name="tracks2" id="tracks2">';
            for($i=0; $i<count(($playlist->tracks)); $i++) {
                $track=($music_repositary->all())[$playlist->tracks[$i]];
                echo '<option value="' . $track->title  . '">' . $track->title . '</option>';
            }
            echo "</select><br>";
            echo "<button>Töröl</button></form>";
        }
    }
    ?>
    <p><a href="index.php">Vissza</a></p>
</body>
</html>