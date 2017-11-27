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
                ->integer($response->statusCode)
                    ->isIdenticalTo($expectedResponse['statusCode']);

            if (!empty($expectedResponse['mediaType'])) {
                $this
                    ->array($response->headers)
                        ->string['Content-Type']
                            ->isIdenticalTo($expectedResponse['mediaType']);
            }

            if (!empty($expectedResponse['headers'])) {
                $headerAsserter = $this->array($response->headers);

                foreach ($expectedResponse['headers'] as $headerName => $headerValue) {
                    $headerAsserter
                        ->string[$headerName]
                            ->isIdenticalTo($headerValue);
                }
            }

            if (!empty($expectedResponse['body'])) {
                $this
                    ->string($response->body)
                        ->isIdenticalTo($expectedResponse['body']);
            }
        }

        return $this;
    }
}
