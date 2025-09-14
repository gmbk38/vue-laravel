<?php
// Смотрим, что прилетает в контейнер
echo '<pre>';
echo 'Request URI: ' . $_SERVER['REQUEST_URI'] . "\n";
echo 'Method: ' . $_SERVER['REQUEST_METHOD'] . "\n";
echo "Headers:\n";
foreach (getallheaders() as $name => $value) {
    echo "  $name: $value\n";
}
echo '</pre>';
