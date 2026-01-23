<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Mproduct;
use App\Pagination\SemanticUIPresenter;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use DB;

class MproductController extends Controller {

    public function __construct() {
        $this->middleware('auth');
        $this->middleware('localize_auth');
    }

    public function add(Request $request) {
        $company = null;

        $this->validate($request, [
            'product_name'        => 'required|string',
            'status'              => 'required',
            'company_name'        => 'required'

        ]);
        $products_id = DB::table('mproduct')->insertGetId([
            'product_name'     => $request->product_name ?: null,
            'company_name'     => $request->company_name ?: null,
            'status'           => $request->status,

        ]);
        return redirect()->back()->with('success', 'New Mutual Fund Product Added');
    }

    public function delete(Request $request,Mproduct $mproducts) {
        //print_r($request->branch);exit;
        DB::table('mproduct')->where('id', '=',$request->mproduct)->delete();
        //$branches->delete();
        return redirect()->back()->with('status', 'Product Deleted');
    }

    public function edit(Request $request,Mproduct $mproducts) {
        //print_r($request->id);exit;

        $this->validate($request, [
            'product_name'        => 'required|string',
            'status'              => 'required',
            'company_name'        => 'required'

        ]);
        try{
            $mproducts = Mproduct::findOrFail($request->mproduct);
        }catch(ModelNotFoundException $e) {
            $mproducts = $mproducts->id;
        }
        $mproducts->product_name     = $request->product_name;
        $mproducts->company_name     = $request->company_name;
        $mproducts->status           = $request->status;
        $mproducts->save();

        return redirect()->back()->with('success', 'Product edited');
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
        $view_data['mproducts']->appends($filters);
        $view_data['presenter'] = new SemanticUIPresenter($view_data['mproducts']);


        return view('super.mproducts', $view_data);
    }

    /**
     * Get one mproduct
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Mproduct  $mproduct
     * @return \Illuminate\Http\Response
     */
    public function getOne(Request $request, Mproduct $mproduct) {
        // Since there's no dedicated view for single mproduct, redirect back to list
        // This method exists for Laravel 8 route model binding compatibility
        return redirect()->route('mproducts.all');
    }

    protected function getFilters(Request $request) {
        $master = array('branch_name','branch_code','branch_phone');
        $filters = collect($request->only($master))->reject(function($val) {
            return is_null($val) || empty($val);
        })->toArray();
        return $filters;
    }
    
    public function suggest(Request $request) {
        $query =  $request->name;
        $data = DB::table('companies')
                ->where('name', 'like', '%'.$query.'%')
                ->get();
        $count = count($data);
        ?>
        <select name="list" size="<?php echo $count;?>">
        <?php
        foreach ($data as  $value) { ?>

            <option><?php echo $value->name;?></option>

        <?php } ?>
        </select>
        <?php

    }


}
