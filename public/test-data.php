<?php
ini_set('display_errors', 'yes');
error_reporting(E_ALL);

$marketplacesCount = 10;
$productsCount = 10;

function randomProductTag() {
    $allTags = [
        'tag1' => [
            'Subtag1' => [
                'SS1',
                'SS2',
                'SS3',
                'SS4',
                'SS5'
            ],
            'Subtag2' => [
                'SS6',
                'SS7',
                'SS8',
                'SS9',
                'SS10'
            ],
            'Subtag3' => [
                'SS11',
                'SS12',
                'SS13',
                'SS14',
                'SS15'
            ],
        ],
        'tag2' => [
            'Subtag22' => [
                'SSA',
                'SSS',
                'SSD',
                'SSF',
                'SSG'
            ],
            'Subtag222' => [
                '123',
                '123456',
                '123456789'
            ]
        ],
        'tag3' => [
            'Subtag33' => [
                'SSQ',
                'SSW',
                'SSE',
            ],
            'Subtag333' => [
                'SSR',
                'SST'
            ]
        ],
        'tag4' => [
            'Subtag44' => [
                'SSZ',
                'SSX',
            ],
            'Subtag444' => [
                'SSC',
                'SSV'
            ],
            'Subtag4444' => [
                'SSB'
            ]
        ],
        'tag5' => [
            'Subtag55' => [
                'SSN',
                'SSM'
            ],
            'Subtag555' => [
                'SSMMM',
                'SSMMMM'
            ],
            'Subtag5555555' => [
                'SSMMMMM'
            ]
        ]
    ];

    $result = [];
    $index = rand(0, count($allTags) - 1);

    $i = 0;
    foreach($allTags as $tag => $subtags) {
        if ($i === $index) {
            $mainTag = $tag;
            break;
        }

        $i++;
    }

    $result[] = $mainTag;
    $subtags = $allTags[$mainTag];
    $index = rand(0, count($subtags) - 1);

    $i = 0;
    foreach($subtags as $tag => $sstags) {
        if ($i === $index) {
            $mainTag = $tag;
            break;
        }

        $i++;
    }

    $result[] = $mainTag;
    $sstags = $subtags[$mainTag];
    $index = rand(0, count($subtags) - 1);

    $i = 0;
    foreach($sstags as $tag) {
        if ($i === $index) {
            $mainTag = $tag;
            break;
        }

        $i++;
    }

    $result[] = $mainTag;

    return $result;
}

function randomDescr() {
    $text = <<<TEXT
    On the other hand, we denounce with righteous indignation and dislike men who are so beguiled and demoralized by the charms of pleasure of the moment, so blinded by desire, that they cannot foresee the pain and trouble that are bound to ensue; and equal blame belongs to those who fail in their duty through weakness of will, which is the same as saying through shrinking from toil and pain. These cases are perfectly simple and easy to distinguish. In a free hour, when our power of choice is untrammelled and when nothing prevents our being able to do what we like best, every pleasure is to be welcomed and every pain avoided. But in certain circumstances and owing to the claims of duty or the obligations of business it will frequently occur that pleasures have to be repudiated and annoyances accepted. The wise man therefore always holds in these matters to this principle of selection: he rejects pleasures to secure other greater pleasures, or else he endures pains to avoid worse pains.
TEXT;
    $pieces = explode(" ", $text);
    $pieceLen = (int) round(count($pieces) / 4);
    $pieces = array_slice($pieces, rand(0, count($pieces) - $pieceLen) , $pieceLen);

    return implode(' ', $pieces);
}

$xml = new SimpleXMLElement('<worm/>');
$count = 1;

for ($i = 0; $i < $marketplacesCount; $i ++) {
    $marketplace = $xml->addChild('marketplace');
    $marketplace->addAttribute('name', 'Marketplace ' . ($i + 1));
    $marketplace->addAttribute('descr', randomDescr());

    for ($j = 0; $j < $marketplacesCount; $j ++) {
        $product = $marketplace->addChild('product');
        $product->addAttribute('name', 'Product ' . $count);
        $product->addAttribute('price', rand(1000, 100000)/ 100);
        $product->addAttribute('descr', randomDescr());
        $product->addAttribute('tags', implode(',', randomProductTag()));
        $count++;
    }
}

Header('Content-type: text/xml');
echo $xml->asXML();