<?php

namespace App\Http\Controllers\ImportExcel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
