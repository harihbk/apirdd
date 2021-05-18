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
            'file' => 'required|mimes:pdf,docx,png,jpg,jpeg',
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
    function multipledocUpload(Request $request)
    {
       $file_path = [];
       $file_path = $this->uploadFile($request,0,$file_path);
       return response()->json(['response'=>$file_path], 200);
    
    }
    function uploadFile(Request $request,$index,$file_path)
    {
        if($request->hasfile('file'.$index))
        {
            $fileIndex = "file".$index;
            $orignalName = $request->$fileIndex->getClientOriginalName();
            $request->$fileIndex->move($request->input('docpath'), $orignalName);  
            array_push($file_path,$request->input('docpath')."/".$orignalName);
        }
        else
        {
            return $file_path;
        }
        $index++;
        $file_path = $this->uploadFile($request,$index,$file_path);
        return $file_path;
    }   
}
