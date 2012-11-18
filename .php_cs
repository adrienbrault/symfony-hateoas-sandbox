<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->notName('LICENSE')
    ->notName('readme.md')
    ->notName('.php_cs')
    ->notName('.travis.yml')
    ->notName('build.xml')
    ->notName('pom.xml')
    ->notName('composer.*')
    ->notName('phpunit.xml*')
    ->exclude('Resources')
    ->exclude('vendor')
    ->exclude('Tests')
    ->exclude('app')
    ->exclude('.DS_STORE')
    ->exclude('.idea')
    ->exclude('features')
    ->in(__DIR__)
;

return Symfony\CS\Config\Config::create()
    ->finder($finder)
;
