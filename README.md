Create Laravel 6 Application
First of all we need to create a fresh laravel project, download and install Laravel 6 using the below command

composer create-project --prefer-dist laravel/laravel laraImportExcel
1
composer create-project --prefer-dist laravel/laravel laraImportExcel
Configure Database In .env file
Now, lets create a MySQL database and connect it with laravel application. After creating database we need to set database credential in application’s .env file.

.env

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laraImportExcel
DB_USERNAME=root
DB_PASSWORD=


DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laraImportExcel
DB_USERNAME=root
DB_PASSWORD=
Create Model and Migration
Now, we have to define table schema for contact table. Open terminal and let’s run the following command to generate a Contact model along with a migration file to create contact table in our database.

php artisan make:model Contact -m
1
php artisan make:model Contact -m
Once this command is executed you will find a migration file created under “database/migrations”. Lets open migration file created and put following code in it –

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}

<?php
 
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
 
class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->timestamps();
        });
    }
 
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
Run Laravel Migration
Now, run following command to migrate database schema.

Recommended:-  Laravel Clear View Cache
php artisan migrate
1
php artisan migrate
After, the migration executed successfully the contact table will be created in database along with a model file Contact.php in app directory.

app/Contact.php

<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class Contact extends Model
{
     protected $fillable = [
        'name', 'email', 'phone'
    ];
}

<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class Contact extends Model
{
     protected $fillable = [
        'name', 'email', 'phone'
    ];
}
Install Maatwebsite Package
In this step, we will install Maatwebsite Package via the composer dependency manager. Use the following command to install Maatwebsite Package.

composer require maatwebsite/excel
1
composer require maatwebsite/excel
Configure Maatwebsite Package
After Installing Maatwebsite package, we need to add service provider and alias in config/app.php file as following.

config/app.php

'providers' => [
  .......
  Maatwebsite\Excel\ExcelServiceProvider::class,
 
 ],  

'aliases' => [ 
  .......
  'Excel' => Maatwebsite\Excel\Facades\Excel::class,

],

'providers' => [
  .......
  Maatwebsite\Excel\ExcelServiceProvider::class,
 
 ],  
 
'aliases' => [ 
  .......
  'Excel' => Maatwebsite\Excel\Facades\Excel::class,
 
],
Now, use following command to publish Maatwesite Package configuration file:

php artisan vendor:publish
1
php artisan vendor:publish
This will create Maatwesite Package configuration file named “config/excel.php”.

Create Import Class
Now we will create a import class for Contact model to use in our ImportExcelController. Use the following command to create import class.

php artisan make:import ImportContacts --model=Contact
1
php artisan make:import ImportContacts --model=Contact
After, the above command executed successfully it will create ImportContacts.php file in Imports directory.

Imports/ImportContacts.php

<?php

namespace App\Imports;

use App\Contact;
use Maatwebsite\Excel\Concerns\ToModel;

class ImportContacts implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Contact([
            //
            'name'     => @$row[0],
            'email'    => @$row[1], 
            'phone'    => @$row[2]
        ]);
    }
}

<?php
 
namespace App\Imports;
 
use App\Contact;
use Maatwebsite\Excel\Concerns\ToModel;
 
class ImportContacts implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Contact([
            //
            'name'     => @$row[],
            'email'    => @$row[1], 
            'phone'    => @$row[2]
        ]);
    }
}
Create Import Excel Controller
Next, we have to create a controller to display a form to upload excel file records. Lets Create a controller named ImportExcelController using command given below –

php artisan make:controller ImportExcel/ImportExcelController
1
php artisan make:controller ImportExcel/ImportExcelController
Once the above command executed, it will create a controller file ImportExcelController.php in app/Http/Controllers/ImportExcel directory. Open the ImportExcel/ImportExcelController.php file and put the following code in it.

Recommended:-  How to Fix "Port 4200 is already in use" error
app/Http/Controllers/ImportExcel/ImportExcelController.php

<?php

namespace App\Http\Controllers\ImportExcel;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Imports\ImportContacts;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use App\Contact;

class ImportExcelController extends Controller

{

    public function index()
    {
        $contacts = Contact::orderBy('created_at','DESC')->get();
        return view('import_excel.index',compact('contacts'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required'
        ]);
        Excel::import(new ImportContacts, request()->file('import_file'));
        return back()->with('success', 'Contacts imported successfully.');
    }

}

