<?php

require __DIR__ . "/../vendor/autoload.php";

foreach (glob(__DIR__ . "/intl/*.yml") as $file) {
    unlink($file);
}
