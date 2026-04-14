<?php

namespace App\Http\Controllers;

use App\Imports\BeneficiariesImport;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

use function back;
use function redirect;

class BeneficiaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        return Inertia::render('Beneficiary/Upload');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => ['required', 'mimes:xlsx,xls'],
        ]);

        $file_path = Storage::putFile('profiles', $request->file);

        try {
            (new BeneficiariesImport)->import($file_path);
        } catch (Exception $e) {
            return back()->with('error', 'Something Went Wrong! Please Contact Administrator ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Beneficiaries Successfully Uploaded');
    }
}
