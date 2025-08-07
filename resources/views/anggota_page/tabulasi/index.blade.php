@extends('layouts.anggota_layout')
@php use Illuminate\Support\Str; @endphp
@php
    use Carbon\Carbon;
    Carbon::setLocale('id');
@endphp

<?php
$main_data = 'Tabulasi';
$url = '/anggota/tabulasi';
?>
@section('title', $main_data)
@section('content')
    <div class="container-fluid">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">
                    {{ $main_data }}
                </h4>
            </div>

            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item">
                        <a href="{{ url($url) }}">
                            Anggota
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ url($url) }}">
                            {{ $main_data }}
                        </a>
                    </li>
                </ol>
            </div>
        </div>
        <div class="alert alert-primary mt-4" role="alert">
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <strong>Distribusi Anggota per Provinsi</strong>
                </div>
                <div class="card-body">
                    <div id="indonesia-map" style="height: 500px; width: 100%;"></div>
                </div>
            </div>

            <div class="alert alert-info">
                <h5 class="fw-bold">Sebaran Anggota per Provinsi</h5>
                <div class="row">
                    @foreach ($rekapProvinsi as $provinsi => $total)
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex justify-content-between border-bottom py-1">
                                <span>{{ $provinsi }}</span>
                                <strong>{{ $total }}</strong>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="table-responsive">
                @foreach ($grouped as $provinsi => $group)
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
                                    @foreach ($group['data'] as $row)
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
 <script>
        document.addEventListener('DOMContentLoaded', function() {
            Highcharts.mapChart('indonesia-map', {
                chart: {
                    map: 'countries/id/id-all'
                },
                title: {
                    text: 'Sebaran Anggota Aktif per Provinsi'
                },
                colorAxis: {
                    min: 0,
                    stops: [
                        [0, '#EFEFFF'],
                        [0.5, '#4472C4'],
                        [1, '#002060']
                    ]
                },
                series: [{
                    data: @json($mapData),
                    name: 'Jumlah Anggota',
                    states: {
                        hover: {
                            color: '#BADA55'
                        }
                    },
                    dataLabels: {
                        enabled: false,
                        format: '{point.name}'
                    }
                }]
            });
        });
    </script>
   
@endsection
