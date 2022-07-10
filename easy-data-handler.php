<?php
/*
Easy Data Handler
This plugin adds a series of functions that are used for validating, sanitizing, and displaying common data types. This is a set of functions to be used to work with other plugins and themes.
1.0.0
*/

/*
VALIDATE, SANITIZE, and DISPLAY FUNCTIONS

Validation:
This function returns true if it passes validation and false if it fails.

Sanitization:
This function returns sanitized values that are safe to store in database and display. First it sees if the value submitted validates for that format, if it doesn't it removes invalid characters.
*** Note: if the safe value still does not validate, it may be set to a default value. If you don't want it to be changed, then use text. ***

Display:
These functions take values and prepares them for display on the website.
Currently only supports US phone numbers and text.

DATA TYPES:

text - single line of text (default) - any text except html or php tags
multitext - multiline text

letters1a - A-Z, a-z, whitespace
letters1b - A-Z, a-z, no whitespace

alphanum1a - A-Z, a-z, 0-9, whitespace
alphanum1b - A-Z, a-z, 0-9, no whitespace
alphanum2a - A-Z, a-z, 0-9, -, _, ., whitespace
alphanum2b - A-Z, a-z, 0-9, -, _, ., no whitespace

num - 0-9, no whitespace (positive integer)
numneg - 0-9, -, no whitespace (positive or negative integer)

numdec - 0-9, ., no whitespace (positive decimal number)
numdecneg - 0-9, ., -, no whitespace (positive or negative decimal number)

email - email address
phone - US phone number
intphone - international phone number

money - number with 2 decimal places (eg. 123.45)
currency - US currency with 2 decimal places (eg. $123.45)

date - any valid date format
time - any valid time format

url - website address (eg. domainname.com or https://domainname.com)

password - 1 lowercase letter, 1 uppercase letter, 1 number, and be at least 8 characters long
passwordstrong - 1 lowercase letter, 1 uppercase letter, 1 number, 1 special character and be at least 8 characters long

ip (both ipv4 and ipv6) - IP address ###.###.###.### (ipv4))
                                     ####:####:####:####:####:####:####:#### (ipv6)

For the following fields, use the following data types:
name - text, letters1a, or alphanum2a
address - text or alphanum2a
city - text, letters1a, or alphanum2a
state - text, letters1a, or alphanum2a
country - text, letters1a, or alphanum2a
zip/postal code - alphanum1a or alphanum2b
username - letters1a, alphanum1b, or alphanum2b
email - email
message - multitext
phone - phone or intphone
currency - money or currency
ip address - ip

*/

