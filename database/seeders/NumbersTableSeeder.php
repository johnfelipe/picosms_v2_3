<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

class NumbersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $data=[
            'id'=>1,
            'admin_id'=>'1',
            'number'=>'01737030479',
            'from'=>'signalwire',
            'obj'=>json_encode([]),
            'purch_price'=>0.008,
            'sell_price'=>0.008,
        ];
        \App\Models\Number::create($data);
    }
}
