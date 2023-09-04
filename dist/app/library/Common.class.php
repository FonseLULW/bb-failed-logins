<?php

/**
 * Class Common
 */
class Common
{
  /**
   * Convert \n and \r\n and \r to <br />.
   *
   * @param string $string String to transform
   *
   * @return string New string
   */
  public static function nl2br($str)
  {
    return str_replace(array("\r\n", "\r", "\n", PHP_EOL), '<br />', $str);
  }

  /**
   * Escapes illegal characters in a string.
   *
   * @param string $str
   *
   * @return string
   * @see DbCore::_escape()
   */
  public static function escape($str)
  {
    $search  = array('\\', "\0", "\n", "\r", "\x1a", "'", '"');
    $replace = array('\\\\', '\\0', '\\n', '\\r', "\Z", "\'", '\"');

    return str_replace($search, $replace, $str);
  }

  /**
   * Get Request Data mostly for PUT and DELETE methods it needs to be a valid JSON
   *
   * @return array
   */
  public static function getJsonDataFromRequest()
  {
    $input = (array) json_decode(file_get_contents('php://input'), true);

    return self::sanitizeArray($input);
  }

  /**
   * Sends API response
   *
   * @param $code
   * @param $data
   */
  public static function sendResponse($code, $data = [])
  {
    http_response_code($code);

    if (gettype($data) === 'string') {
      echo $data;
    } else {
      header('Content-type: application/json');
      echo json_encode($data);
    }
    exit;
  }

  /**
   * Sanitizes the data inside an array
   *
   * @param $data
   *
   * @return array
   */
  public static function sanitizeArray($data)
  {
    $form = [];
    foreach ($data as $form_name => $form_input) {
      if (is_array($form_input)) {
        foreach ($form_input as $key => $value) {
          $form[$form_name][$key] = self::sanitizeVar($value);
        }
      } else {
        $form[$form_name] = self::sanitizeVar($form_input);
      }
    }

    return $form;
  }

  /**
   * Sanitizes a variable according to the content
   *
   * @param $var
   *
   * @return float|int|mixed
   */
  public static function sanitizeVar($var)
  {
    if (is_numeric($var)) {
      return (strpos($var, '.') > 0) ? (float) $var : (int) $var;
    }

    return filter_var($var, FILTER_SANITIZE_STRING);
  }
}
