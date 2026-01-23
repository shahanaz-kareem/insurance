<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Policy extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'policies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['beneficiaries', 'expiry', 'payer', 'premium', 'branch_id', 'ref_no', 'renewal', 'special_remarks', 'type','policy_term','payment_term','policy_no','premium_chq_amount','premium_chq_date','premium_chq_no','branch_manager','branch_assist','ecs_mandate','bank_name','pin','bank_branch','ack_date','ack_number','application_no','policy_date','ldob','lmnum','lemail','ndob','nmnum','nemail','deposit_name','premium_amount','point_login','sum_assured','renewal_date'];

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
        static::deleting(function($policy) {
            $policy->attachments()->delete();
            $policy->customFields()->delete();
        });
    }

    // Relationships
    public function attachments() {
        return $this->morphMany(Attachment::class, 'attachee');
    }
    public function customFields() {
        return $this->morphMany(CustomField::class, 'model');
    }
    public function payments() {
        return $this->hasMany(Payment::class);
    }
    public function product() {
        return $this->belongsTo(Product::class);
    }
    public function branch() {
        return $this->belongsTo(Branches::class, 'branch_id');
    }
    public function client() {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function staff() {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Scope a query.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query, $type) {
        $interval_map = array(
            'annual'    => 'YEAR',
            'monthly'   => 'MONTH'
        );
        return $query->{$type}()->whereRaw("CURDATE() BETWEEN DATE_SUB(`{$this->table}`.`expiry`, INTERVAL 1 {$interval_map[$type]}) AND `{$this->table}`.`expiry`");
    }
    public function scopeAnnual($query) {
        return $query->where('type', 'annual');
    }
    public function scopeCreatedIn($query, $period, $value) {
        if(is_null($value)) {
            $value = array(
                'month' => date('m'),
                'year'  => date('Y')
            )[$period];
        }
        $date_map = array(
            'month' => array(
                'begin' => "DATE('" . date('Y') . "-{$value}-01')",
                'end'   => "LAST_DAY(DATE('" . date('Y') . "-{$value}-28'))"
            ),
            'year'  => array(
                'begin' => "DATE('" . $value . "-01-01')",
                'end'   => "DATE('" . $value. "-12-31')"
            )
        )[$period];
        return $query->whereRaw("DATE(`{$this->table}`.`created_at`) BETWEEN {$date_map['begin']} AND {$date_map['end']}");
    }
    public function scopeExpiring($query, $timeline, $days) {
        $operator = array(
            'after'     => 'ADD',
            'before'    => 'SUB'
        )[$timeline];
        return $query->whereRaw("CURDATE() BETWEEN `{$this->table}`.`expiry` AND DATE_{$operator}(`{$this->table}`.`expiry`, INTERVAL {$days} DAY)");
    }
    public function scopeInsuraFilter($query, $filters) {
        $users_table = (new User)->getTable();
        $payments_table = (new Payment)->getTable();
        $modifiers = array(
            // 'due_max' => function($query, $value) use($payments_table) {
            //     return $query->whereRaw("(`{$this->table}`.`premium` - (SELECT SUM(`{$payments_table}`.`amount`) FROM `{$payments_table}` WHERE `{$payments_table}`.`policy_id` = `{$this->table}`.`id`)) <= {$value}");
            // },
            // 'due_min' => function($query, $value) use($payments_table) {
            //     return $query->whereRaw("(`{$this->table}`.`premium` - (SELECT SUM(`{$payments_table}`.`amount`) FROM `{$payments_table}` WHERE `{$payments_table}`.`policy_id` = `{$this->table}`.`id`)) >= {$value}");
            // },
            // 'expiry_from' => function($query, $value) {
            //     return $query->whereRaw("`{$this->table}`.`expiry` >= DATE({$value})");
            // },
            // 'expiry_to' => function($query, $value) {
            //     return $query->whereRaw("`{$this->table}`.`expiry` <= DATE({$value})");
            // },
            'policy_ref' => function($query, $value) {
                return $query->whereRaw("`{$this->table}`.`policy_no` LIKE '%{$value}%'");
            },
            // 'premium_max' => function($query, $value) {
            //     return $query->whereRaw("`{$this->table}`.`premium` <= {$value}");
            // },
            // 'premium_min' => function($query, $value) {
            //     return $query->whereRaw("`{$this->table}`.`premium` >= {$value}");
            // },
            'product' => function($query, $value) {
                return $query->whereRaw("`{$this->table}`.`product_id` = {$value}");
            },
            'renewal_from' => function($query, $value) {
                $value = date("Y-m-d", strtotime($value));
                //var_dump($value);
                return $query->whereRaw("`{$this->table}`.`renewal_date` >= '$value'");
            },
            'renewal_to' => function($query, $value) {
                $value = date("Y-m-d", strtotime($value));
                //var_dump($value);die;
                return $query->whereRaw("`{$this->table}`.`renewal_date` <= '$value'");
            },
            'branch' => function($query, $value) {
                return $query->whereRaw("`{$this->table}`.`branch_id` = {$value}");
            },
            'staff' => function($query, $value) {
                return $query->whereRaw("`{$this->table}`.`staff_id` = {$value}");
            },
            'client' => function($query, $value) {

                return $query->whereRaw("`{$this->table}`.`user_id` = {$value}");
            },
            'status' => function($query, $value) {
                if($value=='I'){
                    return $query->whereRaw("`{$this->table}`.`policy_no` = '' ");
                } else if($value=='A'){
                    return $query->whereRaw("`{$this->table}`.`policy_no` != '' ");
                } else{
                    return $query->whereRaw("`{$this->table}`.`policy_no` != '' OR `{$this->table}`.`policy_no` = '' ");
                }

            },
            'phone' => function($query, $value) use($users_table) {
                return $query->whereRaw("`{$this->table}`.`user_id` = (SELECT `{$users_table}`.`id` FROM `{$users_table}` WHERE `{$users_table}`.`id` = `{$this->table}`.`user_id` AND `{$users_table}`.`phone` LIKE '%{$value}%')");
            }
        );
        if(isset($filters['due_max']) || isset($filters['due_min'])) {
            $query->leftJoin($payments_table, "{$this->table}.id", '=', "{$payments_table}.policy_id");
        }
        foreach($filters as $filter => $value) {
            if (isset($modifiers[$filter]) && !empty($value)) {
                $query = $modifiers[$filter]($query, $value);
            }
        }
        return $query->groupBy("{$this->table}.id");
    }
    public function scopeMonthly($query) {
        return $query->where('type', 'monthly');
    }
}
