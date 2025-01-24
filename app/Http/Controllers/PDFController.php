<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PDFController extends Controller
{
    public function generateScoresheet()
    {
        $user_id = request()->user_id;

        $data = [
            // Pass dynamic data here if needed
            "term" => "First Term",
            "session" => "2020/2021",
            "student" => array(
                "name" => "John Doe",
                "class" => "JSS1",
                "admission_no" => "JSS1/2020/001",
                "dob" => "01/01/2000",
                "gender" => "Female"

            )
        ];

        // $pdf = Pdf::loadView('pdf.scoresheet', $data);
        // return $pdf->download('JSS1_scoresheet.pdf');

        return view('pdf.scoresheet', compact('data'));
    }
}
