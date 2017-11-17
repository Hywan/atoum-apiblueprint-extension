<?php

$extension = new atoum\apiblueprint\extension($script);
$extension->getAPIBFinder()->append(new DirectoryIterator(__DIR__ . '/res'));
$extension->addToRunner($runner);
