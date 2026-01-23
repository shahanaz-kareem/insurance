<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'commission';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['amount', 'client_id','date','notes'];

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
