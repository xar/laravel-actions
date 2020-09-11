<?php

namespace Lorisleiva\Actions;

use Illuminate\Support\Str;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Container\Container as ContainerContract;
use Lorisleiva\Actions\Facades\Actions;

class EventDispatcherDecorator implements DispatcherContract
{
    protected $dispatcher;
    protected $container;

    public function __construct(DispatcherContract $dispatcher, ContainerContract $container)
    {
        $this->dispatcher = $dispatcher;
        $this->container = $container;
    }

    public function listen($events, $listener = null)
    {
        if ($this->isActionFullyQualifiedName($listener)) {
            $listener = $listener . '@runAsListener';
        }

        $this->dispatcher->listen($events, $listener);
    }

    public function isActionFullyQualifiedName($listener)
    {
        if (! is_string($listener)) {
            return false;
        }

        [$class, $method] = Str::parseCallback($listener);

        if (! is_null($method)) {
            return false;
        }

        return Actions::isAction($listener);
    }

    public function hasListeners($eventName)
    {
        return $this->dispatcher->hasListeners($eventName);
    }

    public function subscribe($subscriber)
    {
        $this->dispatcher->subscribe($subscriber);
    }

    public function until($event, $payload = [])
    {
        return $this->dispatcher->until($event, $payload);
    }

    public function dispatch($event, $payload = [], $halt = false)
    {
        return $this->dispatcher->dispatch($event, $payload, $halt);
    }

    public function push($event, $payload = [])
    {
        $this->dispatcher->push($event, $payload);
    }

    public function flush($event)
    {
        $this->dispatcher->flush($event);
    }

    public function forget($event)
    {
        $this->dispatcher->forget($event);
    }

    public function forgetPushed()
    {
        $this->dispatcher->forgetPushed();
    }

    public function __call($method, $parameters)
    {
        return $this->dispatcher->{$method}(...$parameters);
    }
}
