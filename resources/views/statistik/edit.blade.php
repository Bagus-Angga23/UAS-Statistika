@extends('layout.template')

@section('title', 'Edit')

@section('content')
  <div class="container">
    <div class="row">
      <div class="col-10">
        <form action="/statistik/edit/{{ $mahasiswa->id_mahasiswa }}" method="POST">
          @csrf
          @method('PUT')
          <div class="form-group mt-3">
            <label for="nama">Nama Mahasiswa:</label>
            <input type="text" class="form-control" placeholder="Masukan nama" name="nama" id="nama" value="{{ $mahasiswa->nama_mahasiswa }}">
          </div>
          <div class="form-group">
            <label for="nilai">Nilai:</label>
            <input type="number" class="form-control" placeholder="Masukan nilai" name="nilai" id="nilai" value="{{ $mahasiswa->nilai_mahasiswa }}">
          </div>
          <button type="submit" class="btn btn-primary mt-3">Submit</button>
        </form> 
      </div>
    </div>
  </div>
@endsection
