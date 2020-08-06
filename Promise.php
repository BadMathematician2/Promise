<?php


namespace App\Packages\Promise;


use App\Packages\Promise\Exceptions\BindException;
use Closure;
use Exception;

class Promise
{
    /**
     * @var Closure[]
     */
    protected $closures = [];
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
     * @return Closure[]
     */
    private function getBinds(): array
    {
        return $this->closures;
    }
    /**
     * @param string $key
     * @param Closure $closure
     * @throws BindException
     */
    public function bind(string $key, Closure $closure)
    {
        if ($this->bindExists($key)) {
            throw new BindException();
        } else {
            $this->closures[$key] = $closure;
        }
    }
    private function bindExists(string $key): bool
    {
        return isset($this->getBinds()[$key]);
    }
    /**
     * @param Closure $closure
     * @param array|null $args
     */
    private function callClosure(Closure $closure, array $args = null)
    {
        if (null !== $args) {
            $this->setData($closure(...$args));
        } else {
            $this->setData($closure($this->getData()));
        }
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
    private function promiseClosure(Closure $closure, array $args = null, array $exceptions = null)
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
     * @param Closure|string $closure
     * @param array|null $args
     * @param array|null $exceptions
     * @return Promise|mixed
     * @throws Exception
     */
    public function promise($closure, array $args = null, array $exceptions = null)
    {
        return is_string($closure) ? $this->promise($this->getBinds()[$closure], $args, $exceptions) : $this->promiseClosure($closure, $args, $exceptions);
    }
    /**
     * @param Closure $closure
     * @param array|null $args
     * @param array|null $exceptions
     * @return $this
     * @throws Exception
     */
    private function mapClosure(Closure $closure, array $args = null, array $exceptions = null)
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
    /**
     * @param Closure|string $closure
     * @param array|null $args
     * @param array|null $exceptions
     * @return $this
     * @throws Exception
     */
    public function map($closure, array $args = null, array $exceptions = null)
    {
        return is_string($closure) ? $this->mapClosure($this->getBinds()[$closure], $args, $exceptions) : $this->mapClosure($closure, $args, $exceptions);
    }


}
