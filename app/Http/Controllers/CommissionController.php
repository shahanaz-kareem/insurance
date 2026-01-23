<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;


use App\Pagination\SemanticUIPresenter;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use DB;
use App\Models\Commission;
use App\Models\Mproduct;
class CommissionController extends Controller {

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('localize_auth');
    }

    public function add(Request $request) {
        $company = null;

        $this->validate($request, [
            'amount'        => 'required|string',
            'date'          => 'required',
            'notes'         => 'required'

        ]);
        $products_id = DB::table('commission')->insertGetId([
            'amount'     => $request->amount ?: null,

            'date'       => $request->date,
            'notes'      => $request->notes,
            'client_id'   => $request->client_id,

        ]);
        return redirect()->back()->with('success', 'New Commission Added');
    }

    public function delete(Request $request,Commission $commission) {
        //print_r($request->branch);exit;
        DB::table('commission')->where('id', '=',$request->commission)->delete();
        //$branches->delete();
        return redirect()->back()->with('status', 'Commission Deleted');
    }

    public function edit(Request $request,Commission $commission) {
        //print_r($request->amount);exit;

        $this->validate($request, [
            'amount'        => 'required|string',
            'date'          => 'required',
            'notes'         => 'required'

        ]);

        DB::table('commission')
            ->where('id', $request->id)
            ->update([
                'amount' => $request->amount,
                'date'   => $request->date,
                'notes'  => $request->notes
            ]);

        // try{
        //     $commission = Commission::findOrFail($request->commission);
        // }catch(ModelNotFoundException $e) {
        //     $commission = $commission->id;
        // }
        // $commission->amount     = $request->amount;
        // $commission->date       = $request->date;
        // $commission->notes      = $request->notes;
        // $mproducts->save();



        return redirect()->back()->with('success', 'Commission edited');
    }

    public function getAll(Request $request) {
        $user = $request->user();
        $filters = $this->getFilters($request);
        //print_r($filters);
        //exit;
        $view_data = array();
        if($user->role === 'super') {
            // $view_data['companies'] = Company::where('id', '=', 1)->get();
            // $view_data['companies1'] = Company::where('id', '!=', 1)->get();
            $view_data['mproducts'] = Mproduct::paginate(15);

        }else {

            $view_data['mproducts'] = Mproduct::paginate(15);

        }
        $view_data['mproducts']->lastOnPreviousPage = ($view_data['mproducts']->currentPage() - 1) * $view_data['mproducts']->perPage();
        $view_data['filter'] = count($filters) > 0;
        if($view_data['filter']) {
            $view_data['filters'] = $filters;
        }
        //echo '<pre>';print_r($view_data['filters']);echo'</pre>';exit;
        $view_data['presenter'] = new SemanticUIPresenter($view_data['mproducts']);


        return view('super.mproducts', $view_data);
    }
    protected function getFilters(Request $request) {
        $master = array('branch_name','branch_code','branch_phone');
        $filters = collect($request->only($master))->reject(function($val) {
            return is_null($val) || empty($val);
        })->toArray();
        return $filters;
    }


}
