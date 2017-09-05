<?php

namespace Elimuswift\Tenancy\Abstracts;

use Elimuswift\Tenancy\Traits\DispatchesEvents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use ReflectionClass;

abstract class ModelObserver
{
    use DispatchesEvents;

    /**
     * @param Model $model
     */
    public function creating($model)
    {
        $this->fire(__FUNCTION__, $model);
    }

    /**
     * @param Model $model
     */
    public function created($model)
    {
        $this->fire(__FUNCTION__, $model);
    }

    /**
     * @param Model $model
     */
    public function updating($model)
    {
        $this->fire(__FUNCTION__, $model);
    }

    /**
     * @param Model $model
     */
    public function updated($model)
    {
        $this->fire(__FUNCTION__, $model);
    }

    /**
     * @param Model $model
     */
    public function deleting($model)
    {
        $this->fire(__FUNCTION__, $model);
    }

    /**
     * @param Model $model
     */
    public function deleted($model)
    {
        $this->fire(__FUNCTION__, $model);
    }

    /**
     * @param Model $model
     */
    public function saving($model)
    {
        $this->fire(__FUNCTION__, $model);
    }

    /**
     * @param Model $model
     */
    public function saved($model)
    {
        $this->fire(__FUNCTION__, $model);
    }

    /**
     * @param Model $model
     */
    public function restoring($model)
    {
        $this->fire(__FUNCTION__, $model);
    }

    /**
     * @param Model $model
     */
    public function restored($model)
    {
        $this->fire(__FUNCTION__, $model);
    }

    /**
     * @param string $event
     * @param Model  $model
     */
    protected function fire(string $event, Model $model)
    {
        $eventClass = \sprintf(
            'Elimuswift\\Tenancy\\Events\\%s\\%s',
            Str::plural((new ReflectionClass($model))->getShortName()),
            Str::camel($event)
        );

        if (\class_exists($eventClass)) {
            $this->emitEvent(new $eventClass($model));
        }
    }
}
