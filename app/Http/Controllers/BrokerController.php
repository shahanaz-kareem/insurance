<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Jobs\SendWelcomeEmail;
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

class BrokerController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Broker Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the management and update of existing brokers Why
    | don't you explore it?
    |
    */

    /**
     * Create a new broker controller instance.
     * 
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('localize_auth');
    }

    /**
     * Add a broker
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request) {
        $this->validate($request, [
            'address'           => 'max:256|string',
            'birthday'          => 'date',
            'commission_rate'   => 'numeric|required',
            'company_id'        => 'integer|sometimes',
            'email'             => "email|required|unique:users",
            'first_name'        => 'max:32|min:3|string|required',
            'last_name'         => 'max:32|min:3|string',
            'phone'             => 'max:16|string',
            'profile_image'     => 'image'
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
                    trans('brokers.message.errors.file', array(
                        'filename' => $request->file('profile_image')->getClientOriginalName()
                    ))
                ));
            }
        }

        $broker = $company->users()->create(array(
            'address'                   => $request->address ?: null,
            'birthday'                  => $request->birthday ?: null,
            'commission_rate'           => $request->commission_rate,
            'email'                     => $request->email,
            'first_name'                => $request->first_name,
            'last_name'                 => $request->last_name ?: null,
            'locale'                    => $user->locale,
            'phone'                     => $request->phone ?: null,
            'profile_image_filename'    => $profile_image_filename
        ));
        $broker->password = 'InsuraPasswordsAreLongButNeedToBeSetByInvitedUsersSuchAsThis';
        $broker->role = 'broker';
        $broker->save();

        // Dispatch job to send welcome email
        $token = hash_hmac('sha256', Str::random(40), config('app.key'));
        DB::table(config('auth.password.table'))->insert(['email' => $broker->email, 'token' => $token, 'created_at' => new Carbon]);
        dispatch((new SendWelcomeEmail($token, $broker))->onQueue('emails')->delay(10));

        return redirect()->back()->with('success', trans('brokers.message.success.added'));
    }

    /**
     * Delete a broker
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $broker
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, User $broker) {
        $broker->delete();
        return redirect()->route('brokers.all')->with('status', trans('brokers.message.info.deleted'));
    }

    /**
     * Update a broker's profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $broker
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, User $broker) {
        $this->validate($request, array(
            'address'           => 'max:256|string',
            'birthday'          => 'date',
            'commission_rate'   => 'numeric|required',
            'company_id'        => 'integer|sometimes',
            'email'             => "email|required|unique:users,email,{$broker->id}",
            'first_name'        => 'max:32|min:3|string|required',
            'last_name'         => 'max:32|min:3|string',
            'phone'             => 'max:16|string',
            'profile_image'     => 'image'
        ));

        if ($request->hasFile('profile_image') && $request->file('profile_image')->isValid()) {
            $profile_image_filename = Str::random(7) . '-profile.' . str_replace('jpeg', 'jpg', $request->file('profile_image')->guessExtension());
            try{
                $request->file('profile_image')->move(storage_path('app/images/users/'), $profile_image_filename);
                $profile_image_storage_path = 'images/users/' . $broker->profile_image_filename;
                if($broker->profile_image_filename !== 'default-profile.jpg' && Storage::has($profile_image_storage_path)) {
                    Storage::delete($profile_image_storage_path);
                }
                $broker->profile_image_filename = $profile_image_filename;
            }catch(FileException $e) {
                return redirect()->back()->withErrors(array(
                    trans('brokers.message.errors.file', array(
                        'filename' => $request->file('profile_image')->getClientOriginalName()
                    ))
                ));
            }
        }

        $broker->address                 = $request->address ?: null;
        $broker->birthday                = $request->birthday ?: null;
        $broker->commission_rate         = $request->commission_rate;
        $broker->email                   = $request->email;
        $broker->first_name              = $request->first_name;
        $broker->last_name               = $request->last_name ?: null;
        $broker->phone                   = $request->phone ?: null;
        if(!is_null($request->company_id)) {
            try {
                $company = Company::findOrFail($request->company_id);
                $broker->company()->associate($company);
            }catch(ModelNotFoundException $e) {
                return redirect()->back()->withErrors(array(
                    trans('companies.message.error.missing')
                ))->withInput();
            }
        }
        $broker->save();

        return redirect()->back()->with('success', trans('brokers.message.success.edited'));
    }

    /**
     * Get all brokers
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAll(Request $request) {
        $currencies_by_code = collect(config('insura.currencies.list'))->keyBy('code');
        $user = $request->user();
        $view_data = array();
        if($user->role === 'super') {
            $view_data['companies'] = Company::all();
            // Use withSum to avoid N+1 queries
            $view_data['brokers'] = User::broker()
                ->withStatus()
                ->with('company') // Eager load company to avoid N+1
                ->withSum('inviteePolicies', 'premium') // Calculate sum in database
                ->withSum('inviteePayments', 'amount') // Calculate sum in database
                ->simplePaginate(8);
            $view_data['brokers']->transform(function($broker) use($currencies_by_code) {
                $broker->currency_symbol = $currencies_by_code->get($broker->company->currency_code)['symbol'];
                $broker->sales = $broker->invitee_policies_sum_premium ?? 0;
                $broker->commission = ($broker->commission_rate / 100) * $broker->sales;
                $broker->paid = $broker->invitee_payments_sum_amount ?? 0;
                $broker->due = $broker->sales - $broker->paid;
                return $broker;
            });
        }else {
            // Use withSum to avoid N+1 queries
            $view_data['brokers'] = $user->company->brokers()
                ->withStatus()
                ->withSum('inviteePolicies', 'premium') // Calculate sum in database
                ->withSum('inviteePayments', 'amount') // Calculate sum in database
                ->simplePaginate(8);
            $view_data['brokers']->currency_symbol = $currencies_by_code->get($user->company->currency_code)['symbol'];
            $view_data['brokers']->transform(function($broker) {
                $broker->sales = $broker->invitee_policies_sum_premium ?? 0;
                $broker->commission = ($broker->commission_rate / 100) * $broker->sales;
                $broker->paid = $broker->invitee_payments_sum_amount ?? 0;
                $broker->due = $broker->sales - $broker->paid;
                return $broker;
            });
        }
        $view_data['presenter'] = new SimpleSemanticUIPresenter($view_data['brokers']);
        return view($user->role . '.brokers.all', $view_data);
    }

    /**
     * Get one broker
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\User  $broker
     * @return \Illuminate\Http\Response
     */
    public function getOne(Request $request, User $broker) {
        $user = $request->user();
        $view_data = array(
            'broker' => $broker
        );
        if($user->role === 'super') {
            $view_data['companies'] = Company::all();
        }
        $view_data['clients'] = $broker->invitees()->get();
        // Eager load policies with payment sums to avoid N+1 queries
        $policies = $broker->inviteePolicies()->withSum('payments', 'amount')->get();
        $view_data['policies'] = $policies->transform(function($policy) {
            $policy->paid = $policy->payments_sum_amount ?? 0;
            $policy->due = $policy->premium - $policy->paid;
            $time_to_expiry = strtotime(date('Y-m-d')) - strtotime($policy->expiry);
            $policy->statusClass = $policy->due > 0 ? ($time_to_expiry < 1 ? 'warning' : 'negative') : 'positive';
            return $policy;
        });
        $view_data['broker']->currency_symbol = collect(config('insura.currencies.list'))->keyBy('code')->get($broker->company->currency_code)['symbol'];
        return view($user->role . '.brokers.one', $view_data);
    }
}
