<?php
interface FileIO
{
    public function save_to_file(string $filename, $data);
    public function load_from_file(string $filename, bool $array_result = false, $default_data = []);
};

class JsonIO implements FileIO
{
    public function save_to_file(string $filename, $data)
    {
        $s = json_encode($data);
        return file_put_contents($filename, $s, LOCK_EX);
    }
    public function load_from_file(string $filename, bool $array_result = false, $default_data = [])
    {
        $s = @file_get_contents($filename);
        return ($s === false
            ? $default_data
            : json_decode($s, $array_result));
    }
}