<?php

class Timezone extends Eloquent
{
    public $timestamps = false;
    protected $guarded = array('*');

    /**
     * Defines airport relation.
     * @return mixed
     */
    public function airports()
    {
        return $this->hasMany('Airport');
    }
} 