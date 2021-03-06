<?php

namespace App\Http\Controllers;

use Datatables;
use Validator;
use App\Facility;
use App\CaseManager;
use App\Coordinate;
use Illuminate\Http\Request;
use App\Imports\FacilitiesImport;
use App\Imports\CoordinatesImport;
use Maatwebsite\Excel\Facades\Excel;

class FacilityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Facilities';
        $facilities = Facility::all();
        return view('facility.index', compact('title', 'facilities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //check if a bulk option (csv file upload) was choosen or single option
        //verify and upload in both cases
        list($validator, $msg) = $request->hasFile('bulk-facility') ? $this->verifyUploadCSV($request) : $this->verifyUploadSingle($request);

        if ($validator->fails()){
            session()->flash('facility_reg_valid_fail', [
                'failed'    => true,
                'msg'       => $msg
            ]);
            return redirect()->back()->withInput($request->input())->withErrors($validator);
        }

        return redirect(route('facilities'))->with('success', $msg);
    }

    //verify and upload csv file for multiple facilities
    private function verifyUploadCSV(Request $request)
    {
        $rules = ['bulk-facility' => ['required','mimes:csv,txt,xlsx'] ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            //return validator with fail
            $msg = 'csv failed';
            return array($validator, $msg);
        }

        $file = $request->file('bulk-facility');
        Excel::import(new FacilitiesImport, $file);
        // my custom global helper.php function
        // $facility_array = csvToArray($file);
        
        // for($i = 0; $i < count($facility_array); $i++){
        //     Facility::firstOrCreate($facility_array[$i]);
        // }

        //return $validator still, but without fail
        $msg = 'All facilities file successfully registered';
        return array($validator, $msg);
    }

    public function uploadCoords(Request $request)
    {
        $file = $request->file('bulk-coords');
        Excel::import(new CoordinatesImport, $file);
        return redirect(route('facilities'))->with('success', 'All Coordinates uploaded');
    }

    // upload single facility
    private function verifyUploadSingle(Request $request)
    {
        $rules = [
            'name'      => ['required', 'string', 'max:190'],
            'backstop'  => ['required', 'string'],
        ];
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()){
            //return failed validator
            $msg = 'single failed';
            return array($validator, $msg);
        }
        Facility::create($request->all());

        //return validator without fail
        $msg = 'Facility successfully registered';
        return array($validator, $msg);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $facility = Facility::find($id);
        $title = 'Edit Facilty: '.$facility->name;
        return view('facility.edit-facility', compact('facility', 'title'));
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
        $rules = [
            'name'      => ['required', 'string', 'max:190'],
            'backstop'  => ['required', 'string'],
        ];
        $this->validate($request, $rules);
        $facility = Facility::find($id);
        $facility->update($request->all());
        session()->flash('success','Facility Updated');
        return redirect()->route('facilities');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $facility = Facility::find($request->id);
        $facility->delete();
        return true;
    }

    public function viewCaseManagers($id)
    {
        $facility = Facility::find($id);
        $title = 'Case Managers in '.$facility->name.' facility';
        return view('facility.case_managers', compact('title','facility'));
    }

    public function viewClients($id)
    {
        $facility = Facility::find($id);
        $title = 'Clients in '.$facility->name.' facility';
        $managers = CaseManager::where('facility_id',$facility->id)->get();
        return view('facility.clients', compact('title','facility','managers'));
    }

    public function saveCoords(Request $request)
    {
        // dd($request->all());
        $rules = [
            'facility'  => ['required', 'string'],
            'longitude' => 'required',
            'latitude'  => 'required'
        ];

        $msg = [
            'longitude.required' => 'Please allow the system access your location',
            'latitude.required' => 'Please allow the system access your location'
        ];

        $validator = validator()->make($request->all(), $rules, $msg);

        if ($validator->fails()){
            session()->flash('error', 'Incomplete action');
            return redirect()->back()->withInput($request->input())->withErrors($validator);
        }

        $coord = new Coordinate;
        $coord->facility = $request->facility;
        $coord->longitude = $request->longitude;
        $coord->latitude = $request->latitude;
        $coord->save();

        session()->flash('success', 'Success. Thank You!');

        return redirect()->back();
    }
}
