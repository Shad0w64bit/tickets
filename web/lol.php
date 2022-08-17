<?php

function makeSingleArray($array, $path = '')
{
    if (!is_array($array)) { return false; }

    $tmp = [];    
    foreach ($array as $key => $val)
    {        
        $id = (empty($path)) ? $key : "$path.$key";
        if (is_array($val))
        {
            $tmp = array_merge($tmp, makeSingleArray($val, $id));
        } else {
            $tmp[$id] = $val;
        }
    }
    return $tmp;
}
/*
$data = [
    'id' => 1,
    'user' => [
        'name' => 'Kostya',
        'pass' => 221,
    ],
];
*/

/*$data = json_decode('{"departament":"\u0421\u0435\u0440\u0432\u0438\u0441\u043d\u044b\u0439 \u0446\u0435\u043d\u0442\u0440"}', true);
var_dump($data);
$data = makeSingleArray($data);

var_dump($data);
*/



/*
$data = json_decode('[{"id":"6","first_name":"\u041f\u043e\u043b\u044c\u0437\u043e\u0432\u0430\u0442\u0435\u043b\u044c","last_name":"1","organization":"IT-Connect"},{"id":"8","first_name":"\u0410\u0434\u043c\u0438\u043d","last_name":"0","organization":"IT-Connect"},{"id":"7","first_name":"\u041f\u043e\u043b\u044c\u0437\u043e\u0432\u0430\u0442\u0435\u043b\u044c","last_name":"2","organization":"IT-Connect"},{"id":"5","first_name":"\u041a\u043e\u043d\u0441\u0442\u0430\u043d\u0442\u0438\u043d","last_name":"\u0422\u0438\u043c\u043e\u0448\u0435\u043d\u043a\u043e","organization":"\u041a\u043b\u0438\u0435\u043d\u0442"}]',true);

$groups = [];
$group = null;
foreach ($data as &$row)
{
    if ($group['text'] !== $row['organization'])
    {
        var_dump($group);
        $group = ['text'=>$row['organization']];        
    }
    $group['children'][] = [
        'id' => $row['id'],
        'text' => $row['first_name'] . ' ' . $row['last_name'],
    ];
}
if (isset($group))
{
    var_dump($group);
}

//var_dump($groups);

*/


















/*
$data = [
    'user' => [
        'name' => 'Vasya',
        'city' => 'Moscow',
        'user' => [
            'name' => 'Vasya',
            'city' => ['gav' => 'Lodon'],
        ],
    ],
    'date' => '04.09.2018',
    'text' => 'lol',
];


function makeSingleArray($array, $path = '')
{
    if (!is_array($array)) { return false; }
    
    $tmp = [];    
    foreach ($array as $key => $val)
    {        
        $id = (empty($path)) ? $key : "$path.$key";
        if (is_array($val))
        {
            $tmp = array_merge($tmp, makeSingleArray($val, $id));
        } else {
            $tmp[$id] = $val;
        }
    }
    return $tmp;
}

var_dump(makeSingleArray($data));
 * 
 */