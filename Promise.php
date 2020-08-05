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
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param Closure $closure
     * @param array|null $args
     */
    private function callClosure(Closure $closure, array $args = null)
    {
        if ($args != null) {
            $this->data = $closure(...$args);
        }else {
            $this->data = $closure($this->data);
        }
    }

    /**
     * @param Exception $e
     * @param array $exception
     * @throws Exception
     */
    private function throwException(Exception $e, array $exception)
    {
        if (key_exists(get_class($e), $exception)) {
            throw new $exception[get_class($e)];
        }else {
            throw new $e;
        }
    }

    /**
     * @param Closure $closure
     * @param array|null $args
     * @param array|null $exception
     * @return $this
     * @throws Exception
     */
    public function promise(Closure $closure, array $args = null, array $exception = null)
    {
        if ($exception === null) {
            $this->callClosure($closure, $args);
        }else {
            try {
                $this->callClosure($closure, $args);
            } catch (Exception $e) {
                $this->throwException($e, $exception);
            }
        }

        return $this;
    }

    /**
     * @param Closure $closure
     * @param array|null $args
     * @param array|null $exception
     * @return $this
     * @throws Exception
     */
    public function map(Closure $closure, array $args = null, array $exception = null)
    {
        if ($args != null) {
            $this->data = $args;
        }

        if ($exception === null) {
            $this->data = array_map($closure, $this->data);
        }else {
            try {
                $this->data = array_map($closure, $this->data);
            }catch (Exception $e){
                $this->throwException($e, $exception);
            }
        }

        return $this;

    }

}
