<?php
/**
 * @author Tomáš Keske
 */
namespace App\Presenters;

class AffiliatePresenter extends RestrictedPresenter
{
    
    public function renderDefault(){
        $this->template->userId = $this->getUser()->getId();
    }
    
    public function actionDownload(){
        
        $this->passUserArgumentToVM();
        
        $uuid = $this->getVirtualMachineUUID();
        $pid  = $this->initiateRunner($uuid); 
        
        $response = $this->getHttpResponse();
        
        $response->addHeader('Content-Type', 'text/event-stream');
        $response->addHeader('Cache-Control', 'no-cache');
        
        $i = 0;
        
        while($this->isProcessRunning($pid)) {
            $this->message_notify($i, 'on iteration ' . $i . ' of 10' , $i*10); 
            sleep(5);
            $i++;
        }
        
        $this->message_notify('CLOSE', 'Process complete', null);
        $this->terminate();
    }
    
    private function message_notify($id, $message, $progress){
        $d = array('message' => $message , 'progress' => $progress);

        echo "id: $id" . PHP_EOL;
        echo "data: " . json_encode($d) . PHP_EOL;
        echo PHP_EOL;

        ob_flush();
        flush();
    }
    
    private function passUserArgumentToVM(){
        $uid = $this->getUser()->getId();
        shell_exec("VBoxManage guestproperty set windows7 user_id ". $uid);
    }
    
    private function initiateRunner($uuid){
        $pid = shell_exec("VBoxManage startvm '".$uuid."' --type HeadLess|at now");
        return $pid;
    }
    
    private function isProcessRunning($pid){
       exec("ps $pid", $ProcessState);
       return(count($ProcessState) >= 2);
    }
    
    private function getVirtualMachineUUID(){
        $str = ("VBoxManage list vms");
        $ret = explode(" ", $str)[1];
        return $ret;
    }
}
