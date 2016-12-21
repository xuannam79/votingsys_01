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
        $country = '';
        $city = '';
        $district = '';

        // get location success
        if ($locationArray['status'] == 'OK') {
            try {
                foreach ($locationArray['results'][0]['address_components'] as $components) {
                    if (in_array('country', $components['types'])) {
                        $country = $components['long_name'];
                    }

                    if (in_array('administrative_area_level_1', $components['types'])) {
                        $city = $components['long_name'];
                    }

                    if (in_array('administrative_area_level_2', $components['types'])) {
                        $district = $components['long_name'];
                    }
                }

                if (! ($country && $city && $district)) {
                    $data = [
                        'success' => true,
                        'location' => '',
                    ];
                } else {
                    $data = [
                        'success' => true,
                        'location' => $district . ', ' . $city . ', ' . $country,
                    ];
                }
            } catch (Exception $ex) {
                dd($ex);
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
