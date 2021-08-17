<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Upload;

class HomeController extends Controller
{

    public function index()
    {
        $files = Upload::all();
        foreach ($files as $file ) {
            echo '<img src="'.asset($file->path).'" alt="">';
        }


    }
    
    public function upload(Request $request)
    {
        try {
            if($request->hasFile('image')){

                
                // $path = $request->file('image')->store('public');
                // $path = Storage::putFile('public',$request->file('image'));


                // custom nama
                // $path = $request->file('image')->storeAs('public','gambar');

                $files = $request->file('image');
                
                foreach ($files as $file ) {
                    $name = time();
                    $extension = $file->getClientOriginalExtension();
                    $newName= $name.'.'.$extension;
                    // $path = $request->file('image')->storeAs('public',$newName);
                    // simpan file di folder photo
                    $size = $file->getClientSize();
                    $path = Storage::putFileAs('public',$request->file('image'),$newName);
                    // dd($path);

                    // upload ke db
                    $data =[
                        'path' => 'storage/'.$newName,
                        'size' => $size,
                    ];
                }
                

                return Upload::create($data);
                }
            } catch (\Exception $e) {
                $e->getMessage();
            }
    }

    public function list()
    {
        // mendapatkan file di storage
        // $files = Storage::files('');
        // $files = Storage::allFiles('public');
        // $files = Storage::allFiles('');

        // mendapatkan direktori di storage
        // $files = Storage::directories('');
        $directory = Storage::allDirectories('');

        // membuat directory
        // $directory = Storage::makeDirectory('image');
        // $directory = Storage::makeDirectory('image/gif');

        // menhapus directory
        // $directory =  Storage::deleteDirectory('photo');
        // $directory =  Storage::deleteDirectory('image/gif');
        dd($directory);
    }

    public function show()
    {
        // menampilkan file
        $path = Storage::url('1629089042.png');
        return '<img src="'.$path.'"> ';
        // return '<img src="'.asset('/storage/629089042.png').'"> ';
    }

    public function copy()
    {
        // copy image di folder storage
        try {
            // file yang akan di copy , nama file dan tujuan
            Storage::copy('/public/1629089042.png','image/copy-image.png');
            return 'success';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    
    public function move()
    {
        // move image di folder storage
        try {
            // Storage::move('path',target)
            Storage::move('image/copy-image.png','public/move-image.png');
            return 'move success';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function download()
    {
        try {
           return Storage::disk('local')->download('public/upload/1629181583.png');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    
    public function delete()
    {
        try {
            Storage::disk('local')->delete('public/upload/1629181583.png');
            return 'deleted';
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
