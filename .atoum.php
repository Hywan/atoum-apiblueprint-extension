<?php

$extension = new atoum\apiblueprint\extension($script);
$extension->addToRunner($runner);

$extension->getAPIBFinder()->append(new FilesystemIterator(__DIR__ . '/test/system'));
$extension->compileAndEnqueue();
