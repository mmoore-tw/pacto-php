<?php
// see https://github.com/FriendsOfPHP/PHP-CS-Fixer

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in([__DIR__.'/src', __DIR__.'/test'])
;

return Symfony\CS\Config\Config::create()
    ->setUsingCache(true)
    ->fixers(['ordered_use', 'short_array_syntax', 'phpdoc_order', 'multiline_spaces_before_semicolon'])
    ->finder($finder)
;
