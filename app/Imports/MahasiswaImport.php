<?php

namespace App\Imports;

use App\Models\Anggota;
use App\Models\DataMahasiswa;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;

use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MahasiswaImport implements ToModel
{    
    use importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {                            
        return new DataMahasiswa([           
            'nilai_mahasiswa' => $row[0],                                               
        ]);
    }

    // public function customValidationMessages()
    // {
    //     return [
    //         '1.in' => 'Custom message for :attribute.',
    //     ];
    // }

    
   
}