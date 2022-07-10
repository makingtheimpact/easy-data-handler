# Easy Data Handler
The Easy Data Handler is a PHP script with functions that offer validation and sanitization for most common data types. 

To use the script, you work with 2 functions - one that validates and one that sanitizes. 

The validation function is intended to simply tell you if the input provided is valid or not - if it's valid, it returns true, and if it's not, then it returns false. 

The sanitization function is intended to prepare input for displaying or storage in a database. Whatever you feed into the function will return a cleaned and sanitized version of that input. 

## How to Use the Easy Data Handler
1. Copy the easy-data-handler.php file and include it with your project. 
2. When you need to call the PHP functions, you call the script in whatever location you put it, so for example: 
[code]
require_once '/inc/easy-data-handler.php';
[/code]
3. To validate input, you call the edh_validate function and to sanitize input, you call the edh_sanitize function. 

### Validation Function
This function validates data based on the type selected. It is simply an indicator of if it passes or not, and it does not state what is wrong. Please see each type above to find out what is allowed and what is not.

    edh_validate($data, $type, $min, $max, $info)

**$data** - the value to be validated  
**$type** - the data type (see above for list)  
**$min** - minimum number of characters required, default 1  
**$max** - maximum number of characters allowed, max of 0 is unlimited, default 0  
**$info** - only used by date and time  
**returns** true or false, true if passed, false if failed  

### Sanitization Function 
This function takes the data its given and changes it IF it fails validation. If it It will remove all invalid characters, put it in the proper format (if needed), and trim the value if set. The returned value should be safe to store in the database as it does not allow any <> () [] type of characters used in scripting or it convers them to html entities.

Note: If the altered value does not match the type, it may use defaults. This only applies to: time

    edh_sanitize($data, $type, $trim, $info)

**$data** - the value to be validated  
**$type** - the data type (see above for list)  
**$trim** - max number of characters to return (discards the rest), 0 is unlimited, default is 0  
**$info** - only used by phone, date, time, and ip  
**returns** sanitized value  

## Future Versions Will...
* Add new text input validation and sanitization that allows for apostrophes
* Feature examples and templates
