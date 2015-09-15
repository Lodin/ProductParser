<?php

use common\models\db\Product;

class Parser
{
    protected $_lines;
    protected $_counts;
    
    protected function __construct() {}
    
    public static function load($data)
    {
        $parser = new static;
        
        foreach (explode('\n', $data) as $line) {
            $this->_lines[] = Line::from($line);
        }
        
        return $parser;
    }
    
    public function run()
    {
        $result = [];
        
        return $result;
    }
    
    
    protected function count()
    {
        foreach ($this->_lines as $line) {
            $this->useCount($line->count());
        }
    }
    
    protected function result()
    {
        $result = [];
        
        foreach ($this->_lines as $line) {
            $result[] = $line
                ->consider($this->_counts)
                ->apply(new Product);
        }
        
        return $result;
    }
    
    protected function useCount(array $count)
    {
        foreach ($count as $word => $quantity) {
            if (!isset($this->_counts[$word])) {
                $this->_counts[$word] = 0;
            }
            
            $this->_counts[$word] += $quantity;
        }
    }
}

