<?php

if (!file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    echo "Please run 'composer install' on the root directory before running the sample script.";
    return;
}
require __DIR__ . '/../../vendor/autoload.php';

use Neveldo\TextGenerator\TextGenerator;

$template = <<<EOF
#if{%sex% = m|
#shuffle{ |%name% est un #random{acteur|comédien|artiste} #if{%age% <= 30|débutant|confirmé} #random{de|agé de|qui a} %age% ans.|%name% #random{est né|a vu le jour} le %birthdate% #random{dans la ville de|à} %birthplace%.}|
#shuffle{ |%name% est une #random{actrice|comédienne|artiste} #if{%age% <= 30|débutante|confirmée} #random{de|agé de|qui a} %age% ans.|%name% #random{est née|a vu le jour} le %birthdate% #random{dans la ville de|à} %birthplace%.}
}
EOF;


$data = [
    [
        '%name%' => 'Tom Cruise',
        '%birthdate%' => '3 juillet 1962',
        '%age%' => '53',
        '%sex%' => 'm',
        '%birthplace%' => 'Syracuse',
    ],
    [
        '%name%' => 'Hayden Panettiere',
        '%birthdate%' => '21 août 1989',
        '%age%' => '26',
        '%sex%' => 'f',
        '%birthplace%' => 'Palisades',
    ],
    [
        '%name%' => 'Leonardo DiCaprio',
        '%birthdate%' => '11 novembre 1974 2',
        '%age%' => '41',
        '%sex%' => 'm',
        '%birthplace%' => 'Los Angeles',
    ],
    [
        '%name%' => 'Meryl Streep',
        '%birthdate%' => '22 juin 1949',
        '%age%' => '66',
        '%sex%' => 'f',
        '%birthplace%' => 'Summit',
    ],
];

$tg = new TextGenerator();
$tg->setTemplate($template);

foreach($data as $actorData) {
    echo $tg->generate($actorData) . PHP_EOL;
}