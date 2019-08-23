<?php

namespace FatAhoCorasick;

class FatAhoCorasick
{
    protected $maxState = 0;
    
    // keyword list
    protected $keywords = [];
    
    // goto table
    protected $goto = [];
    
    // output table
    protected $output = [];
    
    //failure table
    protected $failure = [];
    
    //next table
    protected $next = [];
    
    public function __construct()
    {

    }
    
    public function getGoto()
    {
        return $this->goto;
    }
    
    public function getOutput()
    {
        return $this->output;
    }
    
    public function getFailure()
    {
        return $this->failure;
    }
    
    public function getNext()
    {
        return $this->next;
    }
    
    public function addKeyword($keyword)
    {
        if (is_array($keyword)) {
            foreach ($keyword as $realKeyword) {
                $this->keywords[] = (string)$realKeyword;
            }
        } else {
            $this->keywords[] = (string)$keyword;
        }
    }
    
    public function compute($useNext = true)
    {
        $this->reset();
        $this->computeGoto();
        $this->computeFailure();
        if ($useNext) {
            $this->computeNext();
        }
    }
    
    protected function reset()
    {
        $this->goto = $this->failure = $this->output = $this->next = [];
    }
    
    protected function computeGoto()
    {
        $this->maxState = 0;
        foreach ($this->keywords as $keyword) {
            $this->enter($keyword);
        }
    }
    
    protected function enter(string $keyword)
    {
        $state = 0;
        $len = strlen($keyword);
        $i = 0;
        for ($i = 0; $i < $len; $i++) {
            $state = $this->goto[$state][$keyword[$i]] ?? ($this->goto[$state][$keyword[$i]] = ++$this->maxState);
        }

        $this->output[$state][] = $keyword;
    }
    
    protected function computeFailure()
    {
        $queue = [];
        $nowIndex = $endIndex = 0;
        foreach ($this->goto[0] as $char => $toState) {
            $this->failure[$toState] = 0;
            $queue[$endIndex++] = $toState;
        }
        while ($nowIndex != $endIndex) {
            $nextState = $queue[$nowIndex];
            if ( ! isset($this->goto[$nextState])) {
                unset($queue[$nowIndex++]);
                continue;
            }
            foreach ($this->goto[$nextState] as $char => $toState) {
                $queue[$endIndex++] = $toState;
                $tempState = $this->failure[$nextState];
                while($tempState !== 0 && ! isset($this->goto[$tempState][$char])) {
                    $tempState = $this->failure[$tempState];
                }
                $this->failure[$toState] = $this->goto[$tempState][$char] ?? 0;
                if (isset($this->output[$this->failure[$toState]])) {
                    if ( ! isset($this->output[$toState])) {
                        $this->output[$toState] = [];
                    }
                    $this->output[$toState] = array_merge($this->output[$toState], $this->output[$this->failure[$toState]]);
                }
            }
            unset($queue[$nowIndex++]);
        }
    }
    
    protected function computeNext()
    {
        $queue = [0];
        $nowIndex = 0;
        $endIndex = 1;
        while ($nowIndex != $endIndex) {
            $nextState = $queue[$nowIndex];
            $failureState = $this->failure[$nextState] ?? 0;
            $this->next[$nextState] = ($this->goto[$nextState] ?? []) + ($this->next[$failureState] ?? []);
            if ( isset($this->goto[$nextState])) {
                foreach ($this->goto[$nextState] as $toState) {
                    $queue[$endIndex++] = $toState;
                }
            }
            unset($queue[$nowIndex++]);
        }
    }
    
    public function searchWithoutNext($string)
    {
        return $this->searchByFailure($string, $this->output, $this->goto, $this->failure);
    }
    
    public function search($string)
    {
        if (empty($this->next)) {
            return $this->searchWithoutNext($string);
        }
        
        return $this->searchByNext($string, $this->output, $this->next);
    }
    
    public function searchByNext($string, $output, $next)
    {
        $result = [];
        $state = 0;
        $len = strlen($string);
        
        for ($i = 0; $i < $len; $i++) {
            $state = $next[$state][$string[$i]] ?? 0;
            if (isset($output[$state])) {
                foreach ($output[$state] as $outputString) {
                    $result[] = [$outputString, $i - strlen($outputString) + 1];
                }
            }
        }
        
        return $result;
    }
    
    public function searchByFailure($string, $output, $goto, $failure)
    {
        $result = [];
        $state = 0;
        $len = strlen($string);
        
        for ($i = 0; $i < $len; $i++) {
            while($state !== 0 && ! isset($goto[$state][$string[$i]])) {
                $state = $failure[$state] ?? 0;
            }
            $state = $goto[$state][$string[$i]] ?? 0;
            if (isset($output[$state])) {
                foreach ($output[$state] as $outputString) {
                    $result[] = [$outputString, $i - strlen($outputString) + 1];
                }
            }
        }
        
        return $result;
    }
}