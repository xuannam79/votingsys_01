<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $lat = $request->lat;
        $lng = $request->lng;
        $locationJson = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?' .
            'latlng=' . $lat .',' . $lng . '&sensor=true');
        $locationArray = json_decode($locationJson, true);

        // get location success
        if ($locationArray['status'] == 'OK') {
            try {
                $district = $locationArray['results'][0]['address_components'][4]['long_name'];
                $city = $locationArray['results'][0]['address_components'][5]['long_name'];
                $country = $locationArray['results'][0]['address_components'][6]['long_name'];
                $data = [
                    'success' => true,
                    'location' => $district . ', ' . $city . ', ' . $country,
                ];
            } catch (Exception $ex) {
                $data = [
                    'success' => true,
                    'location' => '',
                ];
            }
        } else {
            $data = [
                'success' => false,
            ];
        }

        return response()->json($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }
}
