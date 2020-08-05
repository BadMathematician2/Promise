<?php


namespace App\Packages\Promise;


use Closure;
use Exception;

class Promise
{
    /**
     * @var mixed
     */
    private $data;
    /**
     * @var Exception
     */
    private $exception;
    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
    /**
     * @param Closure $closure
     * @param array|null $args
     * @param string|null $exception
     * @return $this
     */
    public function promise(Closure $closure, array $args = null, $exception = null)
    {


        if (! is_null($exception)) {
            $this->exception = $exception;
        }

        try {
            if (! is_null($args)) {
                $this->data = $closure(...$args);
            }else {
                $this->data = $closure($this->data);
            }
        }catch (Exception $e) {
            throw new $this->exception;
        }

        return $this;
    }

    /**
     * @param Closure $closure
     * @param array|null $args
     * @param string|null $exception
     * @return $this
     */
    public function map(Closure $closure, array $args = null, $exception = null)
    {

        if (! is_null($exception)) {
            $this->exception = $exception;
        }

        if (! is_null($args)) {
            $this->data = $args;
        }

        $result = [];

        foreach ($this->data as $arg) {
            try {
                $result[] = $closure(...(array)$arg);
            } catch (Exception $e) {
                throw new $this->exception;
            }
        }
        $this->data = $result;

        return $this;

    }

}
