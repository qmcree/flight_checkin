<?php

class Airport extends Eloquent
{
    public $timestamps = false;
    protected $guarded = array('*');

    /**
     * Defines flight relation.
     * @return mixed
     */
    public function flights()
    {
        return $this->hasMany('Flight');
    }

    /**
     * Defines inverse timezone relation.
     * @return mixed
     */
    public function timezone()
    {
        return $this->belongsTo('Timezone');
    }
} 