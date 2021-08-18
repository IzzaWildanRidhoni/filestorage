# manipulasi filestorage di laravel

## **mengatur env dengan database**

sebelum membuat program kita buat terlebih dahulu database kita di phpmyadmin dengan nama **db_laravel_filestorage**
. kemudian kita ubah juga di dalam file env kita menjadi seperti ini

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_laravel_filestorage
DB_USERNAME=root
DB_PASSWORD=
```

---

## **1. merubah welcome.blade.php**

di program yang saya buat saya merubah `welcome.blade.php ` bagian content menjadi seperti ini

```html
<div class="content">
    <div class="title m-b-md">Laravel</div>

    <form action="/upload" method="POST" enctype="multipart/form-data">
        @csrf {{-- <input type="file" name="image" id="" /> --}} {{-- untuk
        multiple upload --}}
        <input type="file" name="image[]" id="" multiple="true" />
        <button type="submit">Submit</button>
    </form>
</div>
```

> untuk melakukan upload secara massal dapat menambahkan `enctype` , `[]` dibagian name dan menambah kan `multiple="true"`.

## **2. menambahkan route**

menambahkan route website kita tepatnya di dalam file `web.php` menjadi

```php

Route::get('/', function () {
    return view('welcome');
});

Route::get('index','HomeController@index');
Route::post('upload','HomeController@upload');
Route::get('list','HomeController@list');
Route::get('show','HomeController@show');
Route::get('copy','HomeController@copy');
Route::get('move','HomeController@move');
Route::get('download','HomeController@download');
Route::get('delete','HomeController@delete');
```

## **3.Membuat model dan migration**

untuk membuat model sekaligus membuat migration bisa langsung menggunakan perintah

```bash
php artisan make:model Upload -m
```

arti nya buatkan saya model **Upload** beserta migratinnya , nama class migrationnya akan otomatis menjadi **CreateUploadsTable**

### **4.membuat struktur table upload**

kita rubah struktur tabel tepatnya di method `up` di file `create_uploads_table`

```php
public function up(){
        Schema::create('uploads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('path');
            $table->string('size');
            $table->timestamps();
        });
    }
```

setelah dirubah lakukan

```bash
php artisan migrate
```

maka akan dibuatkan migrasi table **upload** di database kita

### **5.membuat simlink**

```artisan
php artisan storage:link
```

maka akan dibuatkan simlink antara antara folder `storage/public/app` pada laravel , dengan `folder public/storage`

### **6. merubah properti**

merubah properti di `HomeController`

```php
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;//memanggil storage
use App\Upload; //memanggil model upload
```

### **7.Menampilkan data gambar**

untuk menampiklan data saya membuat method `index` di dalam **HomeController**

```php
public function index()
{
    $files = Upload::all();
    foreach ($files as $file ) {
        echo '<img src="'.asset($file->path).'" alt="">';
    }
}
```

### **8.Membuat method upload**

membuat method upload di `HomeController`

```php
public function upload(Request $request)
    {
        try {
            // apakah ada file image?
            if($request->hasFile('image')){


                // $path = $request->file('image')->store('public');
                // $path = Storage::putFile('public',$request->file('image'));


                // custom nama
                // $path = $request->file('image')->storeAs('public','gambar');

                $files = $request->file('image');

                foreach ($files as $file ) {
                    $name = rand(1,999);
                    $extension = $file->getClientOriginalExtension();
                    $newName= $name.'.'.$extension;
                    // $path = $request->file('image')->storeAs('public',$newName);
                    $size = $file->getClientSize();

                    // simpan file di folder public
                    $path = Storage::putFileAs('public',$file,$newName);

                    // dd($path);

                    // upload ke db
                    $data =[
                        'path' => 'storage/'.$newName,
                        'size' => $size,
                    ];

                  Upload::create($data);
                }

                return 'Success upload multiple';

            }

            return 'empty file';

            } catch (\Exception $e) {
                $e->getMessage();
            }
    }
```

### 9. **menampilkan file / directories**

masih di `HomeController` tambahkan method list

```php
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
```

### **10. memanipulasi data**

_menampilkan, copy, move, download, dan delete_ gambar/file di laravel

```php

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
```

# finish

```bash
php artisan serve
```