// VALIDATION
/*
  This function validates data based on the type selected. It is simply an indicator of if it passes or not, and it does not state what is wrong. Please see each type above to find out what is allowed and what is not.
  $data - the value to be validated
  $type - the data type (see above for list)
  $min - minimum number of characters required, default 1
  $max - maximum number of characters allowed, max of 0 is unlimited, default 0
  $info - only used by date and time
  returns true or false, true if passed, false if failed
*/
function edh_validate($data = '', $type = 'text', $min = 0, $max = 0, $info = '') {
  $isvalid = false; // default fail

  if ($data != '' && $data != null):

    // Safety net - Set default values if not set
    if ($type == '' || $type == null):
      $type = 'text';
    endif;
    if ($min == '' || $min == null):
      $min = 1;
    endif;
    if ($max == '' || $max == null):
      $max = 0;
    endif;

    // Switch statement to check for data type
    switch($type) {
      // text - single line of text (default)
      case 'text':
        // runs $data through strip_tags () then compares it to original value
        // This allows for any characters - but it detects any html or php code and removes it
                    $sanitized = wp_strip_all_tags($data, $remove_breaks = true); // removes html and php code
                    if ($data == $sanitized):
                      $isvalid = true;
                    endif;
                    break;
      // multitext - multiline text
      case 'multitext':
        // runs $data through strip_tags () then compares it to original value
        // This allows for any characters - but it detects any html or php code and removes it
                    $sanitized = wp_strip_all_tags($data, $remove_breaks = false);
                    if (rtrim($data) == $sanitized):
                      $isvalid = true;
                    endif;
                    break;
      // letters1a - A-Z, a-z, whitespace
      case 'letters1a': // Regex
                    $pattern = "/^[a-zA-Z ]*$/";
                    if (preg_match($pattern, $data)) :
                      $isvalid = true;
                    endif;
                    break;
      // letters1b - A-Z, a-z, no whitespace
      case 'letters1b': // Regex
                    $pattern = "/^\d+$/";
                    if (preg_match($pattern, $data)) :
                      $isvalid = true;
                    endif;
                    break;
      // alphanum1a - A-Z, a-z, 0-9, whitespace
      case 'alphanum1a': // Regex
                    $pattern = "/^[a-zA-Z0-9 ]*$/";
                    if (preg_match($pattern, $data)) :
                      $isvalid = true;
                    endif;
                    break;
      // alphanum1b - A-Z, a-z, 0-9, no whitespace
      case 'alphanum1b': // Regex
                    $pattern = "/^[a-zA-Z0-9]*$/";
                    if (preg_match($pattern, $data)) :
                      $isvalid = true;
                    endif;
                    break;
      // alphanum2a - A-Z, a-z, 0-9, -, _, ., whitespace
      case 'alphanum2a': // Regex
                    $pattern = "/^[a-zA-Z0-9 .\-_]*$/";
                    if (preg_match($pattern, $data)) :
                      $isvalid = true;
                    endif;
                    break;
      // alphanum2b - A-Z, a-z, 0-9, -, _, ., no whitespace
      case 'alphanum2b': // Regex
                    $pattern = "/^[a-zA-Z0-9.\-_]*$/";
                    if (preg_match($pattern, $data)) :
                      $isvalid = true;
                    endif;
                    break;
      // num - whole numbers (positive)
      case 'num': // Regex
                    $pattern = "/^\d+$/";
                    if (preg_match($pattern, $data)) :
                      $isvalid = true;
                    endif;
                    break;
      // numneg - whole numbers (positive or negative)
      case 'numneg': // Regex
                    $pattern = "/^-?\d+$/";
                    if (preg_match($pattern, $data)) :
                      $isvalid = true;
                    endif;
                    break;
      // numdec - whole and decimal numbers
      case 'numdec': // Regex
                    $pattern = "/^\d*(\.\d+)?$/";
                    if (preg_match($pattern, $data)) :
                      $isvalid = true;
                    endif;
                    break;
      // numdecneg - whole, decimal, and negative numbers
      case 'numdecneg': // Regex
                    $pattern ="/^-?\d*(\.\d+)?$/";
                    if (preg_match($pattern, $data)) :
                      $isvalid = true;
                    endif;
                    break;
      // email - email address
      case 'email':
                    $sanitized = sanitize_email($data);
                    if ($data == $sanitized):
                      $isvalid = true;
                    endif;
                    break;
      // phone - US phone number
      case 'phone': // Regex
                    $tel = array();
                    // ###-###-####
                    $tel[0] = "/^[0-9]{3}[\-]{1}[0-9]{3}[\-]{1}[0-9]{4}$/";
                    // ###.###.####
                    $tel[1] = "/^[0-9]{3}[\.]{1}[0-9]{3}[\.]{1}[0-9]{4}$/";
                    // ### ### ####
                    $tel[2] = "/^[0-9]{3}[ ]{1}[0-9]{3}[ ]{1}[0-9]{4}$/";
                    // (###) ###-####
                    $tel[3] = "/^[(]{1}[0-9]{3}[)]{1}[ ]?[0-9]{3}[\-]{1}[0-9]{4}$/";
                    // ##########
                    $tel[4] = "/^[0-9]{10}$/";
                    foreach($tel as $reg):
                      if (preg_match($reg, $data)) :
                        $isvalid = true;
                      endif;
                    endforeach;
                    break;
      // intphone - international phone number
      case 'intphone': // Regex
                    $pattern = "/^[0-9 .\+\-()]*$/";
                    if (preg_match($pattern, $data)) :
                      $isvalid = true;
                    endif;
                    break;
      // money - number with 2 decimal places (eg. 123 or 123.45)
      case 'money': // Regex
                    $pattern = "/^[0-9]+[.]?[0-9]{2}$/";
                    if (preg_match($pattern, $data)) :
                      $isvalid = true;
                    endif;
                    break;
      // currency - US currency with 2 decimal places (eg. $123.45)
      case 'currency': // Regex
                    $pattern = "/^[$][0-9]+[.][0-9]{2}$/";
                    if (preg_match($pattern, $data)) :
                      $isvalid = true;
                    endif;
                    break;
      // date - ensures date entered is valid
        // year values allowed: 1000-2999
      case 'date': // Regex
                      switch ($info) {
                        // m/d/Y
                        case 0:
                                            $pattern = "/((0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01])\/[12]\d{3})/";
                                            break;
                        // m-d-Y
                        case 1:
                                            $pattern = "/((0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])-[12]\d{3})/";
                                            break;
                        // d/m/Y
                        case 2:
                                            $pattern = "/((0[1-9]|[12]\d|3[01])\/(0[1-9]|1[0-2])\/[12]\d{3})/";
                                            break;
                        // d-m-Y
                        case 3:
                                            $pattern = "/((0[1-9]|[12]\d|3[01])-(0[1-9]|1[0-2])-[12]\d{3})/";
                                            break;
                        // mysql datetime YYYY-MM-DD HH:MM:SS
                        case 4:
                                            $pattern = "/([12]\d{3})-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]) (?:[01]\d|2[0123]):(?:[012345]\d):(?:[012345]\d)/";
                                            break;
                        // mysql date YYYY-MM-DD
                        case 5:
                                            $pattern = "/([12]\d{3})-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])/";
                                            break;
                        default: // mm/dd/yyyy
                                            $pattern = "/((0[1-9]|1[0-2])\/(0[1-9]|[12]\d|3[01])\/[12]\d{3})/";
                                            break;
                      }
                      if (preg_match($pattern, $data)) :
                        $isvalid = true;
                      endif;
                    break;
        // time
        case 'time': // Regex
                        switch ($info) {
                          case 'HH:MM':
                          // Time Format HH:MM 12-hour, optional leading 0
                                              $pattern = "/^(0?[1-9]|1[0-2]):[0-5][0-9]$/";
                                              break;
                          case 'HH:MM AM':
                           // Time Format HH:MM 12-hour, optional leading 0, Meridiems (AM/PM)
                                              $pattern = "/((1[0-2]|0?[1-9]):([0-5][0-9]) ?([AaPp][Mm]))/";
                                              break;
                          case 'HH:MM:SS':
                          // Time Format HH:MM:SS 12-hour, optional leading 0
                                              $pattern = "/((1[0-2]|0?[1-9]):([0-5][0-9]):(?:[012345]\d))/";
                                              break;
                          case 'HH:MM:SS AM':
                          // Time Format HH:MM:SS 12-hour, optional leading 0, Meridiems (AM/PM)
                                              $pattern = "/((1[0-2]|0?[1-9]):([0-5][0-9]):(?:[012345]\d) ?([AaPp][Mm]))/";
                                              break;
                          case 'HH:MM 24':
                          // Time Format HH:MM 24-hour, optional leading 0
                                              $pattern = "/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/";
                                              break;
                          case 'HH:MM:SS 24':
                          //Time Format HH:MM:SS 24-hour
                                              $pattern = "/(?:[01]\d|2[0123]):(?:[012345]\d):(?:[012345]\d)/";
                                              break;
                          default: // default HH:MM 12-hour
                                              $pattern = "/^(0?[1-9]|1[0-2]):[0-5][0-9]$/";
                                              break;
                        }
                        if (preg_match($pattern, $data)) :
                          $isvalid = true;
                        endif;
                      break;
      // url - website address (eg. domainname.com or https://domainname.com)
      case 'url':
                    $sanitized = esc_url_raw($data);
                    if ($data == $sanitized):
                      $isvalid = true;
                    endif;
                    break;
      // passwordweak - 1 lowercase letter, 1 uppercase letter, 1 number, and be at least 8 characters long
        // special characters: .* ^&$@#:=+-_~!?%
      case 'password':
                    // this checks $data for required elements and makes sure it's safe to store
                    $pattern_upper = "/^.*(?=\S*[A-Z]).*$/";
                    $pattern_lower = "/^.*(?=\S*[a-z]).*$/";
                    $pattern_num = "/^.*(?=\S*[0-9]).*$/";
                    $pattern_chars = "/^[a-zA-Z0-9.* ^&$@#:=+\-_~!?%]+$/";

                    $uppercase = preg_match($pattern_upper, $data);
                    $lowercase = preg_match($pattern_lower, $data);
                    $number    = preg_match($pattern_num, $data);
                    $valid_char = preg_match($pattern_chars, $data);

                    if($uppercase && $lowercase && $number && $valid_char && strlen($data) > 7) {
                      $isvalid = true;
                    }
                    break;
      // passwordstrong - 1 lowercase letter, 1 uppercase letter, 1 number, 1 special character and be at least 8 characters long
        // special characters:
      case 'passwordstrong': // Regex
                    // this checks $data for required elements and makes sure it's safe to store
                    $pattern_upper = "/^.*(?=\S*[A-Z]).*$/";
                    $pattern_lower = "/^.*(?=\S*[a-z]).*$/";
                    $pattern_num = "/^.*(?=\S*[0-9]).*$/";
                    $pattern_special = "/^.*(?=\S*[.* ^&$@#:=+\-_~!?%]).*$/";
                    $pattern_chars = "/^[a-zA-Z0-9.* ^&$@#:=+\-_~!?%]+$/";

                    $uppercase = preg_match($pattern_upper, $data);
                    $lowercase = preg_match($pattern_lower, $data);
                    $number    = preg_match($pattern_num, $data);
                    $special   = preg_match($pattern_special, $data);
                    $valid_char = preg_match($pattern_chars, $data);

                    if($uppercase && $lowercase && $number && $special && $valid_char && strlen($data) > 7) {
                      $isvalid = true;
                    }
                    break;



                    $pattern = "/(?=(.*[0-9]))(?=.*[\!@#$%^&*()\\[\]{}\-_+=~`|:;\"'<>,./?])(?=.*[a-z])(?=(.*[A-Z]))(?=(.*)).{8,}/";
                    if (preg_match($pattern, $data)) :
                      $isvalid = true;
                    endif;




                    break;
      // ipadd (both ipv4 and ipv6) - IP address
      case 'ip': // Regex
                    $ipv4_pattern = "/^(?:(?:^|\.)(?:2(?:5[0-5]|[0-4]\d)|1?\d?\d)){4}$/";
                    $ipv6_pattern = "/(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))/";
                    // is valid ipv4
                    if (preg_match($ipv4_pattern, $data)) :
                      $isvalid = true;
                    endif;
                    if (preg_match($ipv6_pattern, $data)) :
                      $isvalid = true;
                    endif;
                    break;
      // default is text
      default:
                    $sanitized = sanitize_text_field($data);
                    if ($data == $sanitized):
                      $isvalid = true;
                    endif;
                    break;
    }
  endif;

  // check length
  if ($isvalid == true):
    // get length of $data
    $datalength = strlen($data);
    if ($min > 0):
      $setmin = $min - 1;
      if ($datalength < $setmin):
        $isvalid = false;
      endif;
    endif;
    if ($max > 0):
      $setmax = $max - 1;
      if ($datalength > $setmax):
        $isvalid = false;
      endif;
    endif;
  endif;

  return $isvalid;
}

