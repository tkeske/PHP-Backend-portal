<?php

namespace App\Factory;

use Nette\Application\UI\Form;
use App\Model\User;

class AddUserForm
{
    
    public $userModel;
    
    public function __construct(User $um){
        $this->userModel = $um;
    }
  
    public function create($checkbox = NULL)
    {
        $form = new Form;
        $form->addText('name', "Jméno:")->addRule(Form::FILLED, "Jméno musí být vyplněno");
        $form->addText('email', 'E-mail:')->addRule(Form::FILLED, "Email musí být vyplněn.")
                                            ->addRule(Form::EMAIL, "Zadejte prosím platný email.");
        $form->addPassword('pass', 'Heslo:')->addRule(Form::FILLED, "Pole musí být vyplněno.");
        $form->addPassword('pass2', 'Heslo znovu:')->addRule(Form::FILLED, "Pole musí být vyplněno.")
                                                    ->addRule(Form::EQUAL, "Hesla se neshodují.", $form['pass']);
        if ($checkbox){
            $form->addCheckbox('admin', 'Je administrátorem');
        }
        
        $form->onValidate[] = [$this, 'registerFormValidate'];
        $form->onSuccess[] = [$this, 'registerFormSuccess'];
        return $form;
    }
    public function registerFormValidate(Form $form, $values){
        
        $r = $this->userModel->userExists($values['email']);
        
        if ($r){
            $form->addError("Tento email je již registrován.");
        }
    }
    public function registerFormSuccess(Form $form, $values){
      
        $values["heslo"] = password_hash($values["pass2"], PASSWORD_DEFAULT);
        $values["public_ip"] = $_SERVER["REMOTE_ADDR"];
        $values["joined"] = date("d-m-Y", time());
        
        
        unset($values["pass"]);
        unset($values["pass2"]);
        
        if (isset($values["admin"])){
            $values["role"] = 0;
        } else {
            $values["role"] = 3;
        }
        
        $this->userModel->createUser($values);
    }
}
