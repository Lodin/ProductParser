<?php

namespace common\extensions\parser;

use common\models\db\Product;
use common\extensions\parser\Counter;

class Parser
{
    protected $_lines;
    protected $_brandCounter;
    protected $_categoryCounter;
    
    protected function __construct()
    {
        $this->_brandCounter = Counter::brand();
        $this->_categoryCounter = Counter::category()
            ->useTester(WordTester::create());
    }
    
    public static function load($data)
    {
        $parser = new static;
        
        foreach (explode("\n", $data) as $line) {
            if (empty($line)) {
                continue;
            }
            
            $parser->_lines[] = Line::from($line);
        }
        
        return $parser;
    }
    
    public function run()
    {
        $result = [];
        
        $this->each(function($line){
            $this->_brandCounter->count($line);
            $this->_categoryCounter->count($line);
        });
        
        $this->each(function($line) use(&$result){
            $result[] = $line
                ->consider($this->_brandCounter)
                ->consider($this->_categoryCounter)
                ->apply(new Product);
        });
        
        return $result;
    }
    
    protected function each(callable $callback)
    {
        foreach ($this->_lines as &$line) {
            $callback($line);
        }
    }
}

