<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DataMahasiswa extends Model
{
    // public function dataMahasiswa()
    // {
    //     return [
    //         [
    //             'No' => '1',
    //             'Nama' => 'Satria',
    //             'Nilai' => 'Nilai'
    //         ]
    //     ];
    // }
    protected $table = 'Tabel_Mahasiswa';
    protected $primaryKey = 'id_mahasiswa';

    public function dataMahasiswa(){
        return DB::table('Tabel_Mahasiswa')->get();
    }

    public function addData($data){
        DB::table('Tabel_Mahasiswa')->insert($data);
    }

    public function editData($id_mahasiswa, $data){
        DB::table('Tabel_Mahasiswa')
        ->where('id_mahasiswa',$id_mahasiswa)
        ->update($data);
    }
}
