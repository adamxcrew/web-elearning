<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{Auth, Crypt};
use App\Models\{Absen, Jadwal, Materi, Mahasiswa};

class KelasController extends Controller
{

    public function masuk($jadwalId)
    {
        //parameter $jadwalId adalah id dari jadwal yang sudah di encrypt
        //dan kode dibawah untuk mencari jadwal dari param $jadwalId sekalian di decrypt var $jadwalId nya
        $jadwal = Jadwal::where('id', decrypt($jadwalId))->first();

        $mahasiswa = Mahasiswa::with(['mahasiswaAbsenHariIni' => function($q) use ($jadwal){
            $q->where('jadwal_id', $jadwal->id);
        }])->where('kelas_id', $jadwal->kelas_id)->get();

        $absen = Absen::where('dosen_id', Auth::Id())
            ->where('jadwal_id', $jadwal->id)
            ->whereDate('created_at', date('Y-m-d'))
            ->first();

            // foreach ($mahasiswa as $mhs) {
            //     echo $mhs->absens[0]->status ? 'absen' : 'tidak'. '<hr>';
            // }

            // return $mahasiswa;


        return view('frontend.dosen.kelas.masuk', compact('mahasiswa', 'jadwal', 'absen'));
    }

    public function storeAbsen()
    {
        $absen = collect(Absen::where('dosen_id', Auth::Id())
        ->where('jadwal_id', request('jadwal'))
        ->whereDate('created_at', date('Y-m-d'))
        ->first());

        //Jika parent absen belom dibuat jangan kasih create absen
        if ($absen->isNotEmpty()) {
            for ($i = 0; $i < count(request('mahasiswa')); $i++) {
                Absen::updateOrCreate(
                    [
                        'mahasiswa_id' => request('mahasiswa')[$i],
                        'parent' => $absen['id']
                    ],
                    [
                        'parent' => request('parent'),
                        'status' => request('status')[$i],
                        'jadwal_id' => request('jadwal'),
                        'pertemuan' => request('pertemuan'),
                    ]
                );
            }
            session()->flash('success', 'Berhasil menyimpan data absen');
        }

        return back();
    }

    public function materi($jadwalId)
    {

        // return Mahasiswa::get();
        //Setelah di decrypt cari. apakah id ada di dalam table jadwal
        //jika ada tampilkan hanya 1
        $jadwal = Jadwal::where('id', decrypt($jadwalId))->firstOrFail();
        $materis = Materi::with(['matkul','kelas'])
            ->where('matkul_id', $jadwal->matkul_id)
            ->where('dosen_id', $jadwal->dosen_id)
            ->where('kelas_id', $jadwal->kelas_id)
            ->latest()->get();

        return view('frontend.dosen.kelas.materi', compact('materis','jadwal'));
    }
}
