<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataMahasiswa;
use Illuminate\Support\Facades\DB;
use App\Exports\MahasiswaExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Models\ZTabel;
use App\Imports\MahasiswaImport;
use App\Models\UjiT;
use App\Models\TTabel;
use App\Exports\UjiTExport;
use App\Imports\UjiTImport;
use App\Models\UjiAnava;
use App\Exports\UjiAnavaExport;
use App\Imports\UjiAnavaImport;
use App\Models\FTabel;


// public function mahasiswaexport(){
//     return Excel::download(new MahasiswaExport,'mahasiswa.xlsx');
// }

class StatistikController extends Controller
{
    public function __construct()
    {
        $this->DataMahasiswa = new DataMahasiswa();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = DataMahasiswa::all();             
       $maxSkor = DataMahasiswa::max('nilai_mahasiswa');
       $minSkor = DataMahasiswa::min('nilai_mahasiswa');
       $rata2 = number_format(DataMahasiswa::average('nilai_mahasiswa'),3);
       
       
       //untuk tabel frekuensi
       $frekuensi = DataMahasiswa::select('nilai_mahasiswa', DB::raw('count(*) as frekuensi'))  //ambil skor, hitung banyak skor taruh di tabel frekuensi
                                ->groupBy('nilai_mahasiswa')                              //urutkan sesuai skor
                                ->get();
       $totalskor = DataMahasiswa::sum('nilai_mahasiswa');              
       $totalfrekuensi = DataMahasiswa::count('nilai_mahasiswa');        //karena total frekuensi = banyaknya skor yang ada

       return view('/statistik/index', ['mahasiswa' => $data,
                            'max' => $maxSkor, 
                            'min' => $minSkor, 
                            'rata2' => $rata2,
                            'frekuensi' => $frekuensi,
                            'totalskor' => $totalskor,
                            'totalfrekuensi' => $totalfrekuensi]);    //tampilkan home.blade
        $data = [
            'mahasiswa' => $this->DataMahasiswa->dataMahasiswa(),
        ];
        // return view('statistik/index', $data);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $data = [
            'id_mahasiswa' => Request()->id,
            'nama_mahasiswa' => Request()->nama,
            'nilai_mahasiswa' => Request()->nilai,
        ];

