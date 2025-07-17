<?php

namespace Core\Traits;

trait BuildFilterOrValidator
{
    private array $_params;

    public function build(): self
    {
        if (!isset($this->_params)) {
            $this->setDefaultParams();
        }

        foreach ($this->_params as $key => $value) {
            if (isset($this->$key)) {
                if (is_null($value)) {
                    unset($this->$key);
                } else {
                    $this->$key = $value;
                }
            }
        }

        return $this;
    }

    private function setDefaultParams(): void
    {
        $class = get_class($this);
        $properties = (new \ReflectionObject($this))
            ->getProperties(\ReflectionProperty::IS_PROTECTED);

        $this->_params = [];

        foreach ($properties as $property) {
            if ($class === $property->class) {
                $this->_params[$property->name] = $this->{$property->name} ?? null;
            }
        }
    }

    public function setOptions(array $options): self
    {
        foreach ($options as $key => $value) {
            if (array_key_exists($key, $this->_params)) {
                $this->$key = $value;
            }
        }

        return $this;
    }
}