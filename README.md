# FatAhoCorasick
A little PHP library implementing the [Aho–Corasick algorithm](https://en.wikipedia.org/wiki/Aho%E2%80%93Corasick_algorithm)  
The original paper cound be found [here](https://www.uio.no/studier/emner/matnat/ifi/INF3800/v13/undervisningsmateriale/aho_corasick.pdf)

一个纯PHP实现的 [Aho-Corasick算法](https://en.wikipedia.org/wiki/Aho%E2%80%93Corasick_algorithm)  
算法的原论文可以看[这里](https://www.uio.no/studier/emner/matnat/ifi/INF3800/v13/undervisningsmateriale/aho_corasick.pdf)  
百度搜出来的AC算法的中文讲解就那么几篇，转载来转载去的，但我表示看不懂。  
索性一怒之下看原始的论文，然后根据论文中的算法写了这个PHP实现。  
改天我也写篇中文讲解，争取比那几篇写得更容易懂一些。

#Requires
PHP 7.0 or higher

#Installation

```
 composer require tianhe1986/fatahocorasick
```

and then in your code

```php
require_once __DIR__ . '/vendor/autoload.php';
use FatAhoCorasick\FatAhoCorasick;
```

# Usage

```php
$ac = new FatAhoCorasick();

//add keyword, string or array
$ac->addKeyword(['art', 'cart']);
$ac->addKeyword('ted');

//compute info
$ac->compute();

//search
$result = $ac->search('a carted mart lot one blue ted');
```

`$result` would be like follows:
```
(
    [0] => Array
        (
            [0] => cart
            [1] => 2
        )

    [1] => Array
        (
            [0] => art
            [1] => 3
        )

    [2] => Array
        (
            [0] => ted
            [1] => 5
        )

    [3] => Array
        (
            [0] => art
            [1] => 10
        )

    [4] => Array
        (
            [0] => ted
            [1] => 27
        )

)
```

For each item in `$result`, item[0] means the keyword found, item[1] means its start location.