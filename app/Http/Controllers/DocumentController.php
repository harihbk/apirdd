<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;
use Validator;
use File;
use ZipArchive;
use \RecursiveIteratorIterator;
use \RecursiveDirectoryIterator;

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
  
       $request->file->move(trim($request->input('docpath'),'"'), $fileName);

       $path = trim($request->input('docpath'),'"')."/".$orignalName;
  
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
            $request->$fileIndex->move(trim($request->input('docpath'),'"'), $orignalName);  
            array_push($file_path,trim($request->input('docpath'),'"')."/".$orignalName);
        }
        else
        {
            return $file_path;
        }
        $index++;
        $file_path = $this->uploadFile($request,$index,$file_path);
        return $file_path;
    }  
    
    /* Document download block */
    function projectDocszip(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'project_id' => 'required',
            'project_name' => 'required', 
            'doc_path' => 'required', 
            'image_path' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }


        $docPath = realpath($request->input('doc_path'));
        $imgPath = realpath($request->input('image_path'));
        if($docPath=='' &&  $imgPath=='')
        {
            return response()->json(['response'=>"Paths not found"], 410);
        }
        try
        {
                 // Initialize archive object
                $zip = new ZipArchive();
                $zipFileName = $request->input('project_name').'.zip';
                $zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE);


                if (!$this->is_dir_empty($docPath)) 
                {
                    $doc_files = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($docPath),
                        RecursiveIteratorIterator::LEAVES_ONLY
                    );

                    foreach ($doc_files as $name => $file)
                    {
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($docPath) + 1);
                        $final_doc = "documents/".$relativePath;
                        if (!$file->isDir())
                        {
                            $zip->addFile($filePath, $final_doc);
                        }else {
                            if($relativePath !== false)
                                $zip->addEmptyDir($final_doc);
                        }
                    } 
                }
                if(!$this->is_dir_empty($imgPath))
                {
                    $img_files = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($imgPath),
                        RecursiveIteratorIterator::LEAVES_ONLY
                    );

                    foreach ($img_files as $name => $file)
                    {
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($imgPath) + 1);
                        $final_image = "images/".$relativePath;
                        if (!$file->isDir())
                        {
                            $zip->addFile($filePath, $final_image);
                        }else {
                            if($relativePath !== false)
                                $zip->addEmptyDir($final_image);
                        }
                    }
                }
                if($this->is_dir_empty($imgPath) && $this->is_dir_empty($docPath))
                {
                    return response()->json(['response'=>"No files found in paths"], 410);
                }
                $zip->close();
                return response()->download($zipFileName);
        }
        catch(\Exception $e)
        {
            return $e->getMessage();
        }
    }
    function phaseDocszip(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'project_id' => 'required',
            'project_name' => 'required', 
            'path' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $path = realpath($request->input('path'));
        if($path=='')
        {
            return response()->json(['response'=>"Path not found"], 410);
        }
        try
        {
            // Initialize archive object
            $zip = new ZipArchive();
            $zipFileName = $request->input('project_name').'.zip';
            $zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE);
            if ($this->is_dir_empty($path)) 
            {
                return response()->json(['response'=>"Folder has no files"], 410); 
            }
            else
            {
                $doc_files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($path),
                    RecursiveIteratorIterator::LEAVES_ONLY
                );
                foreach ($doc_files as $name => $file)
                {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($path) + 1);
                    $final_doc = $relativePath;
                    if (!$file->isDir())
                    {
                        $zip->addFile($filePath, $final_doc);
                    }else {
                        if($relativePath !== false)
                            $zip->addEmptyDir($final_doc);
                    }
                }
                $zip->close();
                return response()->download($zipFileName);
            }
        }
        catch(\Exception $e)
        {
            return $e->getMessage();
        }
    }
    function alldocumentzip(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'doc_paths' => 'required',
            'image_paths' => 'required', 
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $doc_path = $request->input('doc_paths');
        $image_path = $request->input('image_paths');

        if(count($doc_path)==0)
        {
            return response()->json(['response'=>"No document Paths"], 410);
        }
        if(count($image_path)==0)
        {
            return response()->json(['response'=>"No Image Paths"], 410);
        }
        try
        {
                // Initialize archive object
                $zip = new ZipArchive();
                $zipFileName = 'alldocs.zip';
                $zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE);
                for($i=0;$i<count($doc_path);$i++)
                {
                    $path = realpath($doc_path[$i]);
                    if($path=='')
                    {
                        continue;
                    }
                    $doc_files = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($doc_path[$i]),
                        RecursiveIteratorIterator::LEAVES_ONLY
                    );

                    foreach ($doc_files as $name => $file)
                    {
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($path) + 1);
                        $final_doc = "documents/".basename($doc_path[$i])."/".$relativePath;
                        if (!$file->isDir())
                        {
                            $zip->addFile($filePath, $final_doc);
                        }else {
                            if($relativePath !== false)
                                $zip->addEmptyDir($final_doc);
                        }
                    }
                }
                for($j=0;$j<count($image_path);$j++)
                {
                    $path = realpath($image_path[$j]);
                    if($path=='')
                    {
                        continue;
                    }
                    $img_files = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($image_path[$j]),
                        RecursiveIteratorIterator::LEAVES_ONLY
                    );

                    foreach ($img_files as $name => $file)
                    {
                        $filePath = $file->getRealPath();
                        $relativePath = substr($filePath, strlen($path) + 1);
                        $final_doc = "images/".basename($image_path[$j])."/".$relativePath;
                        if (!$file->isDir())
                        {
                            $zip->addFile($filePath, $final_doc);
                        }else {
                            if($relativePath !== false)
                                $zip->addEmptyDir($final_doc);
                        }
                    }
                }
                $zip->close();
                return response()->download($zipFileName);
        }
        catch(\Exception $e)
        {
            return $e->getMessage();
        }
    }
    function is_dir_empty($dir) {
        if (!is_readable($dir)) return NULL; 
        return (count(scandir($dir)) == 2);
      }

    function checking(Request $request)
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
  
       $request->file->move(trim($request->input('docpath'),'"'), $fileName);

       $path = trim($request->input('docpath'),'"')."/".$orignalName;
  
       $data = array ("message" => 'File Uploaded successfully',"file_name"=>$orignalName,"file_path" => $path );
       $response = Response::json($data,200);
       return $response;
    }

}
