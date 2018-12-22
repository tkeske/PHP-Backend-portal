<?php
/**
 * @author Tomáš Keske
 */
namespace App\Presenters;
use Nette;
use App\User;
use \App\Factory\AddUserForm;
use Nette\Application\UI\Form;


class RegisterPresenter extends PublicPresenter
{
    /** @var AddUserForm @inject */
    public $form;
    
    protected function createComponentRegisterForm(){
        $form = $this->form->create();
        $form->addSubmit("submit", "Registrovat");
        $form->onSuccess[] = [$this, "regSuccess"];
        return $form;
    }
    public function regSuccess(Form $form){
        $this->flashMessage('Byl jste úspěšně registrován.');
        $this->redirect('Login:default');
    }
}