// SANITIZATION
/*
  This function takes the data its given and changes it IF it fails validation. If it It will remove all invalid characters, put it in the proper format (if needed), and trim the value if set. The returned value should be safe to store in the database as it does not allow any <> () [] type of characters used in scripting or it convers them to html entities.
  If the altered value does not match the type, it may use defaults. This only applies to: time
  $data - the value to be validated
  $type - the data type (see above for list)
  $trim - max number of characters to return (discards the rest), 0 is unlimited, default is 0
  $info - only used by phone, date, time, and ip
  returns sanitized value
*/
function edh_sanitize($data = '', $type = 'text', $trim = 0, $info = '') {
  $clean_data = '';

  if ($data != '' && $data != null):
    // set default values
    if ($type == '' || $type == null):
      $type = 'text';
    endif;
    if ($trim == '' || $trim == null):
      $trim = 0;
    endif;

    switch($type) {
      // text - single line of text (default)
      case 'text':
                          $clean_data = sanitize_text_field($data);
                          break;
      // multitext - multiline text
      case 'multitext':
                          $clean_data = sanitize_textarea_field($data);
                          break;
      // letters1a - A-Z, a-z, whitespace
      case 'letters1a':
                          if (edh_validate($data, 'letters1a')) :
                            $clean_data = $data; // data is already valid
                          else :
                            $pattern = "/[^A-Za-z ]/";
                            $clean_data = preg_replace($pattern, '', $data);
                          endif;
                          break;
      // letters1b - A-Z, a-z, no whitespace
      case 'letters1b':
                          if (edh_validate($data, 'letters1b')) :
                            $clean_data = $data; // data is already valid
                          else :
                            $pattern = "/[^A-Za-z]/";
                            $clean_data = preg_replace($pattern, '', $data);
                          endif;
                          break;
      // alphanum1a - A-Z, a-z, 0-9, whitespace
      case 'alphanum1a':
                          if (edh_validate($data, 'alphanum1a')) :
                            $clean_data = $data; // data is already valid
                          else :
                            $pattern = "/[^a-zA-Z0-9 ]/";
                            $clean_data = preg_replace($pattern, '', $data);
                          endif;
                          break;
      // alphanum1b - A-Z, a-z, 0-9, no whitespace
      case 'alphanum1b':
                          if (edh_validate($data, 'alphanum1b')) :
                            $clean_data = $data; // data is already valid
                          else :
                            $pattern = "/[^a-zA-Z0-9]/";
                            $clean_data = preg_replace($pattern, '', $data);
                          endif;
                          break;
      // alphanum2a - A-Z, a-z, 0-9, -, _, ., whitespace
      case 'alphanum2a':
                          if (edh_validate($data, 'alphanum2a')) :
                            $clean_data = $data; // data is already valid
                          else :
                            $pattern = "/[^a-zA-Z0-9 .\-_]/";
                            $clean_data = preg_replace($pattern, '', $data);
                          endif;
                          break;
      // alphanum2b - A-Z, a-z, 0-9, -, _, ., no whitespace
      case 'alphanum2b':
                          if (edh_validate($data, 'alphanum2b')) :
                            $clean_data = $data; // data is already valid
                          else :
                            $pattern = "/[^a-zA-Z0-9.\-_]/";
                            $clean_data = preg_replace($pattern, '', $data);
                          endif;
                          break;
      // num - whole numbers (positive)
      case 'num':
                          if (edh_validate($data, 'num')) :
                            $clean_data = $data; // data is already valid
                          else :
                            $pattern = "/[^\d+]/";
                            $clean_data = preg_replace($pattern, '', $data);
                          endif;
                          break;
      // numneg - whole numbers (positive or negative)
      case 'numneg':
                          if (edh_validate($data, 'numneg')) :
                            $clean_data = $data; // data is already valid
                          else :
                            $pattern = "/[^-?\d+]/";
                            $clean_data = preg_replace($pattern, '', $data);
                          endif;
                          break;
      // numdec - whole and decimal numbers
      case 'numdec':
                          if (edh_validate($data, 'numdec')) :
                            $clean_data = $data; // data is already valid
                          else :
                            $pattern = "/[^\d*(\.\d+)?]/";
                            $clean_data = preg_replace($pattern, '', $data);
                          endif;
                          break;
      // numdecneg - whole, decimal, and negative numbers
      case 'numdecneg':
                          if (edh_validate($data, 'numdecneg')) :
                            $clean_data = $data; // data is already valid
                          else :
                            $pattern = "/[^-?\d*(\.\d+)?]/";
                            $clean_data = preg_replace($pattern, '', $data);
                          endif;
                          break;
      // email - email address
      case 'email':
                          if (edh_validate($data, 'email')) :
                            $clean_data = $data;
                          else:
                            $clean_data = sanitize_email($data);
                          endif;
                          break;
      // phone - US phone number
      case 'phone':
                          if (edh_validate($data, 'phone')) :
                            $clean_data = $data; // data is already valid
                          else :
                            // remove all characters except numbers
                            $pattern = "/[^0-9]/";
                            $clean_data = preg_replace($pattern, '', $data);

                            // Break number into sections
                            $part1 = substr($clean_data, 0, 3);
                            $part2 = substr($clean_data, 3, 3);
                            $part3 = substr($clean_data, 6, 4);

                            switch ($info) {
                              // ###-###-####
                              case 0: $clean_data = $part1 . '-' . $part2 . '-' . $part3;
                                      break;
                              // ###.###.####
                              case 1: $clean_data = $part1 . '.' . $part2 . '.' . $part3;
                                      break;
                              // ### ### ####
                              case 2: $clean_data = $part1 . ' ' . $part2 . ' ' . $part3;
                                      break;
                              // (###) ###-####
                              case 3: $clean_data =  '(' . $part1 . ') ' . $part2 . '-' . $part3;
                                      break;
                              // ##########
                              case 4: $clean_data = $part1 . $part2 . $part3;
                                      break;
                              // (###) ###-####
                              default: $clean_data =  '(' . $part1 . ') ' . $part2 . '-' . $part3;
                                      break;
                            }
                            // if the cleaned value is not a proper phone number, return a default
                            if (!edh_validate($clean_data, 'phone')) :
                              $part1 = '000';
                              $part2 = '000';
                              $part3 =-'0000';
                              switch ($info) {
                                // ###-###-####
                                case 0: $clean_data = $part1 . '-' . $part2 . '-' . $part3;
                                        break;
                                // ###.###.####
                                case 1: $clean_data = $part1 . '.' . $part2 . '.' . $part3;
                                        break;
                                // ### ### ####
                                case 2: $clean_data = $part1 . ' ' . $part2 . ' ' . $part3;
                                        break;
                                // (###) ###-####
                                case 3: $clean_data =  '(' . $part1 . ') ' . $part2 . '-' . $part3;
                                        break;
                                // ##########
                                case 4: $clean_data = $part1 . $part2 . $part3;
                                        break;
                                // (###) ###-####
                                default: $clean_data =  '(' . $part1 . ') ' . $part2 . '-' . $part3;
                                        break;
                              }
                            endif;
                          endif;
                          break;
      // intphone - international phone number
      case 'intphone':
                          if (edh_validate($data, 'intphone')) :
                            $clean_data = $data; // data is already valid
                          else :
                            $pattern = "/[^0-9 .\+\-()]/";
                            $clean_data = preg_replace($pattern, '', $data);
                          endif;
                          break;
      // money - number with 2 decimal places (eg. 123 or 123.45)
      case 'money':
                          if (edh_validate($data, 'money')) :
                            $clean_data = $data; // data is already valid
                          else :
                            // remove all invalid characters
                            $pattern = "/[^0-9.]/";
                            $clean_data = preg_replace($pattern, '', $data);

                            $pattern2 = "/[.+]/"; // find the decimal
                            $pattern3 = "/[^0-9]/"; // numbers only

                            // if the number has a decimal..
                            if (preg_match($pattern2, $clean_data)):
                              // get the position of the decimal
                              $decpos = strpos($clean_data, '.');
                              // get number of char in first part
                              $totalchar = $decpos;
                              // set starting position for second part
                              $position = $decpos + 1;

                              // Break number into sections
                              // if there is no digit before the decimal like .24, make $part1 = 0
                              if ($decpos == 0):
                                $part1 = '0';
                              else:
                                // part before decimal
                                $part1 = substr($clean_data, 0, $totalchar);
                              endif;
                              // part after decimal
                              $part2 = substr($clean_data, $position);
                              // clean second part to remove additional decimals
                              $part2 = preg_replace($pattern3, '', $part2);
                              // put the number back together
                              $clean_data = $part1 . '.' . $part2;
                            endif;
                            // puts the number in the format ###.## or #.##
                            $clean_data = number_format($clean_data, 2, '.', '');
                          endif;
                          break;
      // currency - US currency with 2 decimal places (eg. $123.45)
      case 'currency':
                          if (edh_validate($data, 'currency')) :
                            $clean_data = $data; // data is already valid
                          else :
                            $money = edh_sanitize($data, 'money');
                            $clean_data = '$' . $money;
                          endif;
                          break;
      // date - if invalid date is given, returns current date in desired format
        // year values allowed: 1000-2999
      case 'date':
                        if (edh_validate($data, 'date', 0, 0, $info)) :
                          $clean_data = $data; // data is already valid
                        else :
                          // remove all characters except numbers
                          $pattern = "/[^0-9]/";
                          $clean_data = preg_replace($pattern, '', $data);
                          $clean_data_length = strlen($clean_data);

                          // if the date is the correct length...
                          if ($clean_data_length == 8 || $clean_data_length == 14):
                            // Break number into sections
                            $part1 = substr($clean_data, 0, 2); // mm or dd
                            $part2 = substr($clean_data, 2, 2); // dd or mm
                            $part3 = substr($clean_data, 4, 4); // yyyy

                            switch ($info) {
                              // m/d/Y
                              case 0: $clean_data = $part1 . '/' . $part2 . '/' . $part3;
                                      break;
                              // m-d-Y
                              case 1: $clean_data = $part1 . '-' . $part2 . '-' . $part3;
                                      break;
                              // d/m/Y
                              case 2: $clean_data = $part1 . '/' . $part2 . '/' . $part3;
                                      break;
                              // d-m-Y
                              case 3: $clean_data = $part1 . '-' . $part2 . '-' . $part3;
                                      break;
                              // mysql datetime YYYY-MM-DD HH:MM:SS
                              case 4:
                                      $part1 = substr($clean_data, 0, 4); // yyyy
                                      $part2 = substr($clean_data, 4, 2); // MM
                                      $part3 = substr($clean_data, 6, 2); // DD
                                      $part4 = substr($clean_data, 8, 2); // HH
                                      $part5 = substr($clean_data, 10, 2); // MM
                                      $part6 = substr($clean_data, 12, 2); // SS
                                      $clean_data = $part1 . '-' . $part2 . '-' . $part3 . ' ' . $part4 . ':' . $part5 . ':' . $part6;
                              // mysql date YYYY-MM-DD
                              case 5:
                                      $part1 = substr($clean_data, 0, 4); // yyyy
                                      $part2 = substr($clean_data, 4, 2); // MM
                                      $part3 = substr($clean_data, 6, 2); // DD
                                      $clean_data = $part1 . '-' . $part2 . '-' . $part3;
                              default: $clean_data = $part1 . '/' . $part2 . '/' . $part3;
                                      break;
                            }
                          endif;
                          // if the cleaned date is invalid
                          if (!edh_validate($clean_data, 'date', 0, 0, $info)) :
                            // set to current date based on type
                            $format = '';
                            switch($info) {
                              case 0: $format = 'm/d/Y';
                                      break;
                              case 1: $format = 'm-d-Y';
                                      break;
                              case 2: $format = 'd/m/Y';
                                      break;
                              case 3: $format = 'd-m-Y';
                                      break;
                              case 4: $format = 'Y-m-d H:i:s';
                                      break;
                              case 5: $format = 'Y-m-d';
                                      break;
                              default: $format = 'm/d/Y';
                                      break;
                            }
                            $clean_data = date($format); // current server time
                          endif;
                        endif;
                        break;
      // time
      case 'time':
                        if (edh_validate($data, 'time', 0, 0, $info)) :
                          $clean_data = $data; // data is already valid
                        else :
                          // remove all characters except numbers
                          $pattern = "/[^0-9APM]/";
                          $clean_data = preg_replace($pattern, '', $data);

                          // find the position of the first :
                          $colonpos = strpos($data, ':');
                          if ($colonpos == 1) : // if the position is = 1, then expect #:##:##
                            switch($info) {
                              // HH:MM
                              case 'HH:MM':
                                            // Break number into sections
                                            $part1 = substr($clean_data, 0, 1); // H
                                            $part2 = substr($clean_data, 1, 2); // MM
                                            $part3 = ''; // SS
                                            $part4 = ''; // AM/PM

                                            // If time not valid, set to default 1:00
                                            if ($part1 < 1 || $part1 > 9):
                                              $part1 = 1;
                                            endif;
                                            if ($part2 < 0 || $part2 > 59):
                                              $part2 = '00';
                                            endif;

                                            $clean_data = $part1 . ':' . $part2;
                                            break;
                              // HH:MM AM
                              case 'HH:MM AM':
                                            // Break number into sections
                                            $part1 = substr($clean_data, 0, 1); // H 0
                                            $part2 = substr($clean_data, 1, 2); // MM 1 2
                                            $part3 = substr($clean_data, 3, 2); // AM/PM 3 4
                                            $part4 = ''; // SS

                                            // If time not valid, set to default 1:00 AM
                                            if ($part1 < 1 || $part1 > 9):
                                              $part1 = 1;
                                            endif;
                                            if ($part2 < 0 || $part2 > 59):
                                              $part2 = '00';
                                            endif;
                                            if ($part3 != 'AM' && $part3 != 'PM'):
                                              $part3 = 'AM';
                                            endif;

                                            $clean_data = $part1 . ':' . $part2 . ' ' . $part3;
                                            break;
                              // HH:MM:SS
                              case 'HH:MM:SS':
                                            // Break number into sections
                                            $part1 = substr($clean_data, 0, 1); // H 0
                                            $part2 = substr($clean_data, 1, 2); // MM 1 2
                                            $part3 = substr($clean_data, 3, 2); // SS 3 4
                                            $part4 = ''; // AM/PM

                                            // If time not valid, set to default 1:00:00
                                            if ($part1 < 1 || $part1 > 9):
                                              $part1 = 1;
                                            endif;
                                            if ($part2 < 0 || $part2 > 59):
                                              $part2 = '00';
                                            endif;
                                            if ($part3 < 0 || $part3 > 59):
                                              $part3 = '00';
                                            endif;

                                            $clean_data = $part1 . ':' . $part2 . ':' . $part3;
                                            break;
                              // HH:MM:SS AM
                              case 'HH:MM:SS AM':
                                            // Break number into sections
                                            $part1 = substr($clean_data, 0, 1); // H 0
                                            $part2 = substr($clean_data, 1, 2); // MM 1 2
                                            $part3 = substr($clean_data, 3, 2); // SS 3 4
                                            $part4 = substr($clean_data, 5, 2); // AM/PM 5 6

                                            // If time not valid, set to default 1:00:00 AM
                                            if ($part1 < 1 || $part1 > 9):
                                              $part1 = 1;
                                            endif;
                                            if ($part2 < 0 || $part2 > 59):
                                              $part2 = '00';
                                            endif;
                                            if ($part3 < 0 || $part3 > 59):
                                              $part3 = '00';
                                            endif;
                                            if ($part4 != 'AM' && $part4 != 'PM'):
                                              $part4 = 'AM';
                                            endif;

                                            $clean_data = $part1 . ':' . $part2 . ':' . $part3 . ' ' . $part4;
                                            break;
                              // HH:MM 24
                              case 'HH:MM 24':
                                            // Break number into sections
                                            $part1 = substr($clean_data, 0, 1); // H 0
                                            $part2 = substr($clean_data, 1, 2); // MM 1 2
                                            $part3 = ''; // SS
                                            $part4 = ''; // AM/PM

                                            // If time not valid, set to default 1:00
                                            if ($part1 < 1 || $part1 > 9):
                                              $part1 = 1;
                                            endif;
                                            if ($part2 < 0 || $part2 > 59):
                                              $part2 = '00';
                                            endif;

                                            $clean_data = $part1 . ':'. $part2;
                                            break;
                              // HH:MM:SS 24
                              case 'HH:MM:SS 24':
                                            // Break number into sections
                                            $part1 = substr($clean_data, 0, 1); // H 0
                                            $part2 = substr($clean_data, 1, 2); // MM 1 2
                                            $part3 = substr($clean_data, 3, 2); // SS 3 4
                                            $part4 = ''; // AM/PM

                                            // If time not valid, set to default 1:00:00
                                            if ($part1 < 1 || $part1 > 9):
                                              $part1 = 1;
                                            endif;
                                            if ($part2 < 0 || $part2 > 59):
                                              $part2 = '00';
                                            endif;
                                            if ($part3 < 0 || $part3 > 59):
                                              $part3 = '00';
                                            endif;

                                            $clean_data = $part1 . ':' . $part2 . ':' . $part3;
                                            break;
                              default:
                                            // Break number into sections
                                            $part1 = substr($clean_data, 0, 1); // H 0
                                            $part2 = substr($clean_data, 1, 2); // MM 1 2
                                            $part3 = ''; // SS
                                            $part4 = ''; // AM/PM

                                            // If time not valid, set to default 1:00
                                            if ($part1 < 1 || $part1 > 9):
                                              $part1 = 1;
                                            endif;
                                            if ($part2 < 0 || $part2 > 59):
                                              $part2 = '00';
                                            endif;

                                            $clean_data = $part1 . ':' . $part2;
                                            break;
                            }
                          else: // else expect ##:##:##
                            switch($info) {
                              // HH:MM
                              case 'HH:MM':
                                            // Break number into sections
                                            $part1 = substr($clean_data, 0, 2); // HH
                                            $part2 = substr($clean_data, 2, 2); // MM
                                            $part3 = ''; // SS
                                            $part4 = ''; // AM/PM

                                            // If time not valid, set to default 1:00
                                            if ($part1 < 1 || $part1 > 12):
                                              $part1 = 12;
                                            endif;
                                            if ($part2 < 0 || $part2 > 59):
                                              $part2 = '00';
                                            endif;

                                            $clean_data = $part1 . ':' . $part2;
                                            break;
                              // HH:MM AM
                              case 'HH:MM AM':
                                            // Break number into sections
                                            $part1 = substr($clean_data, 0, 2); // HH 0 1
                                            $part2 = substr($clean_data, 2, 2); // MM 2 3
                                            $part3 = substr($clean_data, 4, 2); // AM/PM 4 5
                                            $part4 = ''; // SS

                                            // If time not valid, set to default 1:00 AM
                                            if ($part1 < 1 || $part1 > 12):
                                              $part1 = 12;
                                            endif;
                                            if ($part2 < 0 || $part2 > 59):
                                              $part2 = '00';
                                            endif;
                                            if ($part3 != 'AM' && $part3 != 'PM'):
                                              $part3 = 'AM';
                                            endif;

                                            $clean_data = $part1 . ':' . $part2 . ' ' . $part3;
                                            break;
                              // HH:MM:SS
                              case 'HH:MM:SS':
                                            // Break number into sections
                                            $part1 = substr($clean_data, 0, 2); // HH 0 1
                                            $part2 = substr($clean_data, 2, 2); // MM 2 3
                                            $part3 = substr($clean_data, 4, 2); // SS 4 5
                                            $part4 = ''; // AM/PM

                                            // If time not valid, set to default 1:00:00
                                            if ($part1 < 1 || $part1 > 12):
                                              $part1 = 12;
                                            endif;
                                            if ($part2 < 0 || $part2 > 59):
                                              $part2 = '00';
                                            endif;
                                            if ($part3 < 0 || $part3 > 59):
                                              $part3 = '00';
                                            endif;

                                            $clean_data = $part1 . ':' . $part2 . ':' . $part3;
                                            break;
                              // HH:MM:SS AM
                              case 'HH:MM:SS AM':
                                            // Break number into sections
                                            $part1 = substr($clean_data, 0, 2); // HH 0 1
                                            $part2 = substr($clean_data, 2, 2); // MM 2 3
                                            $part3 = substr($clean_data, 4, 2); // SS 4 5
                                            $part4 = substr($clean_data, 6, 2); // AM/PM 6 7

                                            // If time not valid, set to default 1:00:00 AM
                                            if ($part1 < 1 || $part1 > 12):
                                              $part1 = 12;
                                            endif;
                                            if ($part2 < 0 || $part2 > 59):
                                              $part2 = '00';
                                            endif;
                                            if ($part3 < 0 || $part3 > 59):
                                              $part3 = '00';
                                            endif;
                                            if ($part4 != 'AM' && $part4 != 'PM'):
                                              $part4 = 'AM';
                                            endif;

                                            $clean_data = $part1 . ':' . $part2 . ':' . $part3 . ' ' . $part4;
                                            break;
                              // HH:MM 24
                              case 'HH:MM 24':
                                            // Break number into sections
                                            $part1 = substr($clean_data, 0, 2); // HH 0 1
                                            $part2 = substr($clean_data, 2, 2); // MM 2 3
                                            $part3 = ''; // SS
                                            $part4 = ''; // AM/PM

                                            // If time not valid, set to default 1:00
                                            if ($part1 < 1 || $part1 > 12):
                                              $part1 = 12;
                                            endif;
                                            if ($part2 < 0 || $part2 > 59):
                                              $part2 = '00';
                                            endif;

                                            $clean_data = $part1 . ':'. $part2;
                                            break;
                              // HH:MM:SS 24
                              case 'HH:MM:SS 24':
                                            // Break number into sections
                                            $part1 = substr($clean_data, 0, 2); // HH 0 1
                                            $part2 = substr($clean_data, 2, 2); // MM 2 3
                                            $part3 = substr($clean_data, 4, 2); // SS 4 5
                                            $part4 = ''; // AM/PM

                                            // If time not valid, set to default 1:00:00
                                            if ($part1 < 1 || $part1 > 12):
                                              $part1 = 12;
                                            endif;
                                            if ($part2 < 0 || $part2 > 59):
                                              $part2 = '00';
                                            endif;
                                            if ($part3 < 0 || $part3 > 59):
                                              $part3 = '00';
                                            endif;

                                            $clean_data = $part1 . ':' . $part2 . ':' . $part3;
                                            break;
                              default:
                                            // Break number into sections
                                            $part1 = substr($clean_data, 0, 2); // HH 0 1
                                            $part2 = substr($clean_data, 2, 2); // MM 2 3
                                            $part3 = ''; // SS
                                            $part4 = ''; // AM/PM

                                            // If time not valid, set to default 1:00
                                            if ($part1 < 1 || $part1 > 12):
                                              $part1 = 12;
                                            endif;
                                            if ($part2 < 0 || $part2 > 59):
                                              $part2 = '00';
                                            endif;

                                            $clean_data = $part1 . ':' . $part2;
                                            break;
                            }
                          endif;
                        endif;
                        break;
      // url - website address (eg. domainname.com or https://domainname.com)
      case 'url':
                        if (edh_validate($data, 'url')) :
                          $clean_data = $data;
                        else :
                          $clean_data = esc_url_raw($data);
                        endif;
                        break;
      // passwordweak - 1 lowercase letter, 1 uppercase letter, 1 number, and be at least 8 characters long
      case 'password':
                        if (edh_validate($data, 'password')) :
                          $clean_data = $data;
                        else :
                          // if the password does not have valid characters
                            // remove invalid characters
                            $pattern = "/[^a-zA-Z0-9.* ^&$@#:=+\-_~!?%]/";
                            $clean_data = preg_replace($pattern, '', $data);
                            // revalidate - if it fails, generate a random password
                            if (!edh_validate($clean_data, 'password')) :
                              $newpassword = '';
                              while(!edh_validate($newpassword, 'password')):
                                $newpassword = edh_generate_password();
                              endwhile;
                              $clean_data = $newpassword;
                            endif;
                        endif;
                        break;
      // passwordstrong - 1 lowercase letter, 1 uppercase letter, 1 number, 1 special character and be at least 8 characters long
      case 'passwordstrong':
                        if (edh_validate($data, 'passwordstrong')) :
                          $clean_data = $data;
                        else :
                          // if the password does not have valid characters
                            // remove invalid characters
                            $pattern = "/[^a-zA-Z0-9.* ^&$@#:=+\-_~!?%]/";
                            $clean_data = preg_replace($pattern, '', $data);
                            // revalidate - if it fails, generate a random password
                            if (!edh_validate($clean_data, 'passwordstrong')) :
                              $newpassword = '';
                              while(!edh_validate($newpassword, 'passwordstrong')):
                                $newpassword = edh_generate_password();
                              endwhile;
                              $clean_data = $newpassword;
                            endif;
                        endif;
                        break;
      // ipadd (both ipv4 and ipv6) - IP address
      case 'ip':
                        if (edh_validate($data, 'ip')) :
                          $clean_data = $data;
                        else:
                          // based on info, return blank ip address
                          switch($info) {
                            case 'ipv4':
                                        $clean_data = '0.0.0.0';
                                        break;
                            case 'ipv6':
                                        $clean_data = '0000:0000:0000:0000:0000:0000:0000:0000';
                                        break;
                            default:
                                        $clean_data = '0.0.0.0';
                                        break;
                          }
                        endif;
      default:
                        $clean_data = sanitize_text_field($data);
                        break;
    }
  endif;

  // trim
  if ($trim > 0):
    $datalength = strlen($data);
    if ($datalength > $trim):
      $clean_data = substr($clean_data, 0, $trim);
    endif;
  endif;

  return $clean_data;
}

