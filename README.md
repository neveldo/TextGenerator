TextGenerator is a tool that aims to automate the generation of text from data by using a template. 
In the template, you can use tags and call functions.
Feel free to comment and contribute.

# Tags

The tags that appears in the template are replaced by the matching values. Example :

Data :

    ['my_tag' => 'dolor']

Template :

    Lorem @my_tag ipsum

Output :

    Lorem dolor ipsum

# Core functions :

## 'random'

Returns randomly one of the arguments

Template example :

    #randon{one|two|three}

Output example :

    two

## 'shuffle'

Returns the arguments shuffled. The first argument is the separator between each others.

Template example :

    #shuffle{ |one|two|three}

Output example :

    two three one

## 'if'

Handle conditions. The first parameter is the condition to check for. The second parameter is returned if the condition is true. The third optional parameter is returned if the condition is false.
Read more about the syntax for the conditions on the [Symfony website](http://symfony.com/doc/current/components/expression_language/syntax.html).
Examples :

    #if{@val = 5|the value equals 5}
    #if{@val = 5|the value equals 5|the value doesn't equal 5}
    #if{@val < 5 or @val > 15|the value is lower that 5 or greater that 15|the value is between 5 and 15}
    #if{@val > 5 and @val < 15 or@val > 10 and @val < 30|then statement ...|else statement ...}

## Complete example :

Template :

> @firstname @lastname is an @nationality #if{@sex == 'm'|actor|actress} of @age years old. #if{@sex == 'm'|He|She} was born in @birthdate in @birth_city (@birth_country). #shuffle{ |#random{Throughout|During|All along} #if{@sex == 'm'|his|her} career, @lastname was nominated @nominations_number time#if{@nominations_number > 1|s} for the oscars and has won @awards_number time#if{@awards_number > 1|s}.|#if{@awards_number > 1 and (@awards_number / @nominations_number) >= 0.5|@lastname is accustomed to win oscars}|@firstname @lastname first movie, "@first_movie_name", was shot in @first_movie_year.|One of #if{@sex == 'm'|his|her} most #random{famous|important|major} #random{film|movie} is @famous_movie_name and has been released in @famous_movie_year. #random{|Indeed, }@famous_movie_name #random{earned|gained|made|obtained} @famous_movie_earn #random{worldwide|#random{across|around} the world}.}

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
    ]

Output :

> Leonardo DiCaprio is an American actor of 41 years old. He was born in November 11, 1974 in Hollywood (US). All along his career, DiCaprio was nominated 6 times for the oscars and has won 1 time. One of his most famous movie is Titanic and has been released in 1997. Titanic earned $2,185,372,302 across the world. Leonardo DiCaprio first movie, "Critters 3", was shot in 1991. 

> Jodie Foster is an American actress of 51 years old. She was born in November 19, 1962 in Los Angeles (US). Jodie Foster first movie, "My Sister Hank", was shot in 1972. Throughout her career, Foster was nominated 4 times for the oscars and has won 2 times. Foster is accustomed to win oscars One of her most important film is Taxi Driver and has been released in 1976. Taxi Driver made $28,262,574 around the world.

## Create a new function

You can extend the TextGenerator capabilities by adding your own text funtions. In order to create a new function for the TextGenerator, you just have to implement the FunctionInterface and call registerFunction() method on the TextGenerator instance. Then, you will be able to call it from your templates.