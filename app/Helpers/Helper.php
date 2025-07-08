<?php

function parseRupiahTambah000($value) {
    $angka = str_replace('.', '', $value); // hapus titik
    return $angka; // tambah 000
}