// DISPLAY FUNCTIONS

// US Phone Number Formatting
/*
  This function is intended to help format a phone number from the database in a consistent way, regardless of how it was stored.
  $data - the value to be displayed
  $format - a number 0-4 that determines the format
  returns the phone number in the proper format
*/
function edh_display_phone($data, $format) {
  if ($data != '' && $data != null) :
    // remove all characters except numbers
    $pattern = "/[^0-9]/";
    $clean_data = preg_replace($pattern, '', $data);

    // Break number into sections
    $part1 = substr($clean_data, 0, 3);
    $part2 = substr($clean_data, 3, 3);
    $part3 = substr($clean_data, 6, 4);

    switch ($format) {
      // ###-###-####
      case 0: $clean_data = $part1 . '-' . $part2 . '-' . $part3;
              break;
      // ###.###.####
      case 1: $clean_data = $part1 . '.' . $part2 . '.' . $part3;
              break;
      // ### ### ####
      case 2: $clean_data = $part1 . ' ' . $part2 . ' ' . $part3;
              break;
      // (###) ###-####
      case 3: $clean_data =  '(' . $part1 . ') ' . $part2 . '-' . $part3;
              break;
      // ##########
      case 4: $clean_data = $part1 . $part2 . $part3;
              break;
      // (###) ###-####
      default: $clean_data =  '(' . $part1 . ') ' . $part2 . '-' . $part3;
              break;
    }
  else :
    $clean_data = '';
  endif;
  return $clean_data;
}

