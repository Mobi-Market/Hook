<?php

namespace Esemve\Hook;

class Callback
{
    /**
     * @var callable|null
     */
    protected $function;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var bool
     */
    protected $run = true;

    public function __construct(?callable $function, array $parameters = [])
    {
        $this->setCallback($function, $parameters);
    }

    public function setCallback(?callable $function, array $parameters): void
    {
        $this->function = $function;
        $this->parameters = $parameters;
    }

    /**
     * @return mixed|null
     */
    public function call(?array $parameters = null)
    {
        if ($this->run) {
            $this->run = false;

            if ($this->function) {
                return \call_user_func_array($this->function, $parameters ?? $this->parameters);
            }
        }

        return null;
    }

    public function reset(): void
    {
        $this->run = true;
    }
}
