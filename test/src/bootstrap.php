<?php
$loader = new Phalcon\Loader();
$loader->registerNamespaces(
    array(
        'Emr' => '../src/Emr',
    )
);
$loader->register();