// Display Text Values
/*
  The purpose of this function is to prepare data for editing inside a form. If the value you want to display has characters that have been escaped such as \' then it can remove that in mode 1 or 2. If the value has HTML entities and you want to display them in a textbox or textarea for editing, then you would use mode 0 or 2. This is needed for text values that allow for all character strings.
  $data - the string to prepare
  $mode - what kind of preparation is needed
    0: Text Fields - decode html entities (default)
    1: Textarea - decodes html entities and removes slashes
        * Note: Do not use this on input fields because it can cause conflict if "" are entered and output in the field value.
    2: remove slashes
  returns the string after performing requested task
*/
function edh_display_text($data, $mode = 0) {
  $result = '';
  if ($data != '' && $data != null):
    if ($mode == '' || $mode == null):
      $mode = 0;
    endif;

    switch ($mode) {
      case 0:   // Text Fields
                // convert html entities to characters
                $result = html_entity_decode($data);
                // strip slashes and remove "
                $result = stripslashes_deep($result);
                $result = str_replace('"', "", $result);
                // convert <br>s to new lines
                $result = str_replace("<br>", "", $result);
                break;
      case 1:   // Textarea
                  // It will strip the slashes from quotes which can break the
                // remove slashes - escaped characters like \' become '
                $result = stripslashes_deep($data);
                // convert html entities to characters
                $result = html_entity_decode($result);
                // convert <br>s to new lines
                $result = str_replace("<br>", "\r\n", $result);
                break;
      case 2:   // remove slashes - escaped characters like \' become '
                $result = stripslashes_deep($data);
                break;
      default:  // convert html entities to characters
                $result = html_entity_decode($data);
                // convert <br>s to new lines
                $result = str_replace("<br>", "\r\n", $result);
                break;
    }
  endif;
  return $result;
}


