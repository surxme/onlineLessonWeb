<?php

use think\migration\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data  =
            [
                'name'        =>  'admin' ,
                'is_super'        =>  '1',
                'password'        =>  '1692daf4005e7336985cb31bbc6ba822',//123456
                'create_time'     =>  time(),
                'update_time'     =>  time(),
            ];
        $this->table('admin')->insert($data)->save();
    }
}