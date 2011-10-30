<?php

require_once(__DIR__.'/vendor/symfony/Component/ClassLoader/UniversalClassLoader.php');

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
    'Ylly\\CommandProcessor' => __DIR__.'/vendor/ylly/CommandProcessor/lib',
    'Symfony' => __DIR__.'/vendor/symfony',
    'Monolog' => __DIR__.'/vendor/monolog/src',
));
$loader->registerPrefixes(array(
    'Twig_'            => __DIR__.'/vendor/twig/lib',
));
$loader->registerNamespaceFallbacks(array(
    __DIR__.'/lib',
));
$loader->register();

