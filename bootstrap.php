<?php

require_once('./vendor/symfony/Component/ClassLoader/UniversalClassLoader.php');

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Ylly\\CommandProcessor' => __DIR__.'/vendor/ylly/CommandProcessor/lib',
    'Symfony' => './vendor/symfony',
    'Monolog' => './vendor/monolog/src',
));
$loader->registerNamespaceFallbacks(array(
    __DIR__.'/lib',
));
$loader->register();