// PASSWORD FUNCTIONS
function edh_generate_password() {
  $lowercase = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
  $uppercase = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
  $numbers = array('0','1','2','3','4','5','6','7','8','9');
  $special = array('.','*','^','8','$','@','#',':','=','+','-','_','~','!','?','%');
  $generate_password = '';

  $counter_low = 0;
  $counter_up = 0;
  $counter_num = 0;
  $counter_spec = 0;
  $counter = 0;

  while(strlen($generate_password) < 8) {

  	if (($counter_low > 0 || $counter_up > 0) && ($counter < 7) && ($counter_num < 3)):
  		$picktype = rand(1,3);
  	else:
  		$picktype = rand(1,2);
  	endif;

  	switch ($picktype) {
  		case 1: $counter_low++;
  				$generate_password = $generate_password . $lowercase[rand(0,25)];
  				break;
  		case 2: $counter_up++;
  				$generate_password = $generate_password . $uppercase[rand(0,25)];
  				break;
  		case 3: $counter_num++;
  				$generate_password = $generate_password . $numbers[rand(0,9)];
  				break;
  		case 4: $counter_spec++;
  				$generate_password = $generate_password . $special[rand(0,15)];
  				break;
  	}

  }
  return $generate_password;
}

function edh_generate_strong_password() {
  $lowercase = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
  $uppercase = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
  $numbers = array('0','1','2','3','4','5','6','7','8','9');
  $special = array('.','*','^','8','$','@','#',':','=','+','-','_','~','!','?','%');
  $generate_password = '';

  $counter_low = 0;
  $counter_up = 0;
  $counter_num = 0;
  $counter_spec = 0;
  $counter = 0;

  while(strlen($generate_password) < 12) {

  	if (($counter_low > 0 || $counter_up > 0) && ($counter < 7) && ($counter_num < 3) && ($counter_spec < 3)):
  		$picktype = rand(1,4);
  	else:
  		$picktype = rand(1,2);
  	endif;

  	switch ($picktype) {
  		case 1: $counter_low++;
  				$generate_password = $generate_password . $lowercase[rand(0,25)];
  				break;
  		case 2: $counter_up++;
  				$generate_password = $generate_password . $uppercase[rand(0,25)];
  				break;
  		case 3: $counter_num++;
  				$generate_password = $generate_password . $numbers[rand(0,9)];
  				break;
  		case 4: $counter_spec++;
  				$generate_password = $generate_password . $special[rand(0,15)];
  				break;
  	}

  }
  return $generate_password;
}

