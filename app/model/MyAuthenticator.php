<?php

namespace App\Model;

use Nette\Object;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Passwords;
use Nette\Security as NS;
use Nette\Security\Identity;

class MyAuthenticator implements NS\IAuthenticator
{
    private $database;

    public function __construct(\Nette\Database\Context $database)
    {


        $this->database = $database;
    }

    public function authenticate(array $credentials)
    {
        list($email, $heslo) = $credentials;
        $row = $this->database->table('uzivatele')
            ->where('email', $email)->fetch();

        if (!$row) {
            throw new AuthenticationException('User not found.');
        }

        if (!Passwords::verify($heslo, $row->heslo)) {
            throw new AuthenticationException('Invalid password.');
        }

        return new Identity($row->id, $row->role, ['email' => $row->email]);
    }
}

