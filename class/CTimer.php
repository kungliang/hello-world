<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CTimer
 *
 * @author tkms
 */
class CTimer {
    //put your code here
    private $mStartTime;
    private $mEndTime;
    
    public function __construct() {
        $this->mStartTime = 0;
        $this->mEndTime = 0;
    }
    public function reset(){
        $this->mStartTime = 0;
        $this->mEndTime = 0;
    }
    public function start(){
        $this->mStartTime = microtime(true);
    }
    public function stop(){
        $this->mEndTime = microtime(true);
    }
    public function getDuration(){
        return ($this->mEndTime - $this->mStartTime);
    }
}