// DATE CONVERSIONS

// DATE TO MYSQL DATETIME
/*
  This function converts a date into the MySQL DateTime format. It takes any date, converts it to unix time, then puts it in the proper format.
  $date - is the date that needs to be converted
  $format - is a number 0 or 2 (default is 0)
  returns the converted date or false if not valid
*/
function edh_convert_date2mysqldatetime($date, $format = 0) {
  if ($date != '' && $date != null) :
    // remove all characters except numbers
    $pattern = "/[^0-9]/";
    $date_digits = preg_replace($pattern, '', $date);
    $part1 = substr($date_digits, 0, 2); // mm or dd
    $part2 = substr($date_digits, 2, 2); // dd or mm
    $part3 = substr($date_digits, 4, 4); // yyyy
    switch($format) {
      // m/d/Y or m/d/Y
      case 0:
              $unixtime = date("U", mktime(0, 0, 0, $part1, $part2, $part3));
              break;
      // d/m/Y or d-m-Y
      case 1:
              $unixtime = date("U", mktime(0, 0, 0, $part2, $part1, $part3));
              break;
      // unix timestamp
      case 2:
              $unixtime = $date_digits;
              break;
      default: // m/d/Y or m/d/Y
              $unixtime = date("U", mktime(0, 0, 0, $part1, $part2, $part3));
              break;
    }
    // if the converted date is not valid, return false
    if (!edh_validate($converted_date, 'date', 0, 0, 4)) :
      return false;
    else: // otherwise return the datetime
      return $converted_date;
    endif;
  else:
    return false;
  endif;
}