        $this->DataMahasiswa->addData($data);
        return redirect()->route('mahasiswa')->with('pesan', 'Berhasil ditambahkan');
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
    public function edit($id_mahasiswa)
    {
        $data = DataMahasiswa::find($id_mahasiswa);
        
        if(!$data){
            abort(404);
        }
 
        return view('statistik/edit', ['mahasiswa' => $data]);
    }    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id_mahasiswa)
    {
        $data = [
            'nama_mahasiswa' => Request()->nama,
            'nilai_mahasiswa' => Request()->nilai,
        ];

        $this->DataMahasiswa->editData($id_mahasiswa, $data);
        return redirect()->route('mahasiswa')->with('pesan', 'Berhasil diedit');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id_mahasiswa)
    {
        return $data;
    }

    public function delete($id_mahasiswa)
   {
       $data = DataMahasiswa::find($id_mahasiswa);
       $data->delete();

       return redirect('/statistik')->with('pesan', 'Berhasil dihapus');
   }

 public function export(){

        return Excel::download(new MahasiswaExport, time().'_'.'mahasiswa.xlsx');               
   }

   public function import(Request $request){

        $this->validate($request, 
        [            
            'file'      =>  'required|file|mimes:xlsx,csv'
        ],
        [
            'file'      =>  'File Harus Berekstensi .xlsx atau .csv',            
        ]);   

        $file = $request->file('file');       
        $namaFile = $file->getClientOriginalName();
        $file->move('nilai_mahasiswa', $namaFile);
        
        $filexcel = Excel::import(new MahasiswaImport, public_path('/nilai_mahasiswa/'.$namaFile));                  
       
        // try{ 
        // } catch (\Exception $ex){
        //     return back()->withErrors('HELO PEK');
        // }
        
        return redirect('/')->with('status', 'Data Berhasil Diimport!');
   }

   public function databergolong(){

        $maxSkor = DataMahasiswa::max('nilai_mahasiswa');
        $minSkor = DataMahasiswa::min('nilai_mahasiswa');
        $n = DataMahasiswa::count('nilai_mahasiswa');
        //mencari rentangan
        $rentangan = $maxSkor - $minSkor;

        //mencari kelas        
        $kelas = ceil(1 + 3.3 * log10 ($n));

        //menghitung interval
        $interval = ceil($rentangan/$kelas);        
        
        //set batas bawah dan batas atas
        $batasBawah = $minSkor;
        $batasAtas = 0;
        
        //data bergolong
        for($i = 0; $i < $kelas; $i++){
            $batasAtas = $batasBawah + $interval - 1;
            // $frekuensi[$i] = Anggota::where(function, $query){
            //     $query->select(DB::raw('SUM(frekuensi) as tabel1'))
            //             ->
            // }          
            $frekuensi[$i] = DataMahasiswa::select(DB::raw('count(*) as frekuensi, nilai_mahasiswa'))
                                    ->where([
                                        ['nilai_mahasiswa', '>=', $batasBawah],
                                        ['nilai_mahasiswa', '<=', $batasAtas],
                                    ])
                                    ->groupBy()                                                                                                    
                                    ->count();            
            $data[$i] = $batasBawah. " - ". $batasAtas;                                                          
            $batasBawah = $batasAtas + 1;
        }
                

        return view ('statistik/databergolong', ['data' => $data,
                                        'frekuensi' => $frekuensi,
                                        'batasAtas' => $batasAtas,
                                        'batasBawah' => $batasBawah,
                                        'kelas' => $kelas,
                                        'interval' => $interval,
                                        'rentangan' => $rentangan,                                        
                                        ]);
   }
    

   public function chikuadrat(){

        $maxSkor = DataMahasiswa::max('nilai_mahasiswa');
        $minSkor = DataMahasiswa::min('nilai_mahasiswa');
        //$n = f0 = banyak skor/total frekuensi
        $n = DataMahasiswa::count('nilai_mahasiswa');
        $rata2 = number_format(DataMahasiswa::average('nilai_mahasiswa'), 2);

        //function standar deviasi
        function std_deviation($my_arr){
            $no_element = count($my_arr);
            $var = 0.0;
            $avg = array_sum($my_arr)/$no_element;
            foreach($my_arr as $i)
                {
                    $var += pow(($i - $avg), 2);
                }
            return (float)sqrt($var/$no_element);
        }

        //function desimal
        function desimal($nilai){
            if($nilai<0){
                $des = substr($nilai,0,4);
            } else {
                $des = substr($nilai,0,3);
            }
            return $des;
        }

        //function label
        function label($nilai){
            if($nilai<0){
                $str1 = substr($nilai,4,1);
            } else {
                $str1 = substr($nilai,3,1);
            }

            switch($str1){
                case '0': 
                    $sLabel = 'nol';
                    break;
                case '1': 
                    $sLabel = 'satu';
                    break;
                case '2': 
                    $sLabel = 'dua';
                    break;
                case '3': 
                    $sLabel = 'tiga';
                    break;
                case '4': 
                    $sLabel = 'empat';
                    break;
                case '5': 
                    $sLabel = 'lima';
                    break;
                case '6': 
                    $sLabel = 'enam';
                    break;
                case '7': 
                    $sLabel = 'tujuh';
                    break;
                case '8': 
                    $sLabel = 'delapan';
                    break;
                case '9': 
                    $sLabel = 'sembilan';
                    break;
                default: $sLabel = 'Tidak ada field';
            }
            
            return $sLabel;
        }

        //ambil nilai skor
        $anggota = DataMahasiswa::select('nilai_mahasiswa')->get();

        //masukin skor ke dalam array biar bsa dipakek sama functionnya
        $i = 0;
        foreach ($anggota as $a){
            $arraySkor[$i] = $a->nilai_mahasiswa;
            $i++;            
        }                         
           
        //standar deviasi dari seluruh skor
        $SD = number_format(std_deviation($arraySkor), 2);                            
        
        //mencari rentangan
        $rentangan = $maxSkor - $minSkor;

        //mencari kelas        
        $kelas = ceil(1 + 3.3 * log10 ($n));

        //menghitung interval
        $interval = ceil($rentangan/$kelas);        
        
        //set batas bawah dan batas atas
        $batasBawah = $minSkor;
        $batasAtas = 0;
        
        //data chi
        $totalchi = 0;
        for($i = 0; $i < $kelas; $i++){
            //menghitung batas bawah
            $batasBawahBaru[$i] = $batasBawah - 0.5;            
            
            $batasAtas = $batasBawah + $interval - 1;

            //menghitung batas atas
            $batasAtasBaru[$i] = $batasAtas + 0.5;
                                    
            //menghitung atas dan bawah z
            $zBawah[$i] = number_format(($batasBawahBaru[$i]- $rata2)/$SD, 2);
            $zAtas[$i] = number_format(($batasAtasBaru[$i]- $rata2)/$SD, 2);                       

            //menghitung z tabel atas dan bawah
            $cariDesimalBawah = desimal($zBawah[$i]);
            $cariDesimalAtas = desimal($zAtas[$i]);

            $labelDesimalBawah = label($zBawah[$i]);            
            $labelDesimalAtas= label($zAtas[$i]);                                                           

            $zTabelBawah = ZTabel::where('z', '=', $cariDesimalBawah)->get(); 
            $zTabelAtas = ZTabel::where('z', '=', $cariDesimalAtas)->get();                     
            $zTabelBawahFix[$i] = $zTabelBawah[0]->$labelDesimalBawah;
            $zTabelAtasFix[$i] = $zTabelAtas[0]->$labelDesimalAtas;            
            
            //menghitung l/proporsi
            $lprop[$i] = abs($zTabelBawahFix[$i] - $zTabelAtasFix[$i]);

            //menghitung fe(L*N)
            $fe[$i] = $lprop[$i]*$n;              

            //menghitung f0
            $frekuensi[$i] = DataMahasiswa::select(DB::raw('count(*) as frekuensi, nilai_mahasiswa'))
                                    ->where([
                                        ['nilai_mahasiswa', '>=', $batasBawah],
                                        ['nilai_mahasiswa', '<=', $batasAtas],
                                    ])
                                    ->groupBy()                                                                                                    
                                    ->count();            
            $data[$i] = $batasBawah. " - ". $batasAtas;                                                          
            $batasBawah = $batasAtas + 1;
            
            //menghitung (f0-fe)^2/fe
            $kai[$i] = number_format(pow(($frekuensi[$i] - $fe[$i]),2)/$fe[$i], 7);
            $totalchi += $kai[$i];                        
        }
       
                

        return view ('statistik/chi-normalisasi', ['data' => $data,
                                        'frekuensi' => $frekuensi,
                                        'batasAtas' => $batasAtas,
                                        'batasBawah' => $batasBawah,
                                        'kelas' => $kelas,
                                        'interval' => $interval,
                                        'rentangan' => $rentangan,
                                        'batasBawahBaru' => $batasBawahBaru,    
                                        'batasAtasBaru' => $batasAtasBaru,
                                        'zBawah' => $zBawah, 
                                        'zAtas' => $zAtas, 
                                        'zTabelBawahFix' => $zTabelBawahFix,
                                        'zTabelAtasFix' => $zTabelAtasFix,
                                        'lprop' => $lprop,
                                        'fe' => $fe,
                                        'kai' => $kai,
                                        'totalchi' => $totalchi,                                                                        
                                        ]);
   }

   public function lilliefors(){               
       
        //ngambil banyak skor
        $n = DataMahasiswa::count('nilai_mahasiswa');
        $rata2 = number_format(DataMahasiswa::average('nilai_mahasiswa'), 2);

        //function standar deviasi
        function std_deviation($my_arr){
            $no_element = count($my_arr);
            $var = 0.0;
            $avg = array_sum($my_arr)/$no_element;
            foreach($my_arr as $i)
                {
                    $var += pow(($i - $avg), 2);
                }
            return (float)sqrt($var/$no_element);
        }

        //function desimal
        function desimal($nilai){
            if($nilai<0){
                $des = substr($nilai,0,4);
            } else {
                $des = substr($nilai,0,3);
            }
            return $des;
        }

        //function label
        function label($nilai){
            if($nilai<0){
                $str1 = substr($nilai,4,1);
            } else {
                $str1 = substr($nilai,3,1);
            }

            switch($str1){
                case '0': 
                    $sLabel = 'nol';
                    break;
                case '1': 
                    $sLabel = 'satu';
                    break;
                case '2': 
                    $sLabel = 'dua';
                    break;
                case '3': 
                    $sLabel = 'tiga';
                    break;
                case '4': 
                    $sLabel = 'empat';
                    break;
                case '5': 
                    $sLabel = 'lima';
                    break;
                case '6': 
                    $sLabel = 'enam';
                    break;
                case '7': 
                    $sLabel = 'tujuh';
                    break;
                case '8': 
                    $sLabel = 'delapan';
                    break;
                case '9': 
                    $sLabel = 'sembilan';
                    break;
                default: $sLabel = 'Tidak ada field';
            }
            
            return $sLabel;
        }

        //ambil nilai skor
        $anggota = DataMahasiswa::select('nilai_mahasiswa')->get();

        //masukin skor ke dalam array biar bsa dipakek sama functionnya
        $i = 0;
        foreach ($anggota as $a){
            $arraySkor[$i] = $a->nilai_mahasiswa;
            $i++;            
        }                         
           
        //standar deviasi dari seluruh skor
        $SD = number_format(std_deviation($arraySkor), 2);    

        //ngambil data dan frekuensinya
        for($i = 0; $i < $n; $i++){
            $frekuensi[$i] = DataMahasiswa::select('nilai_mahasiswa', DB::raw('count(*) as frekuensi'))  //ambil skor, hitung banyak skor taruh di tabel frekuensi
                                ->groupBy('nilai_mahasiswa')    //urutkan sesuai skor
                                ->get();     
            //ngambil banyak data setelah diambil frekuensinya     
            $banyakData = count($frekuensi[$i]);            
        } 

        //mencari f(zi) dari tabel z
        $fkum = 0;
        $totalLillie = 0;
        for ($i = 0; $i < $banyakData; $i++){
            
            //frekuensi komulatif
            $fkum += $frekuensi[0][$i]->frekuensi;
            $fkum2[$i] = $fkum;         

            //mencari nilai Zi
            $Zi[$i] = number_format(($frekuensi[0][$i]->nilai_mahasiswa - $rata2)/$SD, 2);
            
            //mencari F(zi)dari tabel z
            $cariDesimalZi = desimal($Zi[$i]);
            $labelZi = label($Zi[$i]);
            $zTabel = ZTabel::where('z', '=', $cariDesimalZi)->get();
            $fZi[$i] = $zTabel[0]->$labelZi; 
            
            //mencari S(Zi)
            $sZi[$i] = $fkum2[$i]/$n;
            
            //mencari |F(Zi)-S(Zi)|
            $lilliefors[$i] = abs($fZi[$i]-$sZi[$i]);
            
            //total
            $totalLillie += $lilliefors[$i];
        }
                             

        return view('statistik/lilliefors', ['frekuensi' => $frekuensi, 
                                    'banyakData' => $banyakData,                                 
                                    'fkum2' => $fkum2,
                                    'Zi' => $Zi,
                                    'fZi' => $fZi,
                                    'sZi' => $sZi,
                                    'lilliefors' => $lilliefors,
                                    'totalLillie' => $totalLillie,
                                    'n' => $n,
                                 ]);
   }

   public function storeX1X2(Request $request){       

        $this->validate($request, 
        [            
            'x1'      =>  'required|numeric|min:1|max:100',
            'x2'      =>  'required|numeric|min:1|max:100'
        ],
        [
            'x1.min'  =>  'Kolom Skor Hanya Bisa Diisi Angka 1-100',
            'x1.max'  =>  'Kolom Skor Hanya Bisa Diisi Angka 1-100',
            'x1.numeric' => 'Kolom Hanya Bisa Berisi Angka!',
            'x2.min'  =>  'Kolom Skor Hanya Bisa Diisi Angka 1-100',
            'x2.max'  =>  'Kolom Skor Hanya Bisa Diisi Angka 1-100',
            'x2.numeric' => 'Kolom Hanya Bisa Berisi Angka!'
        ]);

        $ujiT = new UjiT;
        $ujiT->x1 = $request->x1;
        $ujiT->x2 = $request->x2;
        $ujiT->save();

        return redirect('statistik/ujiTBerkolerasi')->with('status', 'Data Berhasil Tambah');

   }

   public function deleteT($id)
   {
       $ujiT = UjiT::find($id);         //cari id yang dipencet       
       $ujiT->delete();                  //delete id tersebut

       return redirect('statistik/ujiTBerkolerasi')->with('status', 'Data Berhasil Dihapus');                //redirect lagi ke home
   }

   public function ujiTBerkolerasi(){

        $ujiT = UjiT::all();  
        $rata2x1 = UjiT::average('x1');
        $rata2x2 = UjiT::average('x2');
        $n1 = UjiT::count('x1');
        $n2 = UjiT::count('x2');

        $jumlahData = UjiT::count();

        //function standar deviasi
        function std_deviation($my_arr){
            $no_element = count($my_arr);
            $var = 0.0;
            $avg = array_sum($my_arr)/$no_element;
            foreach($my_arr as $i)
                {
                    $var += pow(($i - $avg), 2);
                }
            return (float)sqrt($var/$no_element);
        }

        //ambil nilai x1 dan x2
        $x1 = UjiT::select('x1')->get();        
        $x2 = UjiT::select('x2')->get();

        //masukin x1 dan x2 ke dalam array biar bsa dipakek sama functionnya
        $i = 0;
        foreach ($x1 as $a){
            $arrayX1[$i] = $a->x1;
            $i++;            
        }        
        $j = 0;
        foreach ($x2 as $b){
            $arrayX2[$j] = $b->x2;
            $j++;            
        }
           
        //standar deviasi dari seluruh x1 dan x2
        $sdX1 = number_format(std_deviation($arrayX1), 2); 
        $sdX2 = number_format(std_deviation($arrayX2), 2); 

        //varians x1 dan x2
        $variansX1 = pow($sdX1, 2);
        $variansX2 = pow($sdX2, 2);
        
        //start mencari korelasi x1 dan x2
        $sumX1Kuadrat = 0;
        $sumX2Kuadrat = 0;
        $sumX1X2 = 0;
        for ($i=0; $i < $jumlahData; $i++) {

            $x1korelasi[$i] = $ujiT[$i]->x1 - $rata2x1;
            $x2korelasi[$i] = $ujiT[$i]->x2 - $rata2x2;

            $x1Kuadrat[$i] = $x1korelasi[$i] * $x1korelasi[$i];             
            $sumX1Kuadrat += $x1Kuadrat[$i];           

            $x2Kuadrat[$i] = $x2korelasi[$i] * $x2korelasi[$i];   
            $sumX2Kuadrat += $x2Kuadrat[$i];

            $x1Kalix2[$i] = $x1korelasi[$i] * $x2korelasi[$i];                       
            $sumX1X2 += $x1Kalix2[$i];
        }       

        //rumus korelasi   
        //dd(number_format($sumX1Kuadrat*$sumX2Kuadrat, 6)); 
       $korelasimoment = number_format($sumX1X2/sqrt($sumX1Kuadrat*$sumX2Kuadrat), 2);                

        //nilaiUjiT
       $nilaiUjiT = number_format($rata2x1 - $rata2x2 / sqrt( ( ($variansX1/$n1)+($variansX2/$n2)) - 2*$korelasimoment*( ($sdX1/sqrt($n1)) * ($sdX2/sqrt($n2)) ) ), 2 );       
       
       //mengecek tabel T, butuh $derajat bebas dan label nilai = 0.05
       $derajatBebas = $jumlahData - 1;
       $labelnilai = "limapersen";       

       //1. cek di tabel T
        $kolom = Ttabel::where('df', '=', $derajatBebas)->get();                       
        $TTabel = $kolom[0]->$labelnilai;  
                
        //cek keterangan
        if ($nilaiUjiT < $TTabel){
            $status =  "Diterima";
        } else {
            $status =   "Tidak Diterima";
        }
                            
        return view('statistik/ujiTBerkolerasi', ['ujiT' => $ujiT,
                                        'rata2x1' => $rata2x1,
                                        'rata2x2' => $rata2x2,
                                        'sdX1' => $sdX1,
                                        'sdX2' => $sdX2,
                                        'variansX1' => $variansX1,
                                        'variansX2' => $variansX2,
                                        'nilaiUjiT' => $nilaiUjiT,
                                        'TTabel' => $TTabel,
                                        'status' => $status,
                                    ]);
    }

    public function ujiTBerkolerasiExport(){

        return Excel::download(new UjiTExport, time().'_'.'DataUjiT.xlsx'); 
    }

    public function ujiTBerkolerasiImport(Request $request){

        $this->validate($request, 
        [            
            'file'      =>  'required|file|mimes:xlsx,csv'
        ],
        [
            'file'      =>  'File Harus Berekstensi .xlsx atau .csv',            
        ]);          

        $file = $request->file('file');       
        $namaFile = $file->getClientOriginalName();
        $file->move('UjiT', $namaFile);
        
        $filexcel = Excel::import(new UjiTImport, public_path('/UjiT/'.$namaFile));                         
        
        return redirect('statistik/ujiTBerkolerasi')->with('status', 'Data Uji T Berhasil Diimport!');

    }
    public function ujiAnava(){

        $ujiAnava = UjiAnava::all();
        $jumlahData = UjiAnava::count();

         // sum dan avg data normal
        $sumX1 = UjiAnava::sum('x1');
        $sumX2 = UjiAnava::sum('x2');
        $sumX3 = UjiAnava::sum('x3');
        $sumX4 = UjiAnava::sum('x4');
        $avgX1 = UjiAnava::avg('x1');
        $avgX2 = UjiAnava::avg('x2');
        $avgX3 = UjiAnava::avg('x3');
        $avgX4 = UjiAnava::avg('x4');

        //mencari count x per kelompok data 
        $nx1 = UjiAnava::count('x1');
        $nx2 = UjiAnava::count('x2');
        $nx3 = UjiAnava::count('x3');
        $nx4 = UjiAnava::count('x4');

        //jumlah semua data 
        $N = $nx1+ $nx2+ $nx3;

        //jumlah kelompok data 
        $k = 4;

        //selesaikan tabel datanya 
        $sigmaX1kuadrat = 0;
        $sigmaX2kuadrat = 0;
        $sigmaX3kuadrat = 0;
        $sigmaX4kuadrat = 0;
        $sigmaXtotal = 0;
        $sigmaXtotalkuadrat = 0;

        for ($i=0; $i < $jumlahData; $i++){
            $X1kuadrat[$i] = $ujiAnava[$i]->x1 * $ujiAnava[$i]->x1;
            $X2kuadrat[$i] = $ujiAnava[$i]->x2 * $ujiAnava[$i]->x2;
            $X3kuadrat[$i] = $ujiAnava[$i]->x3 * $ujiAnava[$i]->x3;
            $X4kuadrat[$i] = $ujiAnava[$i]->x4 * $ujiAnava[$i]->x4;

            $sigmaX1kuadrat += $X1kuadrat[$i];
            $sigmaX2kuadrat += $X2kuadrat[$i];
            $sigmaX3kuadrat += $X3kuadrat[$i];
            $sigmaX4kuadrat += $X4kuadrat[$i];

            // mencari Xtotal
            $Xtotal[$i] = $ujiAnava[$i]->x1 + $ujiAnava[$i]->x2 + $ujiAnava[$i]->x3 + $ujiAnava[$i]->x4;
            $XtotalKuadrat[$i] =  $Xtotal[$i] * $Xtotal[$i];

            $sigmaXtotal += $Xtotal[$i];
            $sigmaXtotalkuadrat += $XtotalKuadrat[$i];
        }

        //mencari JKa (Jumlah Kuadrat Antara) rumus sigma xperkelompok * n x per kelompok
        if($nx1 !== 0 ){
            $a1 =  ($sumX1/$nx1);
        }else {
            $a1 = 0;
        }

        if($nx2 !== 0 ){
            $a2 =  ($sumX2/$nx2);
        }else {
            $a2 = 0;
        }

        
        if($nx3 !== 0 ){
            $a3 =  ($sumX3/$nx3);
        }else {
            $a3 = 0;
        }

        if($nx4 !== 0 ){
            $a4 =  ($sumX4/$nx4);
        }else {
            $a4 = 0;
        }

        if($N !== 0 ){
            $a5 =  ($sigmaXtotal/$N);
        }else {
            $a5 = 0;
        }

        $JKA =  $a1 + $a2 + $a3 + $a4 - $a5;

         // mencari DKA 
        $DKA = $k - 1;

        // mencari RJKA (Rerata Jumlah Kuadrat Antara)
        if($DKA !== 0 ){
            $RJKA = $JKA/$DKA;
        } else {
            $RJKA = 0;
        }

        // mencari JKt
        $sigmaYkuadrat = $sigmaX1kuadrat + $sigmaX2kuadrat + $sigmaX3kuadrat + $sigmaX4kuadrat;

        if ($N !== 0) { 
            $JKT = $sigmaYkuadrat - (($sigmaXtotal * $sigmaXtotal)/$N);
        } else {
            $JKT =0;
        }

        //mencari  Jumlah Kuadrat Dalam (JKD)
        $JKD = $JKT - $JKA;

        //mencari DKD
        $DKD = $N - $k;

        // mencari RJKD Rerata Jumlah Kuadrat Dalam
        if($DKD !== 0) { 
            $RJKD = $JKD/$DKD;  
        } else {
            $RJKD = 0;
        }
        
        // uji F
        if($RJKD !== 0 ){ 
            $F = $RJKA/ $RJKD;
        }else{
            $F = 0;
        }

        $DKT = $DKD + $DKA;

        //mengecek tabel f, butuh $DKA dan $DKD
        //function cek label
        function label($nilai){            

            switch($nilai){
                case '0': 
                    $sLabel = 'nol';
                    break;
                case '1': 
                    $sLabel = 'satu';
                    break;
                case '2': 
                    $sLabel = 'dua';
                    break;
                case '3': 
                    $sLabel = 'tiga';
                    break;
                case '4': 
                    $sLabel = 'empat';
                    break;
                case '5': 
                    $sLabel = 'lima';
                    break;                
                default: $sLabel = 'Tidak ada field';
            }
            
            return $sLabel;
        }

        //1. cek label
        $labelDKA = label($DKA);
        
        //2. cek di tabel f
        $kolom = Ftabel::where('df1', '=', $DKD)->get();                 
        $fTabel = $kolom[0]->$labelDKA;               

        //cek keterangan
        if ($F > $fTabel){
            $status =  "Signifikan";
        } else {
            $status =   "Tidak Signifikan";
        }

        return view('statistik/ujiAnava', ['ujiAnava' => $ujiAnava,
                                'jumlahData' => $jumlahData,
                                'x1kuadrat' => $X1kuadrat,
                                'x2kuadrat' => $X2kuadrat,
                                'x3kuadrat' => $X3kuadrat,
                                'x4kuadrat' => $X4kuadrat,
                                'xtotal'=>$Xtotal,
                                'xtotalkuadrat' =>$XtotalKuadrat,
                                'sumX1' =>$sumX1,
                                'sumX2' =>$sumX2,
                                'sumX3' =>$sumX3,
                                'sumX4' =>$sumX4,
                                'avgX1' =>$avgX1,
                                'avgX2' =>$avgX2,
                                'avgX3' =>$avgX3,
                                'avgX4' =>$avgX4,
                                'sumxtotal'=>$sigmaXtotal,
                                'sumxtotalkuadrat'=>$sigmaXtotalkuadrat,

                                'sigmaX1kuadrat' => $sigmaX1kuadrat,
                                'sigmaX2kuadrat' => $sigmaX2kuadrat,
                                'sigmaX3kuadrat' => $sigmaX3kuadrat,
                                'sigmaX4kuadrat' => $sigmaX4kuadrat,
                                
                                // antar
                                'JKA' => $JKA,
                                'DKA'=>$DKA,
                                'RJKA'=>$RJKA,
                                'F'=>$F,

                                //dalam 
                                'jkd' => $JKD, 
                                'dkd'=> $DKD,
                                'rjkd' => $RJKD,

                                // total 
                                'jkt' => $JKT, 
                                'dkt' => $DKT, 

                                //ftabel
                                'fTabel' => $fTabel,

                                //status
                                'status' => $status,
                    ]);

    }

    public function storeAnava(Request $request){

        $this->validate($request, 
        [            
            'x1'      =>  'required|numeric|min:1|max:100',
            'x2'      =>  'required|numeric|min:1|max:100',
            'x3'      =>  'required|numeric|min:1|max:100',
            'x4'      =>  'required|numeric|min:1|max:100',
        ],
        [
            'x1.min'  =>  'Kolom Skor Hanya Bisa Diisi Angka 1-100',
            'x1.max'  =>  'Kolom Skor Hanya Bisa Diisi Angka 1-100',
            'x1.numeric' => 'Kolom Hanya Bisa Berisi Angka!',
            'x2.min'  =>  'Kolom Skor Hanya Bisa Diisi Angka 1-100',
            'x2.max'  =>  'Kolom Skor Hanya Bisa Diisi Angka 1-100',
            'x2.numeric' => 'Kolom Hanya Bisa Berisi Angka!',
            'x3.min'  =>  'Kolom Skor Hanya Bisa Diisi Angka 1-100',
            'x3.max'  =>  'Kolom Skor Hanya Bisa Diisi Angka 1-100',
            'x3.numeric' => 'Kolom Hanya Bisa Berisi Angka!',
            'x4.min'  =>  'Kolom Skor Hanya Bisa Diisi Angka 1-100',
            'x4.max'  =>  'Kolom Skor Hanya Bisa Diisi Angka 1-100',
            'x4.numeric' => 'Kolom Hanya Bisa Berisi Angka!'
        ]);

        $anava = new UjiAnava;
        $anava->x1 = $request->x1;
        $anava->x2 = $request->x2;
        $anava->x3 = $request->x3;
        $anava->x4 = $request->x4;
        $anava->save();

        return redirect('statistik/ujiAnava')->with('status', 'Data Berhasil Tambah');

    }

    public function exportAnava(){

        return Excel::download(new UjiAnavaExport, time().'_'.'DataUjiAnava.xlsx');
    }

    public function importAnava(Request $request){
        
        $this->validate($request, 
        [            
            'file'      =>  'required|file|mimes:xlsx,csv'
        ],
        [
            'file'      =>  'File Harus Berekstensi .xlsx atau .csv',            
        ]);          

        $file = $request->file('file');       
        $namaFile = $file->getClientOriginalName();
        $file->move('Anava', $namaFile);
        
        $filexcel = Excel::import(new UjiAnavaImport, public_path('/Anava/'.$namaFile));                         
        
        return redirect('statistik/ujiAnava')->with('status', 'Data Uji Anava Berhasil Diimport!');

    }
    public function deleteAnava($id)
   {
       $ujiT = UjiAnava::find($id);         //cari id yang dipencet       
       $ujiT->delete();                  //delete id tersebut

       return redirect('statistik/ujiAnava')->with('status', 'Data Berhasil Dihapus');                //redirect lagi ke home
   }
}

