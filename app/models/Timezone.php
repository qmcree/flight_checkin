<?php

class Timezone extends Eloquent
{
    public $timestamps = false;
    protected $guarded = array('*');

    /**
     * Defines inverse airport relation.
     * @return mixed
     */
    public function airports()
    {
        return $this->belongsToMany('Airport');
    }
} 