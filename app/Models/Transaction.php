<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Transaction extends Model
{
    use HasFactory;


    const STATUSES = [
        'success'    => 'Success',
        'failed'     => 'Failed',
        'processing' => 'Processing',
    ];

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'date',
    ];

    /**
     * @var string[]
     */
    protected $appends = ['date_for_editing'];
    /**
     * @return string
     */
    public function getStatusColorAttribute()
    {
        return [
                   'success' => 'green',
                   'failed'  => 'red',
               ][$this->status] ?? 'cool-gray';
    }

    /**
     * @return mixed
     */
    public function getDateForHumansAttribute()
    {
        return $this->created_at->format('M, d Y');
    }

    /**
     * @return mixed
     */
    public function getDateForEditingAttribute()
    {
        return $this->created_at->format('m/d/Y');
    }

    /**
     * @return mixed
     */
    public function setDateForEditingAttribute($value)
    {
        return $this->created_at = Carbon::parse($value);
    }




}
