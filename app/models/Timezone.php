<?php

class Timezone extends Eloquent
{
    public $timestamps = false;
    protected $guarded = array('*');

    /**
     * Defines flight relation.
     * @return mixed
     */
    public function flight()
    {
        return $this->hasMany('Flight');
    }
} 