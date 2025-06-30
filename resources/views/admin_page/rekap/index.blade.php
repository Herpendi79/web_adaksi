@extends('layouts.admin_layout')
@section('title', 'Dashboard')
@section('content')
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Rekap</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Components</a></li>
                <li class="breadcrumb-item active">Rekap</li>
            </ol>
        </div>
    </div>
     <div class="alert alert-primary mt-4" role="alert">
              <h4 class="mb-3">Jumlah Anggota Divalidasi Per Tanggal</h4>
    <form method="GET" action="{{ route('rekap.anggota') }}" class="row g-3 mb-4">
        <div class="col-md-4">
            <label for="start_date" class="form-label">Tanggal Mulai</label>
            <input type="date" class="form-control" id="start_date" name="start_date"
                value="{{ request('start_date') }}"
                min="2025-06-01"> {{-- ✅ hanya mulai dari Juni 2025 --}}
        </div>
        <div class="col-md-4">
            <label for="end_date" class="form-label">Tanggal Akhir</label>
            <input type="date" class="form-control" id="end_date" name="end_date"
                value="{{ request('end_date') }}"
                min="2025-06-01"> {{-- ✅ hanya mulai dari Juni 2025 --}}
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>


<div class="table-responsive">
    <table class="table table-bordered">
        <thead class="table-light">
           <tr>
            <th>Tanggal</th>
            <th>Jumlah Anggota</th>
            <th>Nominal (x Rp 100.000)</th>
        </tr>
        </thead>
        <tbody>
            @forelse ($rekap as $item)
            <tr>
                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d-m-Y') }}</td>
                <td>{{ $item->total }}</td>
                <td>Rp {{ number_format($item->total * 100000, 0, ',', '.') }}</td> {{-- ✅ Perkalian --}}
            </tr>
        @empty
            <tr>
                <td colspan="2">Tidak ada data</td>
            </tr>
        @endforelse
         @if ($rekap->count())
            <tr class="table-success fw-bold">
                <td colspan="2" class="text-end">Total Keseluruhan</td>
                <td>Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>


              </div>

  
</div>
@endsection