// DATE TO MYSQL DATE
/*
  This function converts a date into the MySQL Date format. It takes any date, converts it to unix time, then puts it in the proper format.
  *** Valid years are 1000 to 2999 ***
  $date - is the date that needs to be converted
  $format - is a number 0 or 2 (default is 0)
  returns the converted date or false if not valid
*/
function edh_convert_date2mysqldate($date, $format = 0) {
  if ($date != '' && $date != null) :
    // remove all characters except numbers
    $pattern = "/[^0-9]/";
    $date_digits = preg_replace($pattern, '', $date);
    $part1 = substr($date_digits, 0, 2); // mm or dd
    $part2 = substr($date_digits, 2, 2); // dd or mm
    $part3 = substr($date_digits, 4, 4); // yyyy
    switch($format) {
      // m/d/Y or m/d/Y
      case 0:
              $unixtime = date("U", mktime(0, 0, 0, $part1, $part2, $part3));
              break;
      // d/m/Y or d-m-Y
      case 1:
              $unixtime = date("U", mktime(0, 0, 0, $part2, $part1, $part3));
              break;
      // unix timestamp
      case 2:
              $unixtime = $date_digits;
              break;
      default: // m/d/Y or m/d/Y
              $unixtime = date("U", mktime(0, 0, 0, $part1, $part2, $part3));
              break;
    }
    $converted_date = date('Y-m-d', $unixtime);
    // if the converted date is not valid, return false
    if (!edh_validate($converted_date, 'date', 0, 0, 5)) :
      return false;
    else : // otherwise return the datetime
      return $converted_date;
    endif;
  else :
    return false;
  endif;
}

// DATE TO UNIX
/*
  This function converts a date into a unix timestamp. It takes any date, converts it to unix time. You can then use it to display whatever format you like.
  *** Valid years are 1000 to 2999 ***
  $date - is the date that needs to be converted
  $format - is a number 0 or 1 (default is 0)
  returns the converted unix timestamp
*/
function edh_convert_date2unix($date, $format = 0) {
  if ($date != '' && $date != null) :
    // remove all characters except numbers
    $pattern = "/[^0-9]/";
    $date_digits = preg_replace($pattern, '', $date);
    $part1 = substr($date_digits, 0, 2); // mm or dd
    $part2 = substr($date_digits, 2, 2); // dd or mm
    $part3 = substr($date_digits, 4, 4); // yyyy
    switch($format) {
      // m/d/Y or m/d/Y
      case 0:
              $unixtime = date("U", mktime(0, 0, 0, $part1, $part2, $part3));
              break;
      // d/m/Y or d-m-Y
      case 1:
              $unixtime = date("U", mktime(0, 0, 0, $part2, $part1, $part3));
              break;
      // m/d/Y H:i:s 24
      case 2:
              $part4 = substr($date_digits, 8, 2);
              $part5 = substr($date_digits, 10, 2);
              $part6 = substr($date_digits, 12, 2);
              if ($part4 == '') $part4 = 0;
              if ($part5 == '') $part5 = 0;
              if ($part6 == '') $part6 = 0;
              $unixtime = date("U", mktime($part4, $part5, $part6, $part2, $part1, $part3));
      default: // m/d/Y or m/d/Y
              $unixtime = date("U", mktime(0, 0, 0, $part1, $part2, $part3));
              break;
    }
    return $unixtime;
  else :
    return false;
  endif;
}

// Strips all HTML and PHP tags
function wp_strip_all_tags($text = '') {
  $clean_text = strip_tags($text); 
  return $clean_text; 
}

function sanitize_text_field($text = '') {
  $clean_text = filter_var($text, FILTER_SANITIZE_STRING);
  return $clean_text;
}

function sanitize_email($email = '') {
  $clean_text = filter_var($email, FILTER_SANITIZE_EMAIL);
  return $clean_text;
}

// Strips 
function sanitize_textarea_field($text = '', $html = false) {
  if ($text != ''):
    $text = nl2br($text, false); // false = <br>, true = <br />
    $text_lines = explode("<br>", $text); 
    foreach ($text_lines as $key => $val):
      $val = trim($val); 
      $val = stripslashes($val);
      if ($html === false):
        $val = strip_tags($val);
      else: 
        $val = strip_tags($val, '<br>');
      endif; 
      $val = htmlspecialchars($val);
      $text_lines[$key] = $val; 
    endforeach; 
    if ($html):
      return implode("<br>", $text_lines);
    else: 
      return implode("\r\n", $text_lines);
    endif; 
  else: 
    return '';
  endif; 
}


?>
