<?php

class Airport extends Eloquent
{
    public $timestamps = false;
    protected $guarded = array('*');

    /**
     * Defines inverse flight relation.
     * @return mixed
     */
    public function flights()
    {
        return $this->belongsToMany('Flight');
    }

    /**
     * Defines timezone relation.
     * @return mixed
     */
    public function timezone()
    {
        return $this->hasOne('Timezone');
    }
} 