<?php
 
namespace App\Http\Controllers\ImportExcel;
 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Imports\ImportContacts;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use App\Contact;
 
class ImportExcelController extends Controller
 
{
 
    public function index()
    {
        $contacts = Contact::orderBy('created_at','DESC')->get();
        return view('import_excel.index',compact('contacts'));
    }
 
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required'
        ]);
        Excel::import(new ImportContacts, request()->file('import_file'));
        return back()->with('success', 'Contacts imported successfully.');
    }
 
}
Here In the controller, we have following methods –

index() :- It displays File Upload Form.

import() :- To Upload excel file and Save records in database .

Create Blade / View Files
In this step, we will create view/blade file to upload excel file. Lets create a blade file “index.blade.php” in “resources/views/import_excel/” directory and put the following code in it respectively.

resources/views/import_excel/index.blade.php

<!DOCTYPE html>
<html>
<head>
    <title>Laravel 6 Import Excel to database - W3Adda</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" ></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" ></script>
</head>

<body>
    
<div class="container">
    <div class="card mt-4">
        <div class="card-header">
            Laravel 6 Import Excel to database - W3Adda
        </div>
            @if ($errors->any())
        <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
   @if($message = Session::get('success'))
   <div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
           <strong>{{ $message }}</strong>
   </div>
   @endif
        <div class="card-body">
            <form action="{{ url('import-excel') }}" method="POST" name="importform" enctype="multipart/form-data">
                @csrf
                <input type="file" name="import_file" class="form-control">
                <br>
                <button class="btn btn-success">Import File</button>
            </form>
        </div>
    </div>
    <div class="panel panel-default">
    <div class="panel-heading">
     <h3 class="panel-title">Customer Data</h3>
    </div>
    <div class="panel-body">
     <div class="table-responsive">
      <table class="table table-bordered table-striped">
       <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
               </tr>
       @foreach($contacts as $c)
       <tr>
        <td>{{ $c->name }}</td>
        <td>{{ $c->email }}</td>
        <td>{{ $c->phone }}</td>
       </tr>
       @endforeach
      </table>
     </div>
    </div>
</div>

</div>
    
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Laravel 6 Import Excel to database - W3Adda</title>
 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
 
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" ></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" ></script>
</head>
 
<body>
    
<div class="container">
    <div class="card mt-4">
        <div class="card-header">
            Laravel 6 Import Excel to database - W3Adda
        </div>
            @if ($errors->any())
        <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
   @if($message = Session::get('success'))
   <div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
           <strong>{{ $message }}</strong>
   </div>
   @endif
        <div class="card-body">
            <form action="{{ url('import-excel') }}" method="POST" name="importform" enctype="multipart/form-data">
                @csrf
                <input type="file" name="import_file" class="form-control">
                <br>
                <button class="btn btn-success">Import File</button>
            </form>
        </div>
    </div>
    <div class="panel panel-default">
    <div class="panel-heading">
     <h3 class="panel-title">Customer Data</h3>
    </div>
    <div class="panel-body">
     <div class="table-responsive">
      <table class="table table-bordered table-striped">
       <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
               </tr>
       @foreach($contacts as $c)
       <tr>
        <td>{{ $c->name }}</td>
        <td>{{ $c->email }}</td>
        <td>{{ $c->phone }}</td>
       </tr>
       @endforeach
      </table>
     </div>
    </div>
</div>
 
</div>
    
</body>
</html>
Create Import Excel Routes
After this, we need to add following routes in “routes/web.php” file along with a resource route. Lets open “routes/web.php” file and add following route.

Recommended:-  Laravel 5.8 Jquery UI Autocomplete Search Example
routes/web.php

Route::get('import-excel', 'ImportExcel\ImportExcelController@index');
Route::post('import-excel', 'ImportExcel\ImportExcelController@import');
1
2
Route::get('import-excel', 'ImportExcel\ImportExcelController@index');
Route::post('import-excel', 'ImportExcel\ImportExcelController@import');
Now we are ready to run our example so lets start the development server using following artisan command –

php artisan serve
1
php artisan serve
Now, open the following URL in browser to see the output –

http://127.0.0.1:8000/import-excel"# laravel-excel-import" 
