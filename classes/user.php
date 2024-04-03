<?php
require_once "jsonstorage.php";
class UserRepository extends JsonStorage
{
    public function __construct()
    {
        parent::__construct('./data/users.json');
    }
}