<?php

declare(strict_types=1);

namespace atoum\apiblueprint\Asserter;

use JsonSchema;
use mageekguy\atoum;

/**
 * Heavily inspired by
 * [`atoum/json-extension`](https://github.com/atoum/json-schema-extension).
 */
class Json extends atoum\asserters\phpString
{
	protected $innerAsserter;
	protected $data;

	public function __get($name)
    {
		return $this->valueIsSet()->innerAsserter->$name;
	}

	public function __call($method, $arguments)
	{
        return $this->valueIsSet()->innerAsserter->$method(...$arguments);
	}

	public function setWith($value, $charlist = null, $checkType = true)
	{
		parent::setWith($value, $charlist, $checkType);

		if (false === self::isJson($value)) {
			$this->fail(sprintf($this->getLocale()->_('%s is not a valid JSON string'), $this));
		}

		$this->data = json_decode($value);

        if (true === is_array($this->data)) {
            $this->innerAsserter = new atoum\asserters\phpArray($this->getGenerator());
        } elseif (true === is_object($this->data)) {
            $this->innerAsserter = new atoum\asserters\phpObject($this->getGenerator());
        } else {
            $this->innerAsserter = new asserters\variable($this->getGenerator());
        }

		$this->innerAsserter->setWith($this->data);

		return $this;
	}

	public function fulfills(string $schema): self
	{
		$schemaObject = $this->toSchemaObject($schema);
		$validator    = new JsonSchema\Validator();

		$validator->check($this->valueIsSet()->data, $schemaObject);

		if ($validator->isValid() === true) {
			$this->pass();
		} else {
			$violations = $validator->getErrors();
			$count      = count($violations);
			$message    = sprintf(
                $this->getLocale()->__(
                    'The JSON response body does not validate the given schema. Found %d violation:',
                    'The JSON response body does not validate the given schema. Found %d violations:',
                    $count
                ),
                $count
            );

			foreach ($validator->getErrors() as $index => $error) {
				$message .=
                    "\n" .
                    sprintf(
                        '    %d. `%s`: %s',
                        $index + 1,
                        $error['property'],
                        $error['message']
                    );
			}

			$this->fail($message);
		}

		return $this;
	}

	protected function valueIsSet($message = 'JSON is undefined')
	{
		return parent::valueIsSet($message);
	}

	protected static function isJson(string $value): bool
	{
		$decoded = @json_decode($value);

		return
			null === error_get_last() &&
			(null !== $decoded || 'null' === strtolower(trim($value)));
	}

	protected function toSchemaObject(string $schema): \StdClass
	{
		$schemaStorage = new JsonSchema\SchemaStorage();
        $schemaObject  = @json_decode($schema);

        if ($schemaObject === null) {
            throw new atoum\exceptions\logic\invalidArgument('Invalid JSON schema');
        }

		return $schemaObject;
	}
}
