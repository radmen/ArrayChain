<?php

namespace ArrayChain;

class Chain {

  private $data;

  private $guesser;

  public function __construct(array $data, FunctionGuesser $guesser = null) {
    $this->data = $data;
    $this->guesser = $guesser ?: new FunctionGuesser();
  }

  public function __call($method, $arguments) {
    $array = $this->guesser->call($this, $method, $arguments);

    if(false === is_array($array)) {
      return $array;
    }

    return new static($array, $this->guesser);
  }

  public function get() {
    return $this->data;
  }
}
