<?php

namespace common\extensions\parser;

use Closure;
use common\models\db\Product;
use common\extensions\parser\Counter;

/**
 * Main parser class
 */
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
    
    /**
     * Creates new parser instance and parses received data.
     * 
     * @param string $data product list
     * @return \common\extensions\parser\Parser
     */
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
    
    /**
     * Starts word sorting and applying parsing result to product models.
     * 
     * @return Product[] resulting product list
     */
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
    
    /**
     * Iterates line list.
     * 
     * @param \Closure $callback rule applying to each line
     */
    protected function each(Closure $callback)
    {
        foreach ($this->_lines as &$line) {
            $callback($line);
        }
    }
}

