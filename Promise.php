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
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @param Closure $closure
     * @param array|null $args
     */
    private function callClosure(Closure $closure, array $args = null)
    {
         null !== $args ? $this->setData($closure(...$args)) : $this->setData($closure($this->getData()) );
    }

    /**
     * @param Exception $e
     * @param array $exception
     * @throws Exception
     */
    private function throwException(Exception $e, array $exception)
    {
        if (isset($exception[$class = get_class($e)])) {
            throw new $exception[$class];
        } else {
            throw new $e;
        }
    }

    /**
     * @param Closure $closure
     * @param array|null $args
     * @param array|null $exceptions
     * @return $this
     * @throws Exception
     */
    public function promise(Closure $closure, array $args = null, array $exceptions = null)
    {
        if ($exceptions === null) {
            $this->callClosure($closure, $args);
        } else {
            try {
                $this->callClosure($closure, $args);
            } catch (Exception $e) {
                $this->throwException($e, $exceptions);
            }
        }

        return $this;
    }

    /**
     * @param Closure $closure
     * @param array|null $args
     * @param array|null $exceptions
     * @return $this
     * @throws Exception
     */
    public function map(Closure $closure, array $args = null, array $exceptions = null)
    {
        if (null != $args) {
            $this->setData($args);
        }

        if (null === $exceptions) {
            $this->setData( array_map($closure, $this->getData()) );
        } else {
            try {
                $this->setData( array_map($closure, $this->getData()));
            } catch (Exception $e){
                $this->throwException($e, $exceptions);
            }
        }

        return $this;
    }

}
