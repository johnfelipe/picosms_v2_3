<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data=[
            'id'=>'1',
            'admin_id'=>'1',
            'title'=>'Trial',
            'sms_limit'=>100,
            'price'=>0,
            'status'=>'active',
            'created_at'=>now(),
            'updated_at'=>now(),
        ];
        \App\Models\Plan::create($data);
    }
}
