<?php
require_once "jsonstorage.php";

class Playlist
{
    public $_id = null;
    public $name;
    public $ispublic;
    public $created_by;
    public $tracks = [];

    public function __construct($name = null, $ispublic = null, $created_by = null, $tracks = null)
    {
        $this->name = $name;
        $this->ispublic = $ispublic;
        $this->created_by = $created_by;
        $this->tracks = $tracks;
    }

    public static function from_array(array $arr): Playlist
    {
        $instance = new Playlist();
        $instance->_id = $arr['_id'] ?? null;
        $instance->name = $arr['name'] ?? null;
        $instance->ispublic = $arr['ispublic'] ?? null;
        $instance->created_by = $arr['created_by'] ?? null;
        $instance->tracks = $arr['tracks'] ?? null;
        return $instance;
    }

    public static function from_object(object $obj): Playlist
    {
        return self::from_array((array) $obj);
    }

    public function isTrackInit($num) {
        foreach($this->tracks as $track) {
            if($track==$num) {
                return true;
            }
        }
        return false;
    }
}

class PlaylistRepository
{
    private $storage;
    public function __construct()
    {
        $this->storage = new JsonStorage('data/playlists.json');
    }
    private function convert(array $arr): array
    {
        return array_map([Playlist::class, 'from_object'], $arr);
    }
    public function all()
    {
        return $this->convert($this->storage->all());
    }
    public function add(Playlist $Playlist): string
    {
        return $this->storage->insert($Playlist);
    }
    public function getPlaylistByName(string $name = null): PlayList
    {
        foreach($this->all() as $track) {
            if($track->name===$name) {
                return $track;
            }
        }
        return null;
    }
    public function addMusicToUser(string $name, $num) {
        $this->storage->update(
            function ($item) use ($name, $num) {
                return ($item->name === $name && !in_array($num, $item->tracks));
            },
            function (&$item) use ($num) {
                $item->tracks[] = $num;
            }
        );
    }
    public function removeMusicFromUser($name, $num) {
        $this->storage->update(
            function ($item) use ($name, $num) {
                return ($item->name === $name && in_array($num, $item->tracks));
            },
            function (&$item) use ($num) {
                $key = array_search($num, $item->tracks); 
                if($key!==false) {
                    unset($item->tracks[$key]);
                }
                $item->tracks = array_values($item->tracks);
            }
        );
    }
    public function removeMusicFromLists($num) {
        $this->storage->update(
            function ($item) use ($num) {
                return (in_array($num, $item->tracks));
            },
            function (&$item) use ($num) {
                $key = array_search($num, $item->tracks); 
                if($key!==false) {
                    unset($item->tracks[$key]);
                }
                $item->tracks = array_values($item->tracks);
            }
        );
    }
}