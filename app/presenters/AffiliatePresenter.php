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
        
        $cmsUID = $this->getUser()->getId();
        $uuid = $this->getVirtualMachineUUID();
        $this->passUserArgumentToVM($uuid);
        $pid  = $this->initiateRunner($uuid); 
        
        $response = $this->getHttpResponse();
        
        $response->addHeader('Content-Type', 'text/event-stream');
        $response->addHeader('Cache-Control', 'no-cache');
        
        $i = 0;
        
        while(!$this->fileInstallerExists($cmsUID)) {
            $this->message_notify($i, 'on iteration ' . $i . ' of 10' , $i*10); 
            sleep(7);
            $i++;
        }
        
        $this->message_notify('CLOSE', 'Process complete', null);
        $this->terminate();
    }
    
    private function fileInstallerExists($cmsUID){
        chdir("../");
        return file_exists(getcwd()."/www/downloads/BotInstaller_".$cmsUID."-1.0.0-64.msi");
    }
    
    private function message_notify($id, $message, $progress){
        $d = array('message' => $message , 'progress' => $progress);

        echo "id: $id" . PHP_EOL;
        echo "data: " . json_encode($d) . PHP_EOL;
        echo PHP_EOL;

        ob_flush();
        flush();
    }
    
    public function logAction($message){
        $h = fopen("AFF.txt", "a+");
        $prepend = "[DEBUG] - ". date("d-m-Y H:i:s", time()) . ": \n";
        $message = $prepend . $message . "\n";
        fwrite($h, $message);
        fclose($h);
    }
    
    private function passUserArgumentToVM($uuid){
        $uid = $this->getUser()->getId();
        $res = shell_exec("sudo -u defaultunderground VBoxManage guestproperty set ".$uuid." \"user_id\" ". $uid);
        $this->logAction("passArgumentToVM uuid je:" .$uuid ."\n");
        $this->logAction("passArgumentToVM output je:" .$res."\n");
    }
    
    private function initiateRunner($uuid){
        
        $descriptors = array(
            0 => array("pipe", "r"),
            1 => array("pipe", "w"), 
            2 => array("pipe", "r")
         );
        
        $p = proc_open("sudo -u defaultunderground nohup VBoxManage startvm ".$uuid." echo $! &", $descriptors, $pipes);
        $pid = proc_get_status($p)["pid"];
        
        $this->logAction("initiateRunner PID is ".$pid."\n");
        return $pid;
    }
    
    private function getVirtualMachineUUID(){
        $str = shell_exec("sudo -u defaultunderground VBoxManage list vms");
        $ret = trim(explode(" ", $str)[1]);
        $this->logAction("VirtualMachineUUID is ".$ret."\n");
        return $ret;
    }
}
