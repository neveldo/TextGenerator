# TextGenerator

TextGenerator is a PHP package that aims to generate automated texts  from data. Feel free to comment and contribute.

## Features

- Text generation from template
- Tags replacement
- Text functions (core functions : random, shuffle, if, loop)
- Nested function calls
- Skip parts that contain empty values to prevent inconsistency in the generated text

## Tags

The tags that appears in the template are replaced by the matching values. Example :

Data :

    ['my_tag' => 'dolor']

Template :

    Lorem @my_tag ipsum

Output :

    Lorem dolor ipsum

## Core functions :

### 'random'

Returns randomly one of the arguments

Template example :

    #random{one|two|three}

Output example :

    two

### 'shuffle'

Returns the arguments shuffled. The first argument is the separator between each others.

Template example :

    #shuffle{ |one|two|three}

Output example :

    two three one

### 'if'

Handle conditions. The first parameter is the condition to check for. The second parameter is returned if the condition is true. The third optional parameter is returned if the condition is false.
Read more about the syntax for the conditions on the [Symfony website](http://symfony.com/doc/current/components/expression_language/syntax.html).
Examples :

    #if{val = 5|the value equals 5}
    #if{val = 5|the value equals 5|the value doesn't equal 5}
    #if{val < 5 or val > 15|the value is lower that 5 or greater that 15|the value is between 5 and 15}
    #if{(val > 5 and val < 15) or (val > 10 and val < 30)|then statement ...|else statement ...}

### 'loop'

Handle loop on a tag that contains an array of multiple data. Arguments list :

 - 1/ The tag that contains an array to loop on
 - 2/ Maximum number of items to loop on ('*' to loop on all elements)
 - 3/ Whether the items should be shuffled or not (true/false)
 - 4/ Separator between each item
 - 5/ Separator for the last item
 - 6/ The template for each item

Example with the tag 'tag_name' that contains the array `[['name' => 'Bill'], ['name' => 'Bob'], ['name' => 'John']]`

    Hello #loop{tag_name|*|true|, | and |dear @name}.
    
It will output : `Hello dear John, dear Bob and dear Bill.`

## Complete example :

Template :

> @firstname @lastname is an @nationality #if{sex == 'm'|actor|actress} of @age years old. #if{sex == 'm'|He|She} was born in @birthdate in @birth_city (@birth_country). #shuffle{ |#random{Throughout|During|All along} #if{sex == 'm'|his|her} career, @lastname was nominated @nominations_number time#if{nominations_number > 1|s} for the oscars and has won @awards_number time#if{awards_number > 1|s}.|#if{awards_number > 1 and (awards_number / nominations_number) >= 0.5|@lastname is accustomed to win oscars.}|@firstname @lastname first movie, "@first_movie_name", was shot in @first_movie_year.|One of #if{sex == 'm'|his|her} most #random{famous|important|major} #random{film|movie} is @famous_movie_name and has been released in @famous_movie_year. #random{|Indeed, }@famous_movie_name #random{earned|gained|made|obtained} @famous_movie_earn #random{worldwide|#random{across|around} the world}. #loop{other_famous_movies|*|true|, | and |@name (@year)} are some other great movies from @lastname.}

Data :

    [
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
            'famous_movie_earn' => '$2,185,372,302',
            'other_famous_movies' => [
                [
                    'name' => 'Catch Me If You Can',
                    'year' => 2002
                ],
                [
                    'name' => 'Shutter Island',
                    'year' => 2010
                ],
                [
                    'name' => 'Inception',
                    'year' => 2010
                ],
            ]
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
            'famous_movie_earn' => '$28,262,574',
            'other_famous_movies' => [
                [
                    'name' => 'The Silence of the Lambs',
                    'year' => 1991
                ],
                [
                    'name' => 'Contact',
                    'year' => null // Empty values are skipped by the parser
                ],
                [
                    'name' => 'The Accused',
                    'year' => 1988
                ],
            ]
        ],
    ]

Output :

> Leonardo DiCaprio is an American actor of 41 years old. He was born in November 11, 1974 in Hollywood (US). During his career, DiCaprio was nominated 6 times for the oscars and has won 1 time. One of his most famous film is Titanic and has been released in 1997. Indeed, Titanic gained $2,185,372,302 worldwide. Catch Me If You Can (2002), Inception (2010) and Shutter Island (2010) are some other great movies from DiCaprio. Leonardo DiCaprio first movie, "Critters 3", was shot in 1991.

> Jodie Foster is an American actress of 51 years old. She was born in November 19, 1962 in Los Angeles (US). Jodie Foster first movie, "My Sister Hank", was shot in 1972. One of her most important movie is Taxi Driver and has been released in 1976. Indeed, Taxi Driver made $28,262,574 worldwide. The Accused (1988) and The Silence of the Lambs (1991) are some other great movies from Foster. Foster is accustomed to win oscars. All along her career, Foster was nominated 4 times for the oscars and has won 2 times.

## Create a new function

You can extend the TextGenerator capabilities by adding your own text funtions. In order to create a new function for the TextGenerator, you just have to implement the FunctionInterface and call registerFunction() method on the TextGenerator instance. Then, you will be able to call it from your templates.

## Install

    $ composer require neveldo/text-generator
