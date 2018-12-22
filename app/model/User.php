<?php

namespace App\Model;

class User
{
    private $database;

    public function __construct(\Nette\Database\Context $database)
    {
        
        $this->database = $database;
    }

    public function userExists($email){
        $q = $this->database->table("uzivatele")->where("email", $email)->fetchAll();
        
        if (count($q) == 0){
            return FALSE;
        } else {
            return TRUE;
        }
    }
    
    public function createUser($values){
        $this->database->table("uzivatele")->insert($values);
    }
}

