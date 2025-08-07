@extends('layouts.admin_layout')
@section('title', 'Dashboard')
@section('content')
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Tabulasi</h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Components</a></li>
                <li class="breadcrumb-item active">Tabulasi</li>
            </ol>
        </div>
    </div>
     <div class="alert alert-primary mt-4" role="alert">
  
                <a href="{{ route('rekap.excel') }}" class="btn btn-success mb-3">
                    <i class="bi bi-file-earmark-excel"></i> Export Tabulasi Excel
                </a>

                <div class="table-responsive">
                   @foreach($grouped as $provinsi => $group)
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <strong>{{ $provinsi }}</strong> — Total: {{ $group['subtotal'] }} Anggota
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Perguruan Tinggi</th>
                                        <th>Jumlah Anggota</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($group['data'] as $row)
                                        <tr>
                                            <td>{{ $row['pt'] }}</td>
                                            <td>{{ $row['jumlah'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

                </div>


              </div>

  
</div>
@endsection