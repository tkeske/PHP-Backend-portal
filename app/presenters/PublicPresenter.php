<?php
/**
 * @author Tomáš Keske
 */
namespace App\Presenters;

class PublicPresenter extends BasePresenter
{
    public function startup(){
        parent::startup();
        //zamezení přístupu na login a registraci
        //když je uživatel přihlášen
        if ($this->getUser()->isLoggedIn()){
            $this->redirect("Dashboard:default");
        }
    }
}