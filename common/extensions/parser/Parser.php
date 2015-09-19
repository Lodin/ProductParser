<?php

namespace common\extensions\parser;

use common\models\db\Product;

class Parser
{
    protected $_lines;
    protected $_counts;
    
    protected function __construct() {}
    
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
        
        $this->count();
        
        foreach ($this->_lines as $line) {
            $result[] = $line
                ->consider($this->_counts)
                ->apply(new Product);
        }
        print_r($this->_lines);
        die();
        
        return $result;
    }
    
    
    protected function count()
    {
        foreach ($this->_lines as $line) {
            $this->useCount($line->count());
        }
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

