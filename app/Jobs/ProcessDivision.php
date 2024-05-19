<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Division;
use App\Models\District;
use App\Models\SubDistrict;
use App\Models\Area;
use Illuminate\Support\Facades\Http;

class ProcessDivision implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $division;

    /**
     * Create a new job instance.
     */
    public function __construct($division)
    {
        $this->division = $division;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $division_data['name'] = $this->division['division_name'];
        $division_data['original_id'] = $this->division['id'];
        $created_div = Division::create($division_data);

        $dist_url = 'https://shopee.co.id/api/v4/location/get_child_division_list?division_id='.$this->division['id'].'&use_case=shopee.account';

        $dist_response = Http::get($dist_url);
        $districts = json_decode($dist_response->body(), true);

        foreach ($districts['data']['divisions'] as $district) {
            $district_data['division_id'] = $created_div->id;
            $district_data['name'] = $district['division_name'];
            $district_data['original_id'] = $district['id'];
            $created_dist = District::create($district_data);

            $sub_dist_url = 'https://shopee.co.id/api/v4/location/get_child_division_list?division_id='.$district['id'].'&use_case=shopee.account';

            $sub_dist_response = Http::get($sub_dist_url);
            $sub_districts = json_decode($sub_dist_response->body(), true);

            foreach ($sub_districts['data']['divisions'] as $sub_district) {
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
}
