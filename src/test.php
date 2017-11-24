<?php

declare(strict_types=1);

namespace atoum\apiblueprint;

use mageekguy\atoum;

class test extends \mageekguy\atoum\test
{
    public function getTestedClassName()
    {
        return 'StdClass';
    }

    public function getTestedClassNamespace()
    {
        return '\\';
    }

    public function responsesMatch(\Generator $responses, array $expectedResponses): self
    {
        foreach ($responses as $i => $response) {
            if (!isset($expectedResponses[$i])) {
                $this->boolean(true)->isTrue();

                continue;
            }

            $expectedResponse = $expectedResponses[$i];

            $this
                ->integer($expectedResponse['statusCode'])
                    ->isEqualTo($response->statusCode);
        }

        return $this;
    }
}
