<?php

namespace App\Http\Controllers\Elearning;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\Matkul;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Requests\MateriRequest;
use App\Models\Jadwal;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class MateriController extends Controller
{
    public function materi($id)
    { 
        
        $jadwal_id =  Crypt::decryptString($id);

        $jadwal = Jadwal::whereId($jadwal_id)->first();

        {
            // if ($kelas->id !== Auth::user()->kelas_id) {
            //     return abort(404);
            // }

            // $user = Auth::user();
            // if(Auth::guard('admin')->user()){
            //     $materis = $kelas->materis()->whereKelasId($kelas->id)->whereMatkulId($matkul->id)->latest()->paginate(5);

            // }elseif(Auth::guard('dosen')->user()){
            //     $dosen_klsId = $user->kelas()->findOrFail($kelas->id);
            //     $dosen_matkulId = $user->matkuls()->findOrFail($matkul->id);
            //     $materis = $kelas->materis()->whereKelasId($dosen_klsId->id)->whereMatkulId($dosen_matkulId->id)->latest()->paginate(5);

            // }else{
            //     $mhs_klsId = Auth::guard('mahasiswa')->user()->kelas_id;
            //     if($mhs_klsId == $kelas->id){
            //         $materis = $kelas->materis()->whereKelasId($mhs_klsId)->whereMatkulId($matkul->id)->orderByDesc('pertemuan')->paginate(5);
            //     }else{
            //         abort(404,'Mahasiswa tidak bisa mengakses kelas lain');
            //     }
            // }
            // return $materis;
        }
        if(Auth::guard('mahasiswa')->user()){
            if(Auth::guard('mahasiswa')->user()->kelas_id == $jadwal->kelas_id){
                $materis = Materi::whereMatkulId($jadwal->matkul_id)->whereDosenId($jadwal->dosen_id)->whereKelasId($jadwal->kelas_id)->latest()->paginate(5);
            }
        }elseif(Auth::guard('dosen')->user() || Auth::guard('admin')->user()){
            $materis = Materi::whereMatkulId($jadwal->matkul_id)->whereDosenId($jadwal->dosen_id)->whereKelasId($jadwal->kelas_id)->latest()->paginate(5);
        }
        // return view('frontend.kelas.materi', compact('materis', 'kelas'));
        return view('frontend.kelas.materi', compact('materis'));
    }

    public function table(Request $request)
    {
        // return Str::between('fadlie ganteng','a','e');
        // $j = $request->merge(['fadlie','ganteng',1]);

        if ($request->wantsJson()) {
            return DataTables::of(Auth::user()->materis()->addSelect([
                'kd_kelas' => Kelas::select('kd_kelas')->whereColumn('kelas_id', 'kelas.id'),
                'nm_matkul' => Matkul::select('nm_matkul')->whereColumn('matkul_id', 'matkuls.id')
            ])->orderByDesc('pertemuan'))
                ->addColumn('action', function ($materi) {
                    $button = '
                        <div class="dropdown d-inline">
                            <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Action
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item has-icon" href="' . route("materi.edit", $materi) . '"><i class="
                                fas fa-edit"></i> Edit</a>
                                <form action="' . route("materi.delete", $materi) . '" method="post" style="font-size:13px">
                                    ' . csrf_field() . '
                                    ' . method_field('delete') . '
                                    <button type="submit" class="dropdown-item has-icon font-sm"><i class="fas fa-times"></i> Delete</button>
                                </form>
                                <a class="dropdown-item has-icon" href="' . route("materi.delete", $materi) . '"><i class="
                                fas fa-list-alt"></i> Detail</a>
                            </div>
                        </div>
                ';
                    return $button;
                })
                ->make(true);
        } else {
            $materis = Auth::user();
        }

        return view('materi.table', compact('materis'));
    }

    public function show(Matkul $matkul, Request $request)
    {
        // return $matkul->materis;
        // dd(DataTables::of($matkul->materis)->make(true));
        // if($request->wantsJson()){
        //     return DataTables::of($matkul->materis()->with('kelas','matkul'))
        //     ->make(true);
        // }
        // return Auth::guard('dosen')->user();

        if (Auth::guard('mahasiswa')->user()) {
            $materis =  $matkul->materis()->where('kelas_id', Auth::user()->kelas->id)->latest()->paginate(5);
        } else {
            $materis = $matkul->materis()->latest()->paginate(5);
        }
        return view('materi.show', compact('materis'));
    }

    public function upload()
    {
        //get name of current user
        $dosen = Auth::user();
        //prepare an array variable for accommodate the array value from kelas
        $kelas = [];
        //loop the kelas and then push to variable $kelas
        foreach ($dosen->kelas as $k) {
            array_push($kelas, $k);
        }

        //prepare an array variable for accommodate the array value from kelas
        $matkuls = [];
        //loop the matkuls and then push to variable $matkuls
        foreach ($dosen->matkuls as $matkul) {
            array_push($matkuls, $matkul);
        }


        return view('materi.upload', [
            'matkuls' => $matkuls,
            'kelas' => $kelas
        ]);
    }

    public function store(MateriRequest $request)
    {
        // Membuat sekaligus materi untuk kelas yang berbeda dikarenakan jadwal,materi,petermuan nya sama
        // Bisa juga membuat hanya 1 jadwal,materi,petermuan
        
        if (request('kelas') > 1) {
            for ($i = 0; $i < count(request('kelas')); $i++) {
                $materi = $request->all();
                $materi['kelas_id'] = $request->kelas[$i];
                $materi['matkul_id'] = $request->matkul;

                
                if($request->tipe == 'pdf'){
                    $fileName = time() .'.'.$request->file('file_or_link')->extension();
                    $materi['file_or_link'] = $request->file('file_or_link')->move("materials",$fileName);
                }
                Auth::user()->materis()->create($materi);
            }
        }
        return back()->with('success', 'Berhasil membuat materi');
    }
}
