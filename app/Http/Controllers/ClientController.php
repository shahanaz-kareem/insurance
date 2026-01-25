<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\SendWelcomeEmail;
use App\Models\Branches as Branch;
use App\Models\Company;
use App\Models\CustomField;
use App\Models\Funds;
use App\Models\Commission;
use App\Models\Mproduct;
use App\Models\Product;
use App\Models\User;
use App\Pagination\SemanticUIPresenter;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Storage;

class ClientController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Client Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the management and update of existing clients Why
    | don't you explore it?
    |
    */

    /**
     * Create a new client controller instance.
     * 
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('localize_auth');
    }

    /**
     * Add a client
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request) {
        $this->validate($request, [
            'address'       => 'max:256|string',
            'birthday'      => 'date',
            'company_id'    => 'exists:companies,id|integer|sometimes',
            'custom_fields' => 'array|sometimes',
            'email'         => "email|required|unique:users",
            'first_name'    => 'max:32|min:3|string|required',
            'inviter_id'    => 'exists:users,id|integer|sometimes',
            'last_name'     => 'max:32|min:3|string',
            'phone'         => 'max:16|string',
            'profile_image' => 'image'
        ]);
        $user = $request->user();
        if(isset($request->company_id)) {
            $company = Company::find($request->company_id);
        }else {
            $company = $user->company;
        }
        if(isset($request->inviter_id)) {
            $inviter = User::find($request->inviter_id);
        }else {
            $inviter = $user;
        }
        $profile_image_filename = 'default-profile.jpg';
        if ($request->hasFile('profile_image') && $request->file('profile_image')->isValid()) {
            $filename = Str::random(7) . '-profile.' . str_replace('jpeg', 'jpg', $request->file('profile_image')->guessExtension());
            try{
                $request->file('profile_image')->move(storage_path('app/images/users/'), $filename);
                $profile_image_filename = $filename;
            }catch(FileException $e) {
                return redirect()->back()->withErrors([
                    trans('clients.message.errors.file') . $request->file('profile_image')->getClientOriginalName()
                ])->withInput();
            }
        }

        try {
            $client = $company->users()->create(array(
                'address'                   => $request->address ?: null,
                'birthday'                  => $request->birthday ?: null,
                'email'                     => $request->email,
                'first_name'                => $request->first_name,
                'last_name'                 => $request->last_name ?: null,
                'locale'                    => $user->locale,
                'phone'                     => $request->phone ?: null,
                'profile_image_filename'    => $profile_image_filename
            ));
            
            // Verify client was created
            if (!$client || !$client->id) {
                throw new \Exception('Client creation failed - no ID returned');
            }
            
            $client->password = 'InsuraPasswordsAreLongButNeedToBeSetByInvitedUsersSuchAsThis';
            $client->role = 'client';
            $client->inviter()->associate($inviter);
            
            if (!$client->save()) {
                throw new \Exception('Failed to save client password and role');
            }
            
            // Refresh to ensure all data is loaded
            $client->refresh();

            // Save custom_fields
            if(isset($request->custom_fields)) {
                foreach($request->custom_fields as $custom_field) {
                    $custom_field = new CustomField(array(
                        'label' => $custom_field['label'],
                        'type'  => $custom_field['type'],
                        'uuid'  => $custom_field['uuid'],
                        'value' => isset($custom_field['value']) ? (is_array($custom_field['value']) ? json_encode($custom_field['value']) : $custom_field['value']) : null
                    ));
                    $custom_field->model()->associate($client);
                    $custom_field->save();
                }
            }

            // Dispatch job to send welcome email - wrap in try-catch so it doesn't break client creation
            try {
                $token = hash_hmac('sha256', Str::random(40), config('app.key'));
                DB::table(config('auth.password.table'))->insert(['email' => $client->email, 'token' => $token, 'created_at' => new Carbon]);
                dispatch((new SendWelcomeEmail($token, $client))->onQueue('emails')->delay(10));
            } catch (\Exception $e) {
                // Log email dispatch error but don't fail the client creation
                try {
                    \Log::error('Failed to dispatch welcome email for client: ' . $client->email, [
                        'error' => $e->getMessage(),
                        'client_id' => $client->id
                    ]);
                } catch (\Exception $logException) {
                    // If logging fails, at least log to error_log
                    error_log('Failed to dispatch welcome email for client: ' . $client->email . ' - Error: ' . $e->getMessage());
                }
            }

            return redirect()->back()->with('success', trans('clients.message.success.added'));
        } catch (\Exception $e) {
            try {
                \Log::error('Failed to create client', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'request_data' => $request->except(['password', '_token'])
                ]);
            } catch (\Exception $logException) {
                // If logging fails, at least log to error_log
                error_log('Failed to create client - Error: ' . $e->getMessage());
            }
            return redirect()->back()->withErrors([
                'error' => trans('clients.message.errors.create_failed') ?: 'Failed to create client. Please try again.'
            ])->withInput();
        }
    }

    /**
     * Delete a client
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $client
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, User $client) {
        $client->delete();
        return redirect()->route('clients.all')->with('status', trans('clients.message.info.deleted'));
    }

    /**
     * Update a client's profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $client
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, User $client) {
        $this->validate($request, array(
            'address'       => 'max:256|string',
            'birthday'      => 'date',
            'company_id'    => 'exists:companies,id|integer|sometimes',
            'custom_fields' => 'array|sometimes',
            'email'         => "email|required|unique:users,email,{$client->id}",
            'first_name'    => 'max:32|min:3|string|required',
            'inviter'       => 'exists:users,id|integer|sometimes',
            'last_name'     => 'max:32|min:3|string',
            'phone'         => 'max:16|string',
            'profile_image' => 'image'
        ));

        if ($request->hasFile('profile_image') && $request->file('profile_image')->isValid()) {
            $profile_image_filename = Str::random(7) . '-profile.' . str_replace('jpeg', 'jpg', $request->file('profile_image')->guessExtension());
            try{
                $request->file('profile_image')->move(storage_path('app/images/users/'), $profile_image_filename);
                $profile_image_storage_path = 'images/users/' . $client->profile_image_filename;
                if($client->profile_image_filename !== 'default-profile.jpg' && Storage::has($profile_image_storage_path)) {
                    Storage::delete($profile_image_storage_path);
                }
                $client->profile_image_filename = $profile_image_filename;
            }catch(FileException $e) {
                return redirect()->back()->withErrors([
                    trans('clients.message.errors.file') . $request->file('profile_image')->getClientOriginalName()
                ]);
            }
        }

        $client->address                 = $request->address ?: null;
        $client->birthday                = $request->birthday ?: null;
        $client->email                   = $request->email;
        $client->first_name              = $request->first_name;
        $client->last_name               = $request->last_name ?: null;
        $client->phone                   = $request->phone ?: null;
        if(!is_null($request->company_id) && $request->company_id != $client->company->id) {
            // Delete current custom fields
            $client->customFields->each(function($custom_field) {
                $custom_field->delete();
            });
            // Save new custom fields
            if(isset($request->custom_fields)) {
                foreach($request->custom_fields as $custom_field) {
                    $custom_field = new CustomField(array(
                        'label' => $custom_field['label'],
                        'type'  => $custom_field['type'],
                        'uuid'  => $custom_field['uuid'],
                        'value' => isset($custom_field['value']) ? (is_array($custom_field['value']) ? json_encode($custom_field['value']) : $custom_field['value']) : null
                    ));
                    $custom_field->model()->associate($client);
                    $custom_field->save();
                }
            }
            // Associate new company
            $client->company()->associate(Company::findOrFail($request->company_id));
        }else if(!is_null($request->company_id) && $request->company_id == $client->company->id) {
            // Save custom_fields
            if(isset($request->custom_fields)) {
                foreach($request->custom_fields as $custom_field) {
                    $client->customFields()->where('uuid', $custom_field['uuid'])->update(array(
                        'value' => isset($custom_field['value']) ? (is_array($custom_field['value']) ? json_encode($custom_field['value']) : $custom_field['value']) : null
                    ));
                }
            }
        }
        // Associate new inviter
        if(!is_null($request->inviter) && $request->inviter != $client->inviter->id) {
            $client->inviter()->associate(User::findOrFail($request->inviter));
        }
        $client->save();

        return redirect()->back()->with('success', trans('clients.message.success.edited'));
    }

    /**
     * Get all clients
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAll(Request $request) {
        $currencies_by_code = collect(config('insura.currencies.list'))->keyBy('code');
        $user = $request->user();
        $view_data = array();
        $view_data['filter'] = $request->has('client') || $request->has('phone') || $request->has('branch') || $request->has('ref_no') || $request->has('policy_ref');
        
        // Initialize all filter keys with empty strings to prevent undefined array key errors
        $view_data['filters'] = [
            'client' => '',
            'phone' => '',
            'branch' => '',
            'ref_no' => '',
            'policy_ref' => '',
            'client_ref' => ''
        ];
        
        // Merge request data with defaults
        $view_data['filters'] = array_merge($view_data['filters'], $request->only(['client', 'phone', 'branch', 'ref_no', 'policy_ref', 'client_ref']));
        
        // Build base query based on user role
        $query = null;
        switch($user->role) {
            case 'super':
                $view_data['companies'] = Company::all()->map(function($company) {
                    $company->custom_fields_metadata = collect(json_decode($company->custom_fields_metadata ?: '[]'));
                    return $company;
                });
                $query = User::client()->withStatus()->with('company');
                break;
            case 'admin':
            case 'staff':
                $query = $user->company->clients()->withStatus();
                break;
            case 'broker':
                $query = $user->invitees()->withStatus();
                break;
        }
        
        // Apply filters to query
        if (!empty($view_data['filters']['client'])) {
            $query->where('users.id', $view_data['filters']['client']);
        }
        if (!empty($view_data['filters']['phone'])) {
            $query->where('users.phone', 'LIKE', '%' . $view_data['filters']['phone'] . '%');
        }
        if (!empty($view_data['filters']['branch'])) {
            $query->where('users.branch_id', $view_data['filters']['branch']);
        }
        if (!empty($view_data['filters']['ref_no']) || !empty($view_data['filters']['policy_ref'])) {
            $ref_no = $view_data['filters']['ref_no'] ?: $view_data['filters']['policy_ref'];
            $query->whereHas('policies', function($q) use ($ref_no) {
                $q->where('ref_no', 'LIKE', '%' . $ref_no . '%')
                  ->orWhere('policy_no', 'LIKE', '%' . $ref_no . '%');
            });
        }
        if (!empty($view_data['filters']['client_ref'])) {
            $query->whereHas('policies', function($q) use ($view_data) {
                $q->where('ref_no', 'LIKE', '%' . $view_data['filters']['client_ref'] . '%')
                  ->orWhere('policy_no', 'LIKE', '%' . $view_data['filters']['client_ref'] . '%');
            });
        }
        
        // Apply eager loading, aggregations, and paginate
        switch($user->role) {
            case 'super':
                $view_data['clients'] = $query
                    ->withSum('policies', 'premium')
                    ->withSum('payments', 'amount')
                    ->withCount('policies')
                    ->paginate(8)
                    ->appends($request->only(['client', 'phone', 'branch', 'ref_no', 'policy_ref', 'client_ref']));
                // Load all users for dropdown WITHOUT their policies to speed up
                $view_data['allusers'] = User::client()->withStatus()->get();
                $view_data['clients']->transform(function($client) use($currencies_by_code) {
                    $client->currency_symbol = $currencies_by_code->get($client->company->currency_code)['symbol'];
                    $client->premiums = $client->policies_sum_premium ?? 0;
                    $client->paid = $client->payments_sum_amount ?? 0;
                    $client->due = $client->premiums - $client->paid;
                    return $client;
                });
                break;
            case 'admin':
            case 'staff':
                $view_data['clients'] = $query
                    ->withSum('policies', 'premium')
                    ->withSum('payments', 'amount')
                    ->withCount('policies')
                    ->paginate(8)
                    ->appends($request->only(['client', 'phone', 'branch', 'ref_no', 'policy_ref', 'client_ref']));
                // Load all users for dropdown WITHOUT their policies to speed up
                $view_data['allusers'] = $user->company->clients()->withStatus()->select('users.id', 'users.first_name', 'users.last_name')->get();
                $view_data['clients']->currency_symbol = $currencies_by_code->get($user->company->currency_code)['symbol'];
                break;
            case 'broker':
                $view_data['clients'] = $query
                    ->withSum('policies', 'premium')
                    ->withSum('payments', 'amount')
                    ->withCount('policies')
                    ->paginate(8)
                    ->appends($request->only(['client', 'phone', 'branch', 'ref_no', 'policy_ref', 'client_ref']));
                // Load all users for dropdown WITHOUT their policies to speed up
                $view_data['allusers'] = $user->invitees()->withStatus()->get();
                $view_data['clients']->currency_symbol = $currencies_by_code->get($user->company->currency_code)['symbol'];
                break;
        }
        $view_data['clients']->transform(function($client) {
            $client->premiums = $client->policies_sum_premium ?? 0;
            $client->paid = $client->payments_sum_amount ?? 0;
            $client->due = $client->premiums - $client->paid;
            return $client;
        });
        
        // Add lastOnPreviousPage for pagination display (like other pages)
        $view_data['clients']->lastOnPreviousPage = ($view_data['clients']->currentPage() - 1) * $view_data['clients']->perPage();
        
        // Create branch map for O(1) lookup instead of O(n) loop in view
        if ($user->role === 'super') {
            $branches = \App\Models\Branches::all();
        } else {
            $branches = $user->company->branches()->get();
        }
        $view_data['branchMap'] = $branches->keyBy('id');
        // Keep branches for backward compatibility
        $view_data['branches'] = $branches;
        
        // Add SemanticUIPresenter for consistent pagination across all pages
        $view_data['presenter'] = new SemanticUIPresenter($view_data['clients']);
        
        return view($user->role . '.clients.all', $view_data);
    }

    /**
     * Get one client
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\User  $client
     * @return \Illuminate\Http\Response
     */
    public function getOne(Request $request, User $client) {
        $user = $request->user();
        $client->company->custom_fields_metadata = collect(json_decode($client->company->custom_fields_metadata ?: '[]'));
        $client->currency_symbol = collect(config('insura.currencies.list'))->keyBy('code')->get($client->company->currency_code)['symbol'];
        $client->customFields->transform(function($custom_field) use($client) {
            $custom_field_metadata = $client->company->custom_fields_metadata->where('uuid', $custom_field->uuid)->first();
            if(isset($custom_field_metadata->required)) {
                $custom_field->required = true;
            }
            return $custom_field;
        });
        // Eager load policies with payment sums to avoid N+1 queries
        $policies = $client->policies()->withSum('payments', 'amount')->get();
        
        $view_data = array(
            'client'    => $client,
            'clients'   => $client->company->clients()->get(),  // All clients for selection dropdowns
            'policies'  => $policies->map(function($policy) {
                $policy->paid = $policy->payments_sum_amount ?? 0;
                $policy->due = $policy->premium - $policy->paid;
                $time_to_expiry = strtotime(date('Y-m-d')) - strtotime($policy->expiry);
                $policy->statusClass = $policy->due > 0 ? ($time_to_expiry < 1 ? 'warning' : 'negative') : 'positive';
                return $policy;
            }),
            // Eager load mproduct relationship to avoid N+1 queries
            'mutualfunds' => Funds::where('client_id', $client->id)->with('mproduct')->get(),
            'commissions' => Commission::where('client_id', $client->id)->get(),
            'branches' => $client->company->branches()->get(),
            // Load all mproducts for the form dropdown
            'mproducts' => Mproduct::all(),
            // Load all products for the form dropdown
            'products' => Product::all(),
            // Load all staff members for the form dropdown
            'staffs' => User::where('company_id', $client->company_id)->where('role', 'staff')->get(),
            // Load all branch managers for the form dropdown
            'branch_manger' => User::where('company_id', $client->company_id)->where('role', 'staff')->get(),
            // Load all branch assistants for the form dropdown
            'branch_assist' => User::where('company_id', $client->company_id)->where('role', 'staff')->get(),
            // Currency symbol for use in forms and views
            'currency_symbol' => $client->currency_symbol
        );
        if($user->role === 'super') {
            $view_data['companies'] = Company::all()->map(function($company) {
                $company->custom_fields_metadata = collect(json_decode($company->custom_fields_metadata ?: '[]'));
                return $company;
            });
        }
        
        return view($user->role . '.clients.one', $view_data);
    }
}
