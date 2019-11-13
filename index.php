<?php

$arr = [
    [
        'id' => 2,
        'sort' => 1
    ],
    [
        'id' => 1,
        'sort' => 2
    ],
    [
        'id' => 2,
        'sort' => 2
    ],
    [
        'id' => 1,
        'sort' => 1
    ],
];

usort($arr, function($a, $b){
    if($a['id'] > $b['id']) return 1;
    if($a['id'] < $b['id']) return -1;
    if($a['sort'] > $b['sort']) return 1;
    if($a['sort'] < $b['sort']) return -1;
});

echo '<pre>'; print_r($arr); echo '</pre>';