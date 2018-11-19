<?php

namespace FatAhoCorasick\Tests;

use PHPUnit\Framework\TestCase;
use FatAhoCorasick\FatAhoCorasick;

class FatAhoCorasickTest extends TestCase
{
    public function testSearchWithoutNext()
    {
        $fatAhoCorasick = new FatAhoCorasick();
        $fatAhoCorasick->addKeyword(['art', 'cart']);
        $fatAhoCorasick->addKeyword('ted');
        $fatAhoCorasick->compute(false);
        $result = $fatAhoCorasick->search("a carted mart lot one blue ted");
        $this->assertEquals(5, count($result));
        $this->assertEquals('cart', $result[0][0]);
        $this->assertEquals(2, $result[0][1]);
        $this->assertEquals('art', $result[1][0]);
        $this->assertEquals(3, $result[1][1]);
        $this->assertEquals('ted', $result[2][0]);
        $this->assertEquals(5, $result[2][1]);
        $this->assertEquals('art', $result[3][0]);
        $this->assertEquals(10, $result[3][1]);
        $this->assertEquals('ted', $result[4][0]);
        $this->assertEquals(27, $result[4][1]);
    }
    
    public function testSearchByFailure()
    {
        $fatAhoCorasick = new FatAhoCorasick();
        $fatAhoCorasick->addKeyword(['art', 'cart']);
        $fatAhoCorasick->addKeyword('ted');
        $fatAhoCorasick->compute(false);
        
        $newFatAhoCorasick = new FatAhoCorasick();
        $result = $newFatAhoCorasick->searchByFailure("a carted mart lot one blue ted", $fatAhoCorasick->getOutput(), $fatAhoCorasick->getGoto(), $fatAhoCorasick->getFailure());
        $this->assertEquals(5, count($result));
        $this->assertEquals('cart', $result[0][0]);
        $this->assertEquals(2, $result[0][1]);
        $this->assertEquals('art', $result[1][0]);
        $this->assertEquals(3, $result[1][1]);
        $this->assertEquals('ted', $result[2][0]);
        $this->assertEquals(5, $result[2][1]);
        $this->assertEquals('art', $result[3][0]);
        $this->assertEquals(10, $result[3][1]);
        $this->assertEquals('ted', $result[4][0]);
        $this->assertEquals(27, $result[4][1]);
    }
    
    public function testSearchNext()
    {
        $fatAhoCorasick = new FatAhoCorasick();
        $fatAhoCorasick->addKeyword(['art', 'cart']);
        $fatAhoCorasick->addKeyword('ted');
        $fatAhoCorasick->compute();
        $result = $fatAhoCorasick->search("a carted mart lot one blue ted");
        $this->assertEquals(5, count($result));
        $this->assertEquals('cart', $result[0][0]);
        $this->assertEquals(2, $result[0][1]);
        $this->assertEquals('art', $result[1][0]);
        $this->assertEquals(3, $result[1][1]);
        $this->assertEquals('ted', $result[2][0]);
        $this->assertEquals(5, $result[2][1]);
        $this->assertEquals('art', $result[3][0]);
        $this->assertEquals(10, $result[3][1]);
        $this->assertEquals('ted', $result[4][0]);
        $this->assertEquals(27, $result[4][1]);
    }
    
    public function testSearchByNext()
    {
        $fatAhoCorasick = new FatAhoCorasick();
        $fatAhoCorasick->addKeyword(['art', 'cart']);
        $fatAhoCorasick->addKeyword('ted');
        $fatAhoCorasick->compute();
        
        $newFatAhoCorasick = new FatAhoCorasick();
        $result = $newFatAhoCorasick->searchByNext("a carted mart lot one blue ted", $fatAhoCorasick->getOutput(), $fatAhoCorasick->getNext());
        $this->assertEquals(5, count($result));
        $this->assertEquals('cart', $result[0][0]);
        $this->assertEquals(2, $result[0][1]);
        $this->assertEquals('art', $result[1][0]);
        $this->assertEquals(3, $result[1][1]);
        $this->assertEquals('ted', $result[2][0]);
        $this->assertEquals(5, $result[2][1]);
        $this->assertEquals('art', $result[3][0]);
        $this->assertEquals(10, $result[3][1]);
        $this->assertEquals('ted', $result[4][0]);
        $this->assertEquals(27, $result[4][1]);
    }
    
