# TextGenerator

TextGenerator is a PHP package that aims to generate automated texts  from data. Feel free to comment and contribute.

Aside from the PHP package, A [Google Spreadsheet Add-on](https://chrome.google.com/webstore/detail/textgenerator/lbcccnholajhofhlfhkmjjekklkhhjnf) is available on the Chrome webstore. It gives users the ability to produce automated text contents from data directly within Google Spreadsheet. A complete tutorial for the Spreadsheet Add-on is available [here](https://medium.com/@neveldo/tutorial-turn-your-data-into-narratives-using-textgenerator-wikidata-and-google-spreadsheet-b85fad31219a#.w8mi43qwh).

## Features

- Text generation from template
- Tags replacement
- Text functions (core functions : random, random with probability, shuffle, if, loop, variables assignment, ...)
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

## Template indentation

Use the ';;' special marker to indent your template. This marker can be inserted at any position in the template, any space characters (including tabs and line breaks) following the marker will be removed.

Template :

    Quare hoc quidem praeceptum, cuiuscumque est. ;;
        ad tollendam amicitiam valet. ;;
            potius praecipiendum fuit. ;;
    ut eam diligentiam adhiberemus.

Output :

    Quare hoc quidem praeceptum, cuiuscumque est. ad tollendam amicitiam valet. potius praecipiendum fuit. ut eam diligentiam adhiberemus.

## Core functions :

### 'set'

Set new tag within the template in order to be used further.

Data :

    [
        [
            'sex' => 'f',
        ]
    ]

Template example :

    #set{@who|#if{sex == 'm'|boy|girl}};;
    #set{@hello|#random{Hello,Goodbye,Hi}};;
    @hello @who

Output example :

    Hi girl

### 'if'

Display text depending on a condition. The first parameter is the condition to check for. The second parameter is returned if the condition is true. The third optional parameter is returned if the condition is false.
Read more about the syntax for the conditions on the [Symfony website](http://symfony.com/doc/current/components/expression_language/syntax.html).
Examples :

    #if{@val == 5|the value equals 5}
    #if{@val == 5|the value equals 5|the value doesn't equal 5}
    #if{@val < 5 or @val > 15|the value is lower that 5 or greater that 15|the value is between 5 and 15}
    #if{(@val > 5 and @val < 15) or (@val > 10 and @val < 30)|then statement ...|else statement ...}

### 'expr'

Returns the evaluated expression. Read more about the syntax for the expressions on the [Symfony website](http://symfony.com/doc/current/components/expression_language/syntax.html).
Examples :

    #expr{@age - (@current_year - @first_movie_year)}
    #expr{(@value / @population) * 100}

### 'filter'

Filter the arguments in order to ouput a result, here are some examples :

    #filter{upperfirst|@word} will output Lorem (if @word = lorem)
    #filter{round|@value|2} will output 56.23 (if @value = 56.23346)
    #filter{timestamp|d/m/Y|1470339485} will output 04/08/2016
    #filter{timestamp|Y} will output 2016
    #filter{date|2016-08-09|Y-m-d|d/m/Y} will output 09/08/2016
    #filter{number|@value} will output 564,564 (if @value = 564564)
    #filter{number|@value|0|,| } will output 564 564 (if @value = 564564)

Available filters : round, ceil, floor, max, min, rand, number, lower, upper, lowerfirst, upperfirst, upperwords, trim, substring, timestamp, date. 
For the filters directly mapped to PHP functions, you can get more information with the PHP documentation.
Custom filters can easily be added through the FilterFunction::addFilter() method.

### 'loop'

Handle loop on a tag that contains an array of multiple data. Arguments list :

 - 1/ The tag that contains an array to loop on
 - 2/ Maximum number of items to loop on ('*' to loop on all elements)
 - 3/ Whether the items should be shuffled or not (true/false)
 - 4/ Separator between each item
 - 5/ Separator for the last item
 - 6/ The template for each item

Example with the tag 'tag_name' that contains the array `[['name' => 'Bill'], ['name' => 'Bob'], ['name' => 'John']]`

    Hello #loop{@tag_name|*|true|, | and |dear @name}.
    
It will output : `Hello dear John, dear Bob and dear Bill.`

### 'random'

Return randomly one of the arguments

Template example :

    #random{first option|second option|third option}

Output example :

    second option

### 'prandom'

Return randomly one of the arguments, taking account of the probability set to each value. In the example below, the first parameter 'one' will have 80% of chance to be output.

Template example :

    #random{80:first option|10:second option|10:third option}

Output example :

    first option

### 'shuffle'

Return the arguments shuffled. The first argument is the separator between each others.

Template example :

    #shuffle{ |one|two|three}

Output example :

    two three one

### 'choose'

Return the chosen argument among the list. The first argument is the ID of the argument to output (starting from 1).

Data :

    [
        [
            'my_choice' => 2,
        ]
    ]

Template example :

    #choose{1|one|two|three} #choose{@my_choice|one|two|three}

Output example :

    one two

For instance, 'choose' function can be used in combination with 'set' function :

Template example :

    #set{my_choice|#random{1,2,3}};;
    Lorem #choose{@my_choice|one|two|three} ipsum #choose{@my_choice|first|second|third}

Output example :

    Lorem two ipsum second

## Complete example :

Template :

    #set{@pronoun|#if{@sex == 'm'|He|She}};;
    @firstname @lastname is an @nationality #if{@sex == 'm'|actor|actress} of @age years old. ;;
    @pronoun was born in @birthdate in @birth_city (@birth_country). ;;
    #shuffle{ |;;
        #random{Throughout|During|All along} #if{sex == 'm'|his|her} career, #random{@pronoun|@lastname} was nominated @nominations_number time#if{@nominations_number > 1|s} for the oscars and has won @awards_number time#if{@awards_number > 1|s}.|;;
        #if{@awards_number > 1 and (@awards_number / @nominations_number) >= 0.5|@lastname is accustomed to win oscars.}|;;
        @firstname @lastname first movie, "@first_movie_name", was shot in @first_movie_year (at #expr{@age - (#filter{timestamp|Y} - @first_movie_year)} years old).|;;
        One of #if{@sex == 'm'|his|her} most #random{famous|important|major} #random{film|movie} is @famous_movie_name and has been released in @famous_movie_year. ;;
            #prandom{20:|80:Indeed, }@famous_movie_name #random{earned|gained|made|obtained} $#filter{number|@famous_movie_earn} #random{worldwide|#random{across|around} the world}. ;;
            #loop{@other_famous_movies|*|true|, | and |@name (@year)} are some other great movies from @lastname.;;
    }

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
            'famous_movie_earn' => '2185372302',
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
            'famous_movie_earn' => '28262574',
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

> Leonardo DiCaprio is an American actor of 41 years old. He was born in November 11, 1974 in Hollywood (US). One of his most famous film is Titanic and has been released in 1997. Indeed, Titanic obtained $2,185,372,302 around the world. Catch Me If You Can (2002), Inception (2010) and Shutter Island (2010) are some other great movies from DiCaprio. Leonardo DiCaprio first movie, "Critters 3", was shot in 1991 (at 16 years old). All along his career, He was nominated 6 times for the oscars and has won 1 time.

> Jodie Foster is an American actress of 51 years old. She was born in November 19, 1962 in Los Angeles (US). Foster is accustomed to win oscars. One of her most important film is Taxi Driver and has been released in 1976. Indeed, Taxi Driver obtained $28,262,574 worldwide. The Accused (1988) and The Silence of the Lambs (1991) are some other great movies from Foster. Jodie Foster first movie, "My Sister Hank", was shot in 1972 (at 7 years old). Throughout her career, Foster was nominated 4 times for the oscars and has won 2 times.

## Create a new function

You can extend the TextGenerator capabilities by adding your own text funtions. In order to create a new function for the TextGenerator, you just have to implement the FunctionInterface and call registerFunction() method on the TextGenerator instance. Then, you will be able to call it from your templates.

## Install

    $ composer require neveldo/text-generator
