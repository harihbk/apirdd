<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;
use Response;

class Fileuploadcontroller extends Controller
{
    public function fileUploadPost(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,docx',
            'docpath' => 'required'
        ]);
  
        $orignalName = $request->file->getClientOriginalName();


        $fileName = $orignalName.'.'.$request->file->extension();  
   
        $request->file->move($request->input('docpath'), $fileName);

        $path = $request->input('docpath')."/".$orignalName;
   
        $data = array ("message" => 'File Uploaded successfully',"data" => $path );
        $response = Response::json($data,200);
        return $response;
   
    }
}
