@extends('layouts.admin_layout')

@section('title', 'Import Anggota')

@section('content')
<h1>Beluuuummm, sabarrrr dulu yaaahhhh</h1>
<ul>

    <!-- tampilan import excel -->
    <div class="container">
        <h2>Import Anggota</h2>
        <form action="{{ url('admin/anggota/import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="file" class="form-label">Pilih File Excel</label>
                <input type="file" class="form-control" id="file" name="file" accept=".xlsx, .xls, .csv" required>
            </div>
            <button type="submit" class="btn btn-primary">Import</button>
        </form>
    </div>
    <!-- @foreach ($calon as $item)
    <li>{{ $item->nama_anggota }} - {{ $item->status_anggota }}</li>
    @endforeach -->
</ul>
@endsection