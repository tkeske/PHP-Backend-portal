<?php
/**
 * @author Tomáš Keske
 */
namespace App\Presenters;
use Nette;
use Nette\Application\UI\Form;

class UvodPresenter extends RestrictedPresenter
{

    public function actionLogout(){
            $this->getUser()->logout(true);
            $this->flashMessage("Byl jste úspěšně odhlášen.");
            $this->redirect("Login:default");
    }
}