    public function testSearchCompare()
    {
        $keywords = ['学', '张学友', '黎明', '周', '驰', '周星驰', '风', '无敌风火轮'];
        $string = "在周星驰的电影《破坏之王》里，就是那个有无敌风火轮和很像黎明的林国斌说过'我不是针对谁，我是说在座的各位都是垃圾'的电影里，黎明的名字是出现过的。"
                . "在何金银排队买张学友演唱会门票的时候，几位妹子说完'张学友！张学友！我们爱你！'之后，有个老头站起来大喊'我爱黎明！'然后就被打了";
        $foundStrpos = [];
        foreach ($keywords as $keyword) {
            $i = 0;
            while (($i = strpos($string, $keyword, $i)) !== FALSE) {
                $foundStrpos[] = [$keyword, $i];
                $i++;
            }
        }
        
        $fatAhoCorasick = new FatAhoCorasick();
        $fatAhoCorasick->addKeyword($keywords);
        $fatAhoCorasick->compute();
        $foundAhoCorasick = $fatAhoCorasick->searchWithoutNext($string);
        $foundAhoCorasickNext = $fatAhoCorasick->search($string);
        
        $comp = function($a, $b) {
            return ($a[1] === $b[1]) ? ($a[0] > $b[0]) : ($a[1] > $b[1]);
        };
        
        usort($foundStrpos, $comp);
        usort($foundAhoCorasick, $comp);
        usort($foundAhoCorasickNext, $comp);
        
        $this->assertSame($foundStrpos, $foundAhoCorasick);
        $this->assertSame($foundStrpos, $foundAhoCorasickNext);
    }
    
    public function testSearchCompareSeparate()
    {
        $keywords = ['学', '张学友', '黎明', '周', '驰', '周星驰', '风', '无敌风火轮'];
        $string = "在周星驰的电影《破坏之王》里，就是那个有无敌风火轮和很像黎明的林国斌说过'我不是针对谁，我是说在座的各位都是垃圾'的电影里，黎明的名字是出现过的。"
                . "在何金银排队买张学友演唱会门票的时候，几位妹子说完'张学友！张学友！我们爱你！'之后，有个老头站起来大喊'我爱黎明！'然后就被打了";
        $foundStrpos = [];
        foreach ($keywords as $keyword) {
            $i = 0;
            while (($i = strpos($string, $keyword, $i)) !== FALSE) {
                $foundStrpos[] = [$keyword, $i];
                $i++;
            }
        }
        
        $fatAhoCorasick = new FatAhoCorasick();
        $fatAhoCorasick->addKeyword($keywords);
        $fatAhoCorasick->compute();
        
        $newFatAhoCorasick = new FatAhoCorasick();
        $foundAhoCorasick = $newFatAhoCorasick->searchByFailure($string, $fatAhoCorasick->getOutput(), $fatAhoCorasick->getGoto(), $fatAhoCorasick->getFailure());
        $foundAhoCorasickNext = $newFatAhoCorasick->searchByNext($string, $fatAhoCorasick->getOutput(), $fatAhoCorasick->getNext());
        
        $comp = function($a, $b) {
            return ($a[1] === $b[1]) ? ($a[0] > $b[0]) : ($a[1] > $b[1]);
        };
        
        usort($foundStrpos, $comp);
        usort($foundAhoCorasick, $comp);
        usort($foundAhoCorasickNext, $comp);
        
        $this->assertSame($foundStrpos, $foundAhoCorasick);
        $this->assertSame($foundStrpos, $foundAhoCorasickNext);
    }
}
