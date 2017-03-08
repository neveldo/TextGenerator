# ChangeLog
Change log for TextGenerator

## 1.0.0 - March 8, 2017
- Add coalesce function
- Handle properly UTF-8 characters
- Remove whitespaces around variable name in set function

## 0.4.0 - September 12, 2016
- Sort the function calls tree from left to right and from bottom to up
- Add 'substring' filter to FilterFunction
- Fix error when a filter doesn't exist
- Fix set function when the variable name contains another existing variable name
- Return an error in date filter is used with a wrong format

## 0.3.0 - September 05, 2016

- Add new #choose{} function
- Add new #prandom{} function
- Add new #expr{} function
- Allow variables assignment through #set{} function
- Update statements execution order to allow variables assignment
- Use the special caracter '@' for tags in every cases
- Allow text to be indented with ';;'

## 0.2.0 - June 01, 2016

- Misc typo fixes

## 0.1.0 - April 18, 2016

- Initial version
