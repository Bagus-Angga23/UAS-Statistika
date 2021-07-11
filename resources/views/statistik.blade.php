@extends('layout.template')
@section('title','statistik')
    
@section('container')
    <div class="row col-12">
        <form action="/statistik" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="form-group mt-3">
            <label for="nama">Nama Mahasiswa:</label>
            <input type="text" class="form-control" placeholder="Masukan nama" name="nama" id="nama">
          </div>
          <div class="form-group">
            <label for="nilai">Nilai:</label>
            <input type="number" class="form-control" placeholder="Masukan nilai" name="nilai" id="nilai">
          </div>
          <button type="submit" class="btn btn-dark mt-3 mb-3">Add</button>
          <label for="min" class="ml-4">Nilai skor min : <b>{{ $min }}</b></label>
          <label for="max" class="ml-4">Nilai skor max : <b>{{ $max }}</b></label>
          <label for="rata2" class="ml-4">skor Rata-rata : <b>{{ $rata2 }}</b></label>
        </form> 
    </div>
  
@if (session('pesan'))
    <div class="row">
      <div class="alert alert-success">{{ session('pesan') }}</div>
    </div>
@endif
        
  <div class="row col-12">
    <table class="table table-bordered m-2">
      <div class=" bg-dark text-light text-center m-2">
        <h2>TABEL DATA MAHASISWA</h2>
      </div> 
      <thead>
        <tr>
          <th class="col-8">Nama</th>
          <th class="col-2">skor</th>
          <th class="col-2">Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($mahasiswa as $data)
          <tr>
            <td>{{ $data->nama_mahasiswa }}</td>
            <td>{{ $data->nilai_mahasiswa }}</td>
            <td>
              <a href="/statistik/edit/{{ $data->id_mahasiswa }}" class="btn btn-sm btn-secondary">Edit</a>
              <a href="/statistik/delete/{{ $data->id_mahasiswa }}" class="btn btn-sm btn-danger" id="delete">Delete</a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>  
  </div>

  <div class="row">
      <div class="col-12 mr-auto">
          <div class="row">
              <div class="col-12 bg-white form-container">
                <div class="bg-dark text-light text-center">
                  <h2>TABEL FREKUENSI</h2>
                </div> 
                  <table class="table table-bordered">
                      <thead>
                          <tr>
                              <th class="col-8">Skor</th>
                              <th class="col-4">Frekuensi</th>
                          </tr>
                     </thead>
                     <tbody>
                         @foreach ($frekuensi as $nilai_mahasiswa)
                         
                         <tr>
                             <td> {{ $nilai_mahasiswa->nilai_mahasiswa }} </td>
                             <td> {{ $nilai_mahasiswa->frekuensi }}</td>
                          </tr>
                           
                          @endforeach
                          <tr>
                            <th>Total frekuensi :</th>
                            <td> {{ $totalfrekuensi }}</td>
                          </tr>
                          <tr>
                            <th>Total skor :</td>
                            <td> {{ $totalskor }}</td>
                          </tr>
                     </tbody>
                  </table>
              </div>
          </div>


@endsection