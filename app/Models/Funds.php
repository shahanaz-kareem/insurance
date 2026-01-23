<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Funds extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mutual_funds';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['amount','m_product','client_id', 'branch_id', 'notes', 'date'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    // Soft Delete timestamp
    protected $dates = ['deleted_at'];

    // Register Event Listeners
    public static function boot() {
        parent::boot();
        static::deleting(function($product) {
            $product->policies->each(function($policy) {
                $policy->attachments()->delete();
            });
        });
    }

    // Relationships
    public function company() {
        return $this->belongsTo(Company::class);
    }
    public function branches() {
        return $this->belongsTo(Branches::class);
    }
    public function client() {
        return $this->belongsTo(User::class, 'client_id', 'id');
    }
    public function mproduct() {
        return $this->belongsTo(Mproduct::class,'m_product', 'id');
    }

    public function scopeInsuraFilter($query, $filters) {

        $modifiers = array(
            'client' => function($query, $value) {
                return $query->whereRaw("`{$this->table}`.`id` = {$value}");
            },
            'branch' => function($query, $value) {
                return $query->whereRaw("`{$this->table}`.`branch_id` = {$value}");
            },

        );
        foreach($filters as $filter => $value) {
            $query = $modifiers[$filter]($query, $value);
        }
        return $query->groupBy("{$this->table}.id");
    }

}
