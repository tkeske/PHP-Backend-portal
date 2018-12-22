<?php
/**
 * @author Tomáš Keske
 */
namespace App\Presenters;
use Nette;
use Nette\Application\UI\Form;

class LoginPresenter extends PublicPresenter
{
    protected function createComponentLoginForm(){
        $form = new Form;
        $form->addText('email', 'E-mail:')->addRule(Form::FILLED, "Pole musí být vyplněno.");
                                            //->addRule(Form::EMAIL, "Zadejte prosím platný email.");
        $form->addPassword('pass', 'Heslo:')->addRule(Form::FILLED, "Pole musí být vyplněno.");
        $form->addSubmit('log', 'Login');
        $form->onValidate[] = [$this, 'loginFormValidate'];
        $form->onSuccess[] = [$this, 'loginFormSuccess'];
        return $form;
    }
    
    public function loginFormValidate(Form $form, $values){
        try{
            $this->getUser()->login($values["email"], $values["pass"]);
        } catch (\Exception $e){
            $form->addError($e->getMessage());
        }   
    }
    
    public function loginFormSuccess(Form $form, $values){
        $this->flashMessage('Vítejte');
        $this->redirect('Uvod:default');
    }
}