<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Models\Company;
use App\Models\Payment;
use App\Models\Policy;
use App\Models\Product;
use App\Models\User;
use App\Models\Branches;
use App\Pagination\SemanticUIPresenter;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use DB;

class PolicyController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Policy Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the management and update of existing policies
    | Why don't you explore it?
    |
    */

    /**
     * Create a new policy controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('localize_auth');
    }

    /**
     * Add a policy
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request) {



        // $validation_rules = array(
        //     // 'amount'            => 'array|required_with_all:date,method',
        //     // 'amount.*'          => 'numeric',
        //     // 'amount'            => 'numeric|required',
        //     // 'beneficiaries'     => 'max:512|string',
        //     // 'custom_fields'     => 'array|sometimes',
        //     'date'              => 'array|required_with_all:amount,method',
        //     'date.*'            => 'date',
        //     // 'expiry'            => 'date|required',
        //     'method'            => 'array|required_with_all:amount,date',
        //     'method.*'          => 'in:card,cash,paypal,cheque',
        //     'owners'            => 'array|required',
        //     'owners.*'          => 'exists:users,id|integer',
        //     //'payer'             => 'max:32|required|string',
        //     'premium'           => 'array|required',
        //     'premium.*'         => 'numeric',
        //     'product'           => 'exists:products,id|integer|required',
        //     'branch'            => 'string',
        //     'staff'             => 'string',
        //     'renewal'           => 'date|required',
        //     'special_remarks'   => 'max:2048|string',
        //     'type'              => 'in:annually,monthly,weekly,quarterly,half yearly|required|string',
        //     'policy_term'       => 'integer|required',
        //     'premium_chq_amount'    => 'integer|required',
        //     'premium_chq_date'      => 'date',
        //     'premium_chq_no'        => 'integer|required',
        //     //'branch_manager'        => 'required',
        //     //'branch_assist'         => 'required',
        //     'ecs_mandate'           => 'required',
        //     'bank_name'             => 'required',
        //     'pin'                   => 'required',
        //     'bank_branch'           => 'required',
        //     'ack_date'              => 'required',
        //     'ack_number'            => 'required',
        //     'application_no'        => 'required',
        //     //'policy_no'           => 'required',
        //     'policy_date'           => 'required',
        //     'payment_term'          => 'integer|required',
        //     'point_login'           => 'required',
        //     'sum_assured'           => 'integer',


        // );
        // $this->validate($request, $validation_rules);
        // foreach($request->owners as $owner) {
        //     $validation_rules["premium.{$owner}"] = 'required';
        //     $validation_rules["amount.{$owner}"] = "required_with_all:date.{$owner},method.{$owner}";
        //     $validation_rules["date.{$owner}"] = "required_with_all:amount.{$owner},method.{$owner}";
        //     $validation_rules["method.{$owner}"] = "required_with_all:amount.{$owner},date.{$owner}";
        // }

        // $this->validate($request, $validation_rules);

        //print_r($request->amount[$owner]); exit;
        $company = $request->user()->company;
        //$product = $company->products()->find($request->product);
        $product = $request->product;
        $branch = $request->branch;
        $staff = $request->staff;


        foreach($request->owners as $owner) {
            $client = $company->clients()->find($owner);
            $policy = new Policy($request->only(array('beneficiaries', 'expiry', 'payer', 'renewal', 'type','policy_term','payment_term','policy_no','premium_chq_amount','premium_chq_date','premium_chq_no','branch_manager','branch_assist','ecs_mandate','bank_name','pin','bank_branch','ack_date','ack_number','application_no','policy_date','ldob','lmnum','lemail','ndob','nmnum','nemail','deposit_name','premium_amount','point_login','sum_assured','renewal_date')));
            $policy->premium = $request->premium[$owner];
            $policy->special_remarks = str_replace(["\n", "\r\n"], '<br/>', $request->special_remarks);
            $policy->ref_no = strtoupper(Str::random(8));
            $policy->renewal_date = $request->renewal;
            $policy->client()->associate($client);
            $policy->product()->associate($product);
            $policy->branch()->associate($branch);
            $policy->staff()->associate($staff);
            $policy->save();

            // Save custom_fields
            if(isset($request->custom_fields)) {
                foreach($request->custom_fields as $custom_field) {
                    $custom_field = new CustomField(array(
                        'label' => $custom_field['label'],
                        'type'  => $custom_field['type'],
                        'uuid'  => $custom_field['uuid'],
                        'value' => isset($custom_field['value']) ? (is_array($custom_field['value']) ? json_encode($custom_field['value']) : $custom_field['value']) : null
                    ));
                    $custom_field->model()->associate($policy);
                    $custom_field->save();
                }
            }

            if(isset($request->amount[$owner]) && !empty($request->amount[$owner])) {
                if(empty($request->method[$owner])){
                    $method = 'cheque';
                } else {
                    $method = $request->method[$owner];
                }
                $payment = new Payment(array(
                    'amount'    => $request->amount[$owner],
                    'date'      => $request->date[$owner],
                    'method'    => $method
                ));
                $payment->client()->associate($client);
                $payment->policy()->associate($policy);
                $payment->save();
            }
        }

        return redirect()->back()->with('success', trans('policies.message.success.added', array(
            'count' => count($request->owners)
        )));
    }


    /**
     * Delete a policy
     *
     * @param  \Illuminate\Http\Policy  $policy
     * @return \Illuminate\Http\Response
     */
    public function delete(Policy $policy) {
        $policy->delete();
        return redirect()->route('policies.all')->with('status', trans('policies.message.info.deleted'));
    }

    /**
     * Update a policy
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Policy  $policy
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Policy $policy) {
        //echo $request->nemail;exit;
        $this->validate($request, array(
            'beneficiaries'     => 'max:512|string',
            'custom_fields'     => 'array|sometimes',
            //'expiry'            => 'date|required',
            //'payer'             => 'max:32|required|string',
            'premium'           => 'numeric|required',
            'product'           => 'exists:products,id|integer|required',
            'branch'            => 'string',
            'staff'             => 'string',
            //'renewal'           => 'date|required',
            'special_remarks'   => 'max:2048|string',
            'type'              => 'in:annually,monthly,weekly,quarterly,half yearly|required|string',
            'policy_term'       => 'integer|required',
            'premium_chq_amount'    => 'integer|required',
            'premium_chq_date'      => 'date',
            'premium_chq_no'        => 'integer|required',
            //'branch_manager'        => 'required',
            //'branch_assist'         => 'required',
            'ecs_mandate'           => 'required',
            'bank_name'             => 'required',
            'pin'                   => 'required',
            'bank_branch'           => 'required',
            'ack_date'              => 'required',
            'ack_number'            => 'required',
            'application_no'        => 'required',
            //'policy_no'           => 'required',
            'policy_date'           => 'required',
            'payment_term'          => 'integer|required',
            'point_login'           => 'required',
            'sum_assured'           => 'integer',
        ));
        $company = $policy->client->company;

        $policy->beneficiaries      = $request->beneficiaries ?: null;
        $policy->expiry             = $request->expiry;
        $policy->payer              = $request->payer;
        $policy->premium            = $request->premium;
        $policy->renewal            = $request->renewal;
        $policy->special_remarks    = str_replace(["\n", "\r\n"], '<br/>', $request->special_remarks) ?: null;
        $policy->type               = $request->type;
        $policy->branch_id          = $request->branch;
        $policy->staff_id           = $request->staff;
        $policy->policy_term        = $request->policy_term;
        $policy->premium_chq_amount    = $request->premium_chq_amount;
        $policy->premium_chq_date      = $request->premium_chq_date;
        $policy->premium_chq_no        = $request->premium_chq_no;
        $policy->branch_manager        = $request->branch_manager;
        $policy->branch_assist         = $request->branch_assist;
        $policy->ecs_mandate           = $request->ecs_mandate;
        $policy->bank_name             = $request->bank_name;
        $policy->pin                   = $request->pin;
        $policy->bank_branch           = $request->bank_branch;
        $policy->ack_date              = $request->ack_date;
        $policy->ack_number            = $request->ack_number;
        $policy->application_no        = $request->application_no;
        $policy->policy_no             = $request->policy_no;
        $policy->policy_date           = $request->policy_date;
        $policy->payment_term          = $request->payment_term;

        $policy->ldob                   = $request->ldob;
        $policy->lmnum                  = $request->lmnum;
        $policy->lemail                 = $request->lemail;
        $policy->ndob                   = $request->ndob;
        $policy->nmnum                  = $request->nmnum;
        $policy->nemail                 = $request->nemail;
        $policy->deposit_name           = $request->deposit_name;
        $policy->premium_amount         = $request->premium_amount;
        $policy->point_login            = $request->point_login;
        $policy->sum_assured            = $request->sum_assured;
        $policy->renewal_date           = $request->renewal;



        if($request->product != $policy->product->id) {
            $product = $company->products()->find($request->product);
            $policy->product()->associate($product);
        }
        $policy->save();

        // Save custom_fields
        if(isset($request->custom_fields)) {
            foreach($request->custom_fields as $custom_field) {
                $policy->customFields()->where('uuid', $custom_field['uuid'])->update(array(
                    'value' => isset($custom_field['value']) ? (is_array($custom_field['value']) ? json_encode($custom_field['value']) : $custom_field['value']) : null
                ));
            }
        }

        return redirect()->back()->with('success', trans('policies.message.success.edited'));
    }

    /**
     * Get all policies
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAll(Request $request) {
        $currencies_by_code = collect(config('insura.currencies.list'))->keyBy('code');
        $filters = $this->getFilters($request);
        $user = $request->user();
        $view_data = array();

        switch($user->role) {
            case 'super':
                // Eager load all relationships to avoid N+1 queries
                // Query directly from Policy to avoid duplicate columns from hasManyThrough + withSum
                $view_data['policies'] = Policy::whereHas('product', function($query) use ($user) {
                        $query->where('company_id', $user->company->id);
                    })
                    ->with(['branch', 'client', 'product']) // Eager load relationships
                    ->withSum('payments', 'amount') // Calculate sum in database
                    ->insuraFilter($filters)
                    ->paginate(15);
                //$view_data['policies']  = DB::table('policies')->get();
                $view_data['clients']  = $user->company->clients()->get();
                $view_data['products'] = DB::table('products')->get();
                $view_data['branches'] = Branches::all();
                $view_data['staffs'] = User::where('role','=','staff')->where('staff_role','=','S')->get();
                $view_data['branch_manger'] = User::where('staff_role','=','BM')->get();
                $view_data['branch_assist'] = User::where('staff_role','=','BA')->get();

                //$view_data['clients'] = DB::table('users')->where('role','=','client')->get();
                break;
            case 'admin':
            case 'staff':
                $view_data['policies'] = $user->company->policies()->with(['branch', 'client', 'product'])->withSum('payments', 'amount')->where('branch_id',$request->user()->branch_id)->insuraFilter($filters)->paginate(15);
                // $view_data['policies'] = $user->company->policies()->insuraFilter($filters)->paginate(15);
                //$view_data['policies']  = DB::table('policies')->get();
                $view_data['clients']  = $user->company->clients()->where('branch_id','=',$request->user()->branch_id)->select('users.id', 'users.first_name', 'users.last_name')->get();
                $view_data['products'] = DB::table('products')->get();
                $view_data['branches'] = Branches::where('id',$request->user()->branch_id)->get();
                $view_data['staffs'] = User::where('role','=','staff')->where('branch_id','=',$request->user()->branch_id)->get();
                $view_data['branch_manger'] = User::where('staff_role','=','BM')->where('branch_id','=',$request->user()->branch_id)->get();
                $view_data['branch_assist'] = User::where('staff_role','=','BA')->where('branch_id','=',$request->user()->branch_id)->get();

                //$view_data['clients'] = DB::table('users')->where('role','=','client')->get();
                break;
            case 'broker':
                // Eager load all relationships to avoid N+1 queries
                $view_data['policies'] = $user->inviteePolicies()
                    ->with(['client', 'product', 'branch']) // Eager load relationships
                    ->withSum('payments', 'amount') // Calculate sum in database
                    ->insuraFilter($filters)
                    ->paginate(15);
                $view_data['clients'] = $user->invitees()->get();
                //$view_data['clients'] = DB::table('users')->where('role','=','client')->get();
                break;
            case 'client':
                // Eager load all relationships to avoid N+1 queries
                $view_data['policies'] = $user->policies()
                    ->with(['product', 'branch']) // Eager load relationships
                    ->withSum('payments', 'amount') // Calculate sum in database
                    ->insuraFilter($filters)
                    ->paginate(15);
                break;
        }
        $view_data['policies']->currency_symbol = $currencies_by_code->get($user->company->currency_code)['symbol'];
        $view_data['policies']->transform(function($policy) {
            $policy->paid       = $policy->payments_sum_amount ?? 0;
            $policy->due        = $policy->premium - $policy->premium_chq_amount;
            if($policy->renewal == '0000-00-00' || is_null($policy->renewal_date)) {
                $policy->renewal = '-';
            } else {
                 $policy->renewal = date('d-m-Y', strtotime($policy->renewal_date));
            }
            $time_to_expiry     = strtotime(date('Y-m-d')) - strtotime($policy->expiry);
            $policy->statusClass = $policy->due > 0 ? ($time_to_expiry < 1 ? 'warning' : 'negative') : 'positive';
            return $policy;
        });

        $view_data['policies']->lastOnPreviousPage = ($view_data['policies']->currentPage() - 1) * $view_data['policies']->perPage();
        $view_data['filter'] = count($filters) > 0;
        if($view_data['filter']) {
            $view_data['filters'] = $filters;
        }

       // echo '<pre>';print_r($view_data['filters']);echo'</pre>';exit;

        $view_data['presenter'] = new SemanticUIPresenter($view_data['policies']);
        $view_data['filters'] = $filters;

        return view($user->role . '.policies.all', $view_data);
    }

    /**
     * get valid filters from a request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function getFilters(Request $request) {
        $master = array('due_max','due_min','expiry_from','expiry_to','policy_ref','premium_max','premium_min','product','renewal_from','renewal_to','branch','staff','client','phone','status');
        
        // Initialize all filter keys with null to prevent undefined array key errors
        $filters = array_fill_keys($master, null);
        
        // Merge in actual values from request
        $requestFilters = collect($request->only($master))->reject(function($val) {
            return is_null($val) || empty($val);
        })->toArray();
        
        $filters = array_merge($filters, $requestFilters);
        return $filters;
    }

    /**
     * Get one policy
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Policy  $policy
     * @return \Illuminate\Http\Response
     */
    public function getOne(Request $request, Policy $policy) {
        $user = $request->user();
        // Eager load relationships to avoid N+1 queries
        $policy->load(['client.company', 'product', 'branch']);
        $policy->loadSum('payments', 'amount');
        
        $policy->currency_symbol = collect(config('insura.currencies.list'))->keyBy('code')->get($policy->client->company->currency_code)['symbol'];
        $policy->paid = $policy->payments_sum_amount ?? 0;
        $policy->due = $policy->premium - $policy->paid;
        $policy->active = (time() - strtotime($policy->expiry)) < 0;
        $policy->statusClass = $policy->due > 0 ? ($policy->active ? 'orange' : 'red') : 'green';
        $policy->customFields->transform(function($custom_field) use ($policy) {
            $custom_field_metadata = collect($policy->product->company->custom_fields_metadata)->where('uuid', $custom_field->uuid)->first();
            if(isset($custom_field_metadata->required)) {
                $custom_field->required = true;
            }
            if($custom_field->type === 'select') {
                $custom_field->value = json_decode($custom_field->value);
            }
            return $custom_field;
        });
        $view_data = array(
            'policy' => $policy
        );
        if($user->role === 'super') {
            $view_data['companies']     = Product::all();
            $view_data['branches']      = Branches::all();
            $view_data['staffs']        = User::where('role','=','staff')->where('staff_role','=','S')->get();
            $view_data['branch_manger'] = User::where('staff_role','=','BM')->get();
            $view_data['branch_assist'] = User::where('staff_role','=','BA')->get();
            $view_data['clients']  = $user->company->clients()->get();
        }
        if($user->role === 'staff') {

            $view_data['branches']      = Branches::where('id','=',$request->user()->branch_id)->get();
            $view_data['staffs'] = User::where('role','=','staff')->where('branch_id','=',$request->user()->branch_id)->get();
            $view_data['branch_manger'] = User::where('staff_role','=','BM')->where('branch_id','=',$request->user()->branch_id)->get();
            $view_data['branch_assist'] = User::where('staff_role','=','BA')->where('branch_id','=',$request->user()->branch_id)->get();
            $view_data['clients']  = $user->company->clients()->where('branch_id','=',$request->user()->branch_id)->get();
        }




        return view($user->role . '.policies.one', $view_data);
    }

}
