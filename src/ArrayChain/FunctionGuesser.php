<?php

namespace ArrayChain;

class FunctionGuesser {

  public function call(Chain $array, $name, array $arguments) {
    $func_name = $this->findFunctionName($name);
    $parsed_args = $this->buildArguments($func_name, $array->get(), $arguments);

    return call_user_func_array($func_name, $parsed_args);
  }

  private function buildArguments($functionName, array $arrayCopy, array $arguments) {
    $reflection = new \ReflectionFunction($functionName);
    $parameters = $reflection->getParameters();
    $index = 0;
    $check_names = array(
      'arg',
      'input',
      'pieces',
    );

    foreach($parameters as $param) {

      // It looks like isArray() not always returns TRUE for internal functions
      if(true === $param->isArray() || true === in_array($param->name, $check_names)) {
        break;
      }

      $index += 1;
    }

    if($index == count($parameters)) {
      throw new \LogicException('What now?');
    }

    if(0 == $index) {
      array_unshift($arguments, $arrayCopy);
    }
    else if($index + 1 == count($parameters)) {
      array_push($arguments, $arrayCopy);
    }
    else {
      $arguments = array_merge(
        array_slice($arguments, 0, $index),
        array($arrayCopy),
        array_slice($arguments, $index)
      );
    }

    return $arguments;
  }

  public function findFunctionName($name) {
    $name = $this->convertToSnakeCase($name);
    $check_names = array(
      $name,
      'array_'.$name,
    );

    foreach($check_names as $name) {

      if(true === function_exists($name)) {
        return $name;
      }
    }

    throw \LogicException('Array function not found');
  }

  /**
   * Covert camel case to snake case
   *
   * Based on Laravel4 Str::snake() (@see https://github.com/laravel/framework/blob/master/src/Illuminate/Support/Str.php)
   *
   * @param string $value
   * @return string
   */
  private function convertToSnakeCase($value) {
    return ctype_lower($value) ? $value : strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', $value));
  }

}
