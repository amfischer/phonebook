<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Person;
use \Storage;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $persons = Person::all();
        foreach ($persons as $p) {
            $p->formatted_phone = Person::formatPhoneNumber($p->phone);
            if (!is_null($p->avatar)) {
                $p->file = base64_encode(Storage::get('./public/avatars/' . $p->avatar));
                $p->avatar = Storage::disk('public')->url('/avatars/' . $p->avatar);
            }     
        }
        return response()->json(['persons' => $persons], 200);
        return view('persons.index', ['persons' => $persons]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'bail|required',
            'last_name' => 'bail|required',
            'title' => 'bail|required',
            'phone' => 'bail|required|numeric|digits:10',
            'avatar' => 'bail|file|image'
        ], [
            'phone.digits' => 'The phone number must be 10 digits'
        ]);

        $person = new Person();
        $person->first_name = $request->first_name;
        $person->last_name = $request->last_name;
        $person->title = $request->title;
        $person->phone = $request->phone;
        $person->save();

        if ($request->has('avatar')) {
            $name = $person->id . '-' . $request->avatar->getClientOriginalName();
            $request->file('avatar')->storeAs('avatars', $name, 'public');

            $person->avatar = $name;
            $person->save();
        }
        

        $person->formatted_phone = Person::formatPhoneNumber($person->phone);
        $flash_message = $person->first_name . ' ' . $person->last_name . ' has been successfully added to the phonebook!';

        return response()->json(['message' => $flash_message], 200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Person $person)
    {
        $request->validate([
            'first_name' => 'bail|required',
            'last_name' => 'bail|required',
            'title' => 'bail|required',
            'phone' => 'bail|required|numeric|digits:10'
        ], [
            'phone.digits' => 'The phone number must be 10 digits'
        ]);

        $person->first_name = $request->first_name;
        $person->last_name = $request->last_name;
        $person->title = $request->title;
        $person->phone = $request->phone;
        $person->save();

        $person->formatted_phone = Person::formatPhoneNumber($person->phone);

        return response()->json([ 'status' => 'Contact successfully updated!', 'person' => $person], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Person $person)
    {
        $flash_message = $person->first_name . ' ' . $person->last_name . ' has been successfully deleted from the phonebook.';
        if (!is_null($person->avatar)) {
            Storage::disk('public')->delete('/avatars/' . $person->avatar);
        } 
        $person->delete();
        return response()->json(['message' => $flash_message], 200);
    }
}
