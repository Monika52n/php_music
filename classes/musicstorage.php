<?php
require_once "jsonstorage.php";

class Music
{
    public $_id = null;
    public $title;
    public $musician;
    public $length;
    public $productionyear;
    public $genre;

    public function __construct($title = null, $musician = null, $length = null, $productionyear = null, $genre = null)
    {
        $this->title = $title;
        $this->musician = $musician;
        $this->length = $length;
        $this->productionyear = $productionyear;
        $this->genre = $genre;
    }

    public static function from_array(array $arr): Music
    {
        $instance = new Music();
        $instance->_id = $arr['_id'] ?? null;
        $instance->title = $arr['title'] ?? null;
        $instance->musician = $arr['musician'] ?? null;
        $instance->length = $arr['length'] ?? null;
        $instance->productionyear = $arr['productionyear'] ?? null;
        $instance->genre = $arr['genre'] ?? null;
        return $instance;
    }

    public static function from_object(object $obj): Music
    {
        return self::from_array((array) $obj);
    }
}

class MusicRepository
{
    private $storage;
    public function __construct()
    {
        $this->storage = new JsonStorage('data/tracks.json');
    }
    private function convert(array $arr): array
    {
        return array_map([Music::class, 'from_object'], $arr);
    }
    public function all() 
    {
        return $this->convert($this->storage->all());
    }
    public function add(Music $Music): string
    {
        return $this->storage->insert($Music);
    }
    public function getMusicByTitle(string $title = null): array
    {
        return $this->convert($this->storage->filter(function ($traks) use ($title) {
            return strpos($traks->title, $title);
        }));
    }
    public function getKey(string $name) {
        $data = $this->storage->all();
        foreach ($data as $key => $value) {
            if ($value->title === $name) {
                return $key;
            }
        }
        return null;
    }
    public function modifyMusic($title, $attrib, $m_to) {
        $this->storage->update( function ($item) use ($title) {
            return ($item->title === $title);
        },
        function (&$item) use ($attrib, $m_to) {
            switch($attrib) {
                case "title": $item->title=$m_to;
                    break;
                case "musician" : $item->musician=$m_to;
                    break;
                case "genre": $item->genre=$m_to;
                    break;
                case "length" : 
                    if(is_numeric($m_to) && is_integer((int)$m_to)) {
                        $item->length=(int)$m_to;
                    }
                    break;
                case "productionyear" :
                    if(is_numeric($m_to) && is_integer((int)$m_to)) {
                        $item->productionyear=(int)$m_to;
                    }
                    break;
                default: break; 
            }
        }
        );
    }
    public function deleteMusic($title) {
        $this->storage->delete(
            function ($item) use ($title) {
                return ($item->title==$title);
            }
        );
    }
}