<?php

function env(string $name, $default = null) {
  return $_ENV[$name] ?? $default;
}