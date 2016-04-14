<?php

if (!file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    echo "Please run 'composer install' on the root directory before running the sample script.";
    return;
}
require __DIR__ . '/../../vendor/autoload.php';

use Neveldo\TextGenerator\TextGenerator;

$template = <<<EOF
@firstname @lastname is an @nationality #if{@sex == 'm'|actor|actress} of @age years old. #if{@sex == 'm'|He|She} was born in @birthdate in @birth_city (@birth_country). #shuffle{ |#random{Throughout|During|All along} #if{@sex == 'm'|his|her} career, @lastname was nominated @nominations_number time#if{@nominations_number > 1|s} for the oscars and has won @awards_number time#if{@awards_number > 1|s}.|#if{@awards_number > 1 and (@awards_number / @nominations_number) >= 0.5|@lastname is accustomed to win oscars}|@firstname @lastname first movie, "@first_movie_name", was shot in @first_movie_year.|One of #if{@sex == 'm'|his|her} most #random{famous|important|major} #random{film|movie} is @famous_movie_name and has been released in @famous_movie_year. #random{|Indeed, }@famous_movie_name #random{earned|gained|made|obtained} @famous_movie_earn #random{worldwide|#random{across|around} the world}.}
EOF;

$data = [
    [
        'firstname' => 'Leonardo',
        'lastname' => 'DiCaprio',
        'birthdate' => 'November 11, 1974',
        'age' => 41,
        'sex' => 'm',
        'nationality' => 'American',
        'birth_city' => 'Hollywood',
        'birth_country' => 'US',
        'awards_number' => 1,
        'nominations_number' => 6,
        'movies_number' => 37,
        'first_movie_name' => 'Critters 3',
        'first_movie_year' => 1991,
        'famous_movie_name' => 'Titanic',
        'famous_movie_year' => 1997,
        'famous_movie_earn' => '$2,185,372,302'
    ],
    [
        'firstname' => 'Jodie',
        'lastname' => 'Foster',
        'birthdate' => 'November 19, 1962',
        'age' => 51,
        'sex' => 'f',
        'nationality' => 'American',
        'birth_city' => 'Los Angeles',
        'birth_country' => 'US',
        'awards_number' => 2,
        'nominations_number' => 4,
        'movies_number' => 75,
        'first_movie_name' => 'My Sister Hank',
        'first_movie_year' => 1972,
        'famous_movie_name' => 'Taxi Driver',
        'famous_movie_year' => 1976,
        'famous_movie_earn' => '$28,262,574'
    ],
];

$tg = new TextGenerator();
$tg->setTemplate($template);

foreach($data as $actorData) {
    echo $tg->generate($actorData) . "\n\n";
}