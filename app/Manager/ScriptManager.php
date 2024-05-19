<?php
namespace App\Manager;

use Illuminate\Support\Facades\Http;
use App\Models\Division;
use App\Models\District;
use App\Models\SubDistrict;
use App\Models\Area;

class ScriptManager {

    // url provinsi = https://shopee.co.id/api/v4/location/get_child_division_list?division_id=0&use_case=shopee.account

    // url kota = https://shopee.co.id/api/v4/location/get_child_division_list?division_id=101644386189719&use_case=shopee.account

    // url kecamatan = https://shopee.co.id/api/v4/location/get_child_division_list?division_id=101424260662784&use_case=shopee.account

    // kode pos = https://shopee.co.id/api/v4/location/get_zipcode_list_by_division?division_id=101297891946441&use_case=shopee.account

    public function getLocationData()
    {
        ini_set('max_execution_time', 600);

        $url = 'https://shopee.co.id/api/v4/location/get_child_division_list?division_id=0&use_case=shopee.account';

        $response = Http::get($url);
        $divisions = json_decode($response->body(), true);

        foreach ($divisions['data']['divisions'] as $key => $division) {
            if($key == 26) {
                $division_data['name'] = $division['division_name'];
                $division_data['original_id'] = $division['id'];
                $created_div = Division::create($division_data);

                $dist_url = 'https://shopee.co.id/api/v4/location/get_child_division_list?division_id='.$division['id'].'&use_case=shopee.account';

                $dist_response = Http::get($dist_url);
                $districts = json_decode($dist_response->body(), true);

                foreach ($districts['data']['divisions'] as $district) {
                    $district_data['division_id'] = $created_div->id;
                    $district_data['name'] = $district['division_name'];
                    $district_data['original_id'] = $district['id'];
                    $created_dist = District::create($district_data);

                    $sub_dist_url = 'https://shopee.co.id/api/v4/location/get_child_division_list?division_id='.$district['id'].'&use_case=shopee.account';

                    $sub_dist_response = Http::get($sub_dist_url);
                    $districts = json_decode($sub_dist_response->body(), true);

                    foreach ($districts['data']['divisions'] as $sub_district) {
                        $sub_district_data['district_id'] = $created_dist->id;
                        $sub_district_data['name'] = $sub_district['division_name'];
                        $sub_district_data['original_id'] = $sub_district['id'];
                        $created_sub_dist = SubDistrict::create($sub_district_data);

                        $area_url = 'https://shopee.co.id/api/v4/location/get_zipcode_list_by_division?division_id='.$sub_district['id'].'&use_case=shopee.account';

                        $area_response = Http::get($area_url);
                        $areas = json_decode($area_response->body(), true);

                        foreach ($areas['data']['zipcode_info_list'] as $area) {
                            $area_data['name'] = $area['zipcode'];
                            $area_data['original_id'] = $area['id'];
                            $area_data['sub_district_id'] = $created_sub_dist->id;
                            Area::create($area_data);
                        }
                    }
                }
            }
            echo 'success';
        }
    }
}