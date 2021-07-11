<?php

namespace App\Exports;

use App\Models\Anggota;
use App\Models\DataMahasiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;


class MahasiswaExport implements FromCollection, WithMapping
{        
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return DataMahasiswa::all();        
    }
    

    public function map($anggota): array{
        return [
            $anggota->nilai_mahasiswa,         
        ];
    }

}