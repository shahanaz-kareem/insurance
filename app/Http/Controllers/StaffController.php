<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\SendWelcomeEmail;
use App\Models\Branches;
use App\Models\Company;
use App\Models\User;
use App\Pagination\SimpleSemanticUIPresenter;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Storage;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class StaffController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Staff Member Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the management and update of existing staff Why
    | don't you explore it?
    |
    */

    /**
     * Create a new staff controller instance.
     * 
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('localize_auth');
    }

    /**
     * Add a staff member
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request) {
    
        $this->validate($request, [
            'address'           => 'max:256|string|nullable',
            'age'                => 'integer|nullable',
            'birthday'           => 'date|nullable',
            'branch_id'          => 'integer|nullable',
            'commission_rate'   => 'numeric|required',
            'company_id'        => 'integer|sometimes',
            'doj'                => 'date|nullable',
            'email'             => "email|required|unique:users",
            'first_name'        => 'max:32|min:3|string|required',
            'last_name'         => 'max:32|min:3|string|nullable',
            'phone'             => 'max:16|string|nullable',
            'profile_image'     => 'image|nullable',
            'staff_code'        => 'string|nullable',
            'staff_role'        => 'string|nullable'
        ]);

        
        $user = $request->user();
        $company = null;
       

        try {
            $company = Company::findOrFail($request->company_id);
        }catch(ModelNotFoundException $e) {
            $company = $user->company;
        }
        $password = bcrypt(Str::random(8));

        $profile_image_filename = 'default-profile.jpg';
        if ($request->hasFile('profile_image') && $request->file('profile_image')->isValid()) {
            $profile_image_filename = Str::random(7) . '-profile.' . str_replace('jpeg', 'jpg', $request->file('profile_image')->guessExtension());
            try{
                $request->file('profile_image')->move(storage_path('app/images/users/'), $profile_image_filename);
            }catch(FileException $e) {
                return redirect()->back()->withErrors(array(
                    trans('staff.message.errors.file', array(
                        'filename'  => $request->file('profile_image')->getClientOriginalName()
                    ))
                ));
            }
        }

        // Handle date conversion for birthday (format: d-m-Y to Y-m-d)
        $birthday = !empty($request->birthday) && strtotime($request->birthday) ? date("Y-m-d", strtotime($request->birthday)) : null;
        // Handle date conversion for doj (format: d-m-Y to Y-m-d)
        $doj = !empty($request->doj) && strtotime($request->doj) ? date("Y-m-d", strtotime($request->doj)) : null;
        
        try {
            $staff = $company->users()->create(array(
                'address'                   => !empty($request->address) ? $request->address : null,
                'age'                       => !empty($request->age) ? (int)$request->age : null,
                'birthday'                  => $birthday,
                'branch_id'                 => !empty($request->branch_id) ? (int)$request->branch_id : null,
                'commission_rate'           => $request->commission_rate,
                'doj'                       => $doj,
                'email'                     => $request->email,
                'first_name'                => $request->first_name,
                'last_name'                 => !empty($request->last_name) ? $request->last_name : null,
                'locale'                    => $user->locale,
                'phone'                     => !empty($request->phone) ? $request->phone : null,
                'profile_image_filename'    => $profile_image_filename,
                'staff_code'                => !empty($request->staff_code) ? $request->staff_code : null,
                'staff_role'                => !empty($request->staff_role) ? $request->staff_role : null
            ));
            $staff->password = 'InsuraPasswordsAreLongButNeedToBeSetByInvitedUsersSuchAsThis';
            $staff->role = 'staff';
            $staff->save();
        } catch (\Exception $e) {
            \Log::error('Error creating staff: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->withErrors(['Error creating staff: ' . $e->getMessage()])->withInput();
        }

        // Dispatch job to send welcome email - wrap in try-catch so email failure doesn't break staff creation
        try {
            $token = hash_hmac('sha256', Str::random(40), config('app.key'));
            DB::table(config('auth.password.table'))->insert(['email' => $staff->email, 'token' => $token, 'created_at' => new Carbon]);
            dispatch((new SendWelcomeEmail($token, $staff))->onQueue('emails')->delay(10));
        } catch (\Exception $e) {
            // Log email error but don't fail the staff creation
            \Log::warning('Failed to send welcome email to staff: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', trans('staff.message.success.added'));
    }

    /**
     * Delete a staff member
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $staff
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, User $staff) {
        $staff->delete();
        return redirect()->route('staff.all')->with('status', trans('staff.message.info.deleted'));
    }

    /**
     * Update a staff member's profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $staff
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, User $staff) {
        $this->validate($request, array(
            'address'           => 'max:256|string',
            'age'                => 'integer|nullable',
            'birthday'           => 'date',
            'branch_id'          => 'integer|nullable',
            'commission_rate'   => 'numeric|required',
            'company_id'        => 'integer|sometimes',
            'doj'                => 'date|nullable',
            'email'             => "email|required|unique:users,email,{$staff->id}",
            'first_name'        => 'max:32|min:3|string|required',
            'last_name'         => 'max:32|min:3|string',
            'phone'             => 'max:16|string',
            'profile_image'     => 'image',
            'staff_code'        => 'string|nullable',
            'staff_role'        => 'string|nullable',
            'status'            => 'string|nullable'
        ));

        if ($request->hasFile('profile_image') && $request->file('profile_image')->isValid()) {
            $profile_image_filename = Str::random(7) . '-profile.' . str_replace('jpeg', 'jpg', $request->file('profile_image')->guessExtension());
            try{
                $request->file('profile_image')->move(storage_path('app/images/users/'), $profile_image_filename);
                $profile_image_storage_path = 'images/users/' . $staff->profile_image_filename;
                if($staff->profile_image_filename !== 'default-profile.jpg' && Storage::has($profile_image_storage_path)) {
                    Storage::delete($profile_image_storage_path);
                }
                $staff->profile_image_filename = $profile_image_filename;
            }catch(FileException $e) {
                return redirect()->back()->withErrors(array(
                    trans('staff.message.errors.file', array(
                        'filename'  => $request->file('profile_image')->getClientOriginalName()
                    ))
                ));
            }
        }

        $staff->address                 = $request->address ?: null;
        $staff->age                     = $request->age ?: null;
        // Handle date conversion for birthday (format: d-m-Y to Y-m-d)
        $staff->birthday                = $request->birthday ? (strtotime($request->birthday) ? date("Y-m-d", strtotime($request->birthday)) : null) : null;
        $staff->branch_id               = $request->branch_id ?: null;
        $staff->commission_rate         = $request->commission_rate;
        // Handle date conversion for doj (format: d-m-Y to Y-m-d)
        $staff->doj                     = $request->doj ? (strtotime($request->doj) ? date("Y-m-d", strtotime($request->doj)) : null) : null;
        $staff->email                   = $request->email;
        $staff->first_name              = $request->first_name;
        $staff->last_name               = $request->last_name ?: null;
        $staff->phone                   = $request->phone ?: null;
        $staff->staff_code              = $request->staff_code ?: null;
        $staff->staff_role              = $request->staff_role ?: null;
        
        // Handle status - if inactive, set password to default; if active and password is default, generate new one
        if($request->status === 'I') {
            // Set to inactive (default password)
            $staff->password = 'InsuraPasswordsAreLongButNeedToBeSetByInvitedUsersSuchAsThis';
        } elseif($request->status === 'A') {
            // If activating and current password is the default one, generate a new password
            if($request->has('tokenn') && $request->tokenn === 'InsuraPasswordsAreLongButNeedToBeSetByInvitedUsersSuchAsThis') {
                $staff->password = bcrypt(Str::random(8));
            }
            // Otherwise, keep existing password (don't change it)
        }
        
        if(!is_null($request->company_id)) {
            try {
                $company = Company::findOrFail($request->company_id);
                $staff->company()->associate($company);
            }catch(ModelNotFoundException $e) {
                return redirect()->back()->withErrors(array(
                    trans('companies.message.error.missing')
                ))->withInput();
            }
        }
        $staff->save();

        return redirect()->back()->with('success', trans('staff.message.success.edited'));
    }

    /**
     * Get all staff members
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAll(Request $request) {
        $user = $request->user();
        $view_data = array();
        
        // Pre-load currency symbols for performance
        $currencies_by_code = collect(config('insura.currencies.list'))->keyBy('code');
        
        if($user->role === 'super') {
            $view_data['companies'] = Company::all();
            $view_data['branches'] = Branches::all();
            // Use withSum to avoid N+1 queries - calculate sums in database instead of loading all relationships
            $view_data['staff'] = User::staff()
                ->withStatus()
                ->with('company') // Eager load company to avoid N+1
                ->withSum('inviteePolicies', 'premium') // Calculate sum in database
                ->withSum('inviteePayments', 'amount') // Calculate sum in database
                ->simplePaginate(8);
            
            $view_data['staff']->transform(function($employee) use($currencies_by_code) {
                $employee->currency_symbol = $currencies_by_code->get($employee->company->currency_code)['symbol'];
                $employee->sales = $employee->invitee_policies_sum_premium ?? 0;
                $employee->commission = ($employee->commission_rate / 100) * $employee->sales;
                $employee->paid = $employee->invitee_payments_sum_amount ?? 0;
                $employee->due = $employee->sales - $employee->paid;
                return $employee;
            });
        }else {
            $view_data['branches'] = $user->company->branches()->get();
            // Use withSum to avoid N+1 queries
            $view_data['staff'] = $user->company->staff()
                ->withStatus()
                ->withSum('inviteePolicies', 'premium') // Calculate sum in database
                ->withSum('inviteePayments', 'amount') // Calculate sum in database
                ->simplePaginate(8);
            
            $view_data['staff']->currency_symbol = $currencies_by_code->get($user->company->currency_code)['symbol'];
            
            $view_data['staff']->transform(function($employee) {
                $employee->sales = $employee->invitee_policies_sum_premium ?? 0;
                $employee->commission = ($employee->commission_rate / 100) * $employee->sales;
                $employee->paid = $employee->invitee_payments_sum_amount ?? 0;
                $employee->due = $employee->sales - $employee->paid;
                return $employee;
            });
        }
        
        $view_data['presenter'] = new SimpleSemanticUIPresenter($view_data['staff']);
        return view($user->role . '.staff.all', $view_data);
    }

    /**
     * Get one staff members
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\User  $staff
     * @return \Illuminate\Http\Response
     */
    public function getOne(Request $request, User $staff) {
        $user = $request->user();
        $view_data = array(
            'staff' => $staff
        );
        if($user->role === 'super') {
            $view_data['companies'] = Company::all();
            $view_data['branches'] = Branches::all();
        } else {
            $view_data['branches'] = $user->company->branches()->get();
        }
        $view_data['clients'] = $staff->invitees()->get();
        // Eager load policies with payment sums to avoid N+1 queries
        $policies = $staff->inviteePolicies()->withSum('payments', 'amount')->get();
        $view_data['policies'] = $policies->transform(function($policy) {
            $policy->paid = $policy->payments_sum_amount ?? 0;
            $policy->due = $policy->premium - $policy->paid;
            $time_to_expiry = strtotime(date('Y-m-d')) - strtotime($policy->expiry);
            $policy->statusClass = $policy->due > 0 ? ($time_to_expiry < 1 ? 'warning' : 'negative') : 'positive';
            return $policy;
        });
        $view_data['staff']->currency_symbol = collect(config('insura.currencies.list'))->keyBy('code')->get($staff->company->currency_code)['symbol'];
        return view($user->role . '.staff.one', $view_data);
    }
}
