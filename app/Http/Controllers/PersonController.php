<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Person;
use App\Http\Resources\Person as PersonResource;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $per_page = 3;

    public function index()
    {

        $persons = Person::orderBy('id','desc')->paginate($this->per_page);
        //return collection of person as a resource
        return PersonResource::collection($persons);
    }


    public function search($fname = null,$lname = null){
        if($fname == '0') $fname = '';
        if($lname == '0') $lname = '';
        
        $key = $fname.' '.$lname;

        $persons = Person::search($key)->paginate($this->per_page);
        return PersonResource::collection($persons);
        
    }

    

    public function agefilter($maxAge){
        //select *, DATEDIFF(CURRENT_DATE, birthdate)/365 as age from `persons` where DATEDIFF(CURRENT_DATE, birthdate)/365 < 20 limit 1

        $persons = Person::selectRaw('*, DATEDIFF(CURRENT_DATE, birthdate)/365 as age')->whereRaw('DATEDIFF(CURRENT_DATE, birthdate)/365 < ?', array($maxAge))
        ->orderBy('id','desc')
        ->paginate($this->per_page);

        return PersonResource::collection($persons);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $person = $request->isMethod('put') ? Person::findOrFail($request->id) : new Person;
        
        $person->id = $request->input('id');
        $person->firstName = $request->input('firstName');
        $person->lastName = $request->input('lastName');
        $person->birthdate = $request->input('birthdate');
        
        if($person->save()){
            return new PersonResource($person);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $person = Person::findOrFail($id);

        return new PersonResource($person);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $person = Person::findOrFail($id);

        if($person->delete()){
            return new PersonResource($person);
        }
        
    }
}
