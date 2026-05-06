<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;

class BusinessController extends Controller
{

    public function index()
    {
        return Business::with('services')->get();
    }

    public function store(Request $r)
    {
        $b = Business::create([
            'user_id'=>auth()->id(),
            'name'=>$r->name,
            'phone'=>$r->phone,
            'email'=>$r->email,
            'address'=>$r->address,
            'lat'=>$r->lat,
            'lng'=>$r->lng,
        ]);

        foreach($r->services as $s){
            $b->services()->attach($s['id'],[
                'min_price'=>$s['min_price'],
                'max_price'=>$s['max_price']
            ]);
        }

        return $b;
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
