# Style guidelines

## Technical requirements
- All files **must** be encoded using UTF-8 (without BOM)
- Use only Unix line endings

## Line lenghts
Please keep every line in XML file 80 characters long. This is loose requirement and 100 is probably acceptable as
maximum length. This allow to keep *diffs* simple and useful for translators, so follow this rule carefully.

## Whitespaces
Indent every block level with one space. Do not use tabs. Only examples are using four spaces as indentation level,
accordingly to [PEAR Coding Standards](http://pear.php.net/manual/en/standards.php).

## Punctuation
Punctuation in the PHP Manual follows regular grammatical rules. When writing flowing sentences, such as in function
descriptions, normal punctuation should be used. Lists, titles, and sentence fragments should not be punctuated with
a period. Sentences need not have two spaces between them. Commas and apostrophes should be used appropriately.

## Personalization
The PHP Manual is a technical document, and should be written so. The use of “you” is rampant in the manual,
and presents an unprofessional image.  The only exceptions to the personalization rule are: the PHP Tutorial and FAQs.

Example:
```
INCORRECT: You can use the optional second parameter to specify tags which should not be stripped.
CORRECT: The optional second parameter may be used to specify tags which should not be stripped.
```

## Chronology
- When referring to a specific version of PHP, "since" should not be used. "As of" should be used in this case.
- In changelogs, newest PHP versions go above the older ones.
- If changelog entry applies to few PHP versions, separate them by a comma, with the lesser version first.
Example: `<entry>5.2.11, 5.3.1</entry>`

## General Grammar
The PHP Manual should be written with particular attention to general English grammar. Contractions should be used
appropriately. Special attention should be applied to sentence construction when using prepositions (ie, sentences
should not end in prepositions).

## PHP Manual Terms
Various non-english, technical terms are used throughout the PHP Manual, without clear indication of their appropriate
spelling. The following list clears up this issue:

Appropriate Use          | Inappropriate Use(s)
-------------------------|--------------------------------------------
any way                  | anyway, anyways
appendices               | appendixes
built-in                 | built in, builtin
command line             | commandline, CLI
email                    | e-mail
example.com              | php.net, google.com
extension                | module
Linux                    | linux, *n*x, *nix, *nux, etc
PHP 5                    | PHP5, PHP-5
PHP 4.3.0                | PHP 4.3, PHP 4.3.0RC2, PHP 5.0.0BETA, PHP 4.3.0PL1
superglobals             | super globals, autoglobals
web server               | webserver
the foo page (as a link) | click here, go here
Unix                     | UNIX (it's a registered trademark)
Windows                  | windows (when referring to Microsoft Windows)