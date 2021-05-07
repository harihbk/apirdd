<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Validator;
use File;

class DocumentController extends Controller
{
    function docUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'file' => 'required|mimes:pdf,docx',
            'docpath' => 'required'
       ]);

       if ($validator->fails()) { 
           return response()->json(['error'=>$validator->errors()], 401);            
       }


       $orignalName = $request->file->getClientOriginalName();


    //    $fileName = $orignalName.'.'.$request->file->extension();  

       $fileName = $orignalName;  
  
       $request->file->move($request->input('docpath'), $fileName);

       $path = $request->input('docpath')."/".$orignalName;
  
       $data = array ("message" => 'File Uploaded successfully',"file_name"=>$orignalName,"file_path" => $path );
       $response = Response::json($data,200);
       return $response;
   
    }
}
