<?php

namespace common\extensions\parser;

use Yii;
use phpMorphy;

class WordTester
{
    const TYPE_NOUN = 0;
    const TYPE_AJECTIVE = 1;
    
    public $_morphy;
    
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
    
    public function testNoun($str)
    {
        return $this->testPartOfSpeech($str, 'С', 'ИМ');
    }
    
    public function testAjective($str)
    {
        return $this->testPartOfSpeech($str, 'П', 'ИМ');
    }
    
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