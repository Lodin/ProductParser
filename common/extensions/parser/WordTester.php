<?php

namespace common\extensions\parser;

use Yii;
use phpMorphy;

/**
 * Incapsulates morphological word inspection logic using phpMorphy.
 */
class WordTester
{
    const TYPE_NOUN = 0;
    const TYPE_AJECTIVE = 1;
    
    protected $_morphy;
    
    /**
     * Creates new WordTester instance.
     * 
     * @return \common\extensions\parser\WordTester
     */
    public static function create()
    {
        $tester = new static;
        $tester->_morphy = new phpMorphy(
            Yii::getAlias('@common/data/dicts'),
            'ru_RU',
            ['storage' => PHPMORPHY_STORAGE_FILE]
        );
        return $tester;
    }
    
    /**
     * Tests if the word is noun in nominative case (word should be russian).
     * 
     * @param string $str
     * @return boolean
     */
    public function testNoun($str)
    {
        return $this->testPartOfSpeech($str, 'С', 'ИМ');
    }
    
    /**
     * Tests if the word is ajective in nominative case (word should be
     * russian).
     * 
     * @param string $str
     * @return boolean
     */
    public function testAjective($str)
    {
        return $this->testPartOfSpeech($str, 'П', 'ИМ');
    }
    
    /**
     * Part of speech testing implementation.
     * 
     * @param string $str
     * @param string $part
     * @param string $case
     * @return boolean
     */
    protected function testPartOfSpeech($str, $part, $case)
    {
        $result = $this->_morphy->getGramInfo(mb_strtoupper($str));
        
        if (!$result) {
            return false;
        }
        
        foreach ($result as $partOfSpeech) {
            foreach ($partOfSpeech as $variant) {
                if ($variant['pos'] === $part && in_array($case, $variant['grammems'])) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    protected function __construct() {}
}