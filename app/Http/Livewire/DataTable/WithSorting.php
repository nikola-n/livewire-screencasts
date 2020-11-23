<?php

namespace App\Http\Livewire\DataTable;

trait WithSorting
{
    public $sorts = [];

    //public $sortField;
    //
    //public $sortDirection = 'asc';

    public function sortBy($field)
    {
        if ( ! isset($this->sorts[$field])) {
            return $this->sorts[$field] = 'asc';
        }
        if ($this->sorts[$field] === 'asc') {
            return $this->sorts[$field] = 'desc';
        }

        unset($this->sorts[$field]);
        //    $this->sortDirection = $this->sortField === $field
        //        ? $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc'
        //        : 'asc';
        //
        //    $this->sortField = $field;
    }

    public function applySorting($query)
    {
        foreach ($this->sorts as $field => $direction) {
            $query->orderBy($field, $direction);
        }

        return $query;
    }
}
