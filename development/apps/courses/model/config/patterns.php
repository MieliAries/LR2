<?php
$patterns = [
    'pat_ukr_number'=> [
        'regex' => '/^[+]{0,1}[0-9]{9,12}$/',
        'callback' => function ($matches) {
        $string = $matches[0];
        $amount = preg_match_all( "/[0-9]/", $string);
        if($amount == 9){
            $string = '+380'.$string;
        }else if($amount == 10){
            $string = '+38'.$string;
        }else if($amount == 11){
            $string = '+3'.$string;
        }else if(substr($string, 0, 1)!='+'){
            $string = '+'.$string;
        }
        return $string;
        }
    ],
    'pat_name' => [
        'regex' => '/^([A-Z]{1}[a-z]{1,20})$/'
    ]
];