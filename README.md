Just a POC of a "simple but extensible" generator of automated texts. Feel free to comment and contribute.

It allows to generate automated text from a template and some data (tags).

# Tags

The tags that appears in the template are replaced by the matching values. Example :

Data :

    ['%my_tag%' => 'dolor']

Template :

    Lorem %my_tag% ipsum

Result :

    Lorem dolor ipsum

# Core functions :

## 'random'

Returns randomly one of the arguments

Template example :

    #randon{one|two|three}

Result example :

    two

## 'shuffle'

Returns the arguments shuffled. The first arguments is the separator between each others.

Template example :

    #shuffle{ |one|two|three}

Result example :

    two three one

## 'if'

Handle condition. The first parameter is the condition (allowed tokens : and, or, <, >, <=, >=, <>, =). The second parameter is the "then statement". The third optional parameter is the "else statement".

Template example :

    #if{%val% = 5|the value equals 5}
    #if{%val% = 5|the value equals 5|the value doesn't equal 5}
    #if{%val% < 5 or %val% > 15|the value is lower that 5 or greater that 15|the value is between 5 and 15}
    #if{%val% > 5 and %val% < 15 or %val2% > 10 and %val2% < 30|then statement ...|else statement ...}

## Complete template example :

    #if{%sex% = m|#shuffle{ |%name% est un #random{acteur|comédien|artiste} #if{%age% <= 30|débutant|confirmé} #random{de|agé de|qui a} %age% ans.|%name% #random{est né|a vu le jour} le %birthdate% #random{dans la ville de|à} %birthplace%.}|#shuffle{ |%name% est une #random{actrice|comédienne|artiste} #if{%age% <= 30|débutante|confirmée} #random{de|agé de|qui a} %age% ans.|%name% #random{est née|a vu le jour} le %birthdate% #random{dans la ville de|à} %birthplace%.}}

Result :

> Tom Cruise est un acteur confirmé de 53 ans. Tom Cruise a vu le jour le 3 juillet 1962 à Syracuse.
> Hayden Panettiere a vu le jour le 21 août 1989 dans la ville de Palisades. Hayden Panettiere est une actrice débutante agé de 26 ans.
> Leonardo DiCaprio est un acteur confirmé qui a 41 ans. Leonardo DiCaprio est né le 11 novembre 1974 2 à Los Angeles.
> Meryl Streep est née le 22 juin 1949 à Summit. Meryl Streep est une artiste confirmée agé de 66 ans.

## Create new function

In order to create a new function parser, just implement the ParserInterface and call registerParser() in order add it to the text generator.