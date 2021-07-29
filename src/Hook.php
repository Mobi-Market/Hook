<?php

namespace Esemve\Hook;

use Illuminate\Support\Arr;

class Hook
{
    /**
     * @var array
     */
    protected $watch = [];
    protected $stop  = [];
    protected $mock  = [];

    /**
     * @var bool
     */
    protected $testing = false;

    /**
     * Return the hook answer.
     *
     * @param mixed|null $initial
     *
     * @return mixed|null
     */
    public function get(string $hook, array $params = [], ?callable $default = null, $initial = null)
    {
        $callbackObject = $this->createCallbackObject($default, $params);

        $output = $this->returnMockIfDebugModeAndMockExists($hook);
        if ($output) {
            return $output;
        }

        $output = $this->run($hook, $params, $callbackObject, $initial);

        if (null === $output && null !== $default) {
            $output = $callbackObject->call();
        }

        unset($callbackObject);

        return $output;
    }

    /**
     * Stop all another hook running.
     */
    public function stop(string $hook): void
    {
        $this->stop[$hook] = true;
    }

    /**
     * Subscribe to hook.
     *
     * @param int|float|null $priority
     */
    public function listen(string $hook, callable $function, $priority = null): void
    {
        $caller = debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT, 3)[2];

        if (in_array(Arr::get($caller, 'function'), ['include', 'require'])) {
            $caller = debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT, 4)[3];
        }

        if (empty($this->watch[$hook])) {
            $this->watch[$hook] = [];
        }

        if (!is_numeric($priority)) {
            $priority = null;
        }

        $this->watch[$hook][$priority] = [
            'function' => $function,
            'caller'   => [
                //'file' => $caller['file'],
                //'line' => $caller['line'],
                'class' => Arr::get($caller, 'class'),
            ],
        ];

        \ksort($this->watch[$hook]);
    }

    /**
     * Return all registered hooks.
     *
     * @return array
     */
    public function getHooks(): array
    {
        $hookNames = \array_keys($this->watch);
        \ksort($hookNames);

        return $hookNames;
    }

    /**
     * Return all listeners for hook.
     *
     * @param string $hook
     *
     * @return array
     */
    public function getEvents($hook): array
    {
        $output = [];

        foreach ($this->watch[$hook] as $key => $value) {
            $output[$key] = $value['caller'];
        }

        return $output;
    }

    /**
     * For testing.
     *
     * @param mixed  $return Answer
     */
    public function mock(string $name, $return): void
    {
        $this->testing = true;
        $this->mock[$name] = ['return' => $return];
    }

    /**
     * Return the mock value.
     *
     * @return null|mixed
     */
    protected function returnMockIfDebugModeAndMockExists(string $hook)
    {
        if ($this->testing) {
            if (array_key_exists($hook, $this->mock)) {
                $output = $this->mock[$hook]['return'];
                unset($this->mock[$hook]);

                return $output;
            }
        }

        return null;
    }

    /**
     * Return a new callback object.
     *
     * @param callable $callback function
     * @param array    $params   parameters
     *
     * @return \Esemve\Hook\Callback
     */
    protected function createCallbackObject(?callable $callback, array $params): Callback
    {
        return new Callback($callback, $params);
    }

    /**
     * Run hook events.
     *
     * @return mixed
     */
    protected function run(string $hook, array $params, Callback $callback, $output = null)
    {
        \array_unshift($params, $output);
        \array_unshift($params, $callback);

        if (\array_key_exists($hook, $this->watch)) {
            if (\is_array($this->watch[$hook])) {
                foreach ($this->watch[$hook] as $function) {
                    if (!empty($this->stop[$hook])) {
                        unset($this->stop[$hook]);
                        break;
                    }

                    $output = \call_user_func_array($function['function'], $params);
                    $params[1] = $output;
                }
            }
        }

        return $output;
    }

    /**
     * Return the listeners.
     *
     * @return array
     */
    public function getListeners(): array
    {
        return $this->watch;
    }
}
