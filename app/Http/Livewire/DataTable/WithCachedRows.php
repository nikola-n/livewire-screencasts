<?php

namespace App\Http\Livewire\DataTable;

trait WithCachedRows
{

    protected $useCached = false;

    public function useCachedRows()
    {
        $this->useCached = true;
    }

    public function cache($callback)
    {
        //component id
        $cacheKey = $this->id;

        if ($this->useCached && cache()->has($cacheKey)) {
            return cache()->get($cacheKey);
        }
        $result = $callback();

        cache()->put($cacheKey, $result);

        return $result;
    }
}
