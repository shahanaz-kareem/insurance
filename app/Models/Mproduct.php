<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mproduct extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mproduct';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['product_name', 'company_name','status'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    // Soft Delete timestamp
    protected $dates = ['deleted_at'];



    // Relationships
    public function mproduct() {
        return $this->hasMany(Funds::class);
    }

}
