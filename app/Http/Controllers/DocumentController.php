<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Handovercertificate;
use App\Models\FitoutCompletionCertificates;
use App\Models\FitoutDepositrefund;

use App\Models\Properties;
use App\Models\Units;
use App\Models\Floor;
use App\Models\Project;

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
            'file' => 'required|mimes:pdf,docx,png,jpg,jpeg,dwg',
            'docpath' => 'required'
       ]);

       if ($validator->fails()) {
           return response()->json(['error'=>$validator->errors()], 401);
       }

       $orignalName = $request->file->getClientOriginalName();

       $docPath = explode("/uploads",$request->input('docpath'));
    //    $fileName = $orignalName.'.'.$request->file->extension();

     if(!File::isDirectory(trim($request->input('docpath'),'"'))){
        File::makeDirectory($request->input('docpath'), 0777, true, true);
        chmod($request->input('docpath'),0777);
        }
       $fileName = $orignalName;

       $request->file->move(trim($request->input('docpath'),'"'), $fileName);

       $path = trim($request->input('docpath'),'"')."/".$orignalName;

       chmod($path, 0777);

       $data = array ("message" => 'File Uploaded successfully',"file_name"=>$orignalName,"file_path" => $path );
       $response = Response::json($data,200);
       return $response;

    }

    function docUpload123(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'file' => 'required|mimes:pdf,docx,png,jpg,jpeg,dwg',
            'docpath' => 'required'
       ]);

       if ($validator->fails()) { 
           return response()->json(['error'=>$validator->errors()], 401);            
       }

       $orignalName = $request->file->getClientOriginalName();

       $docPath = explode("/uploads",$request->input('docpath'));
    //    $fileName = $orignalName.'.'.$request->file->extension();  
       
     if(!File::isDirectory(trim($request->input('docpath'),'"'))){
        File::makeDirectory($request->input('docpath'), 0777, true, true);
        chmod($request->input('docpath'),0777);
        }
       $fileName = $orignalName;  
  
       $request->file->move(trim($request->input('docpath'),'"'), $fileName);

       $path = trim($request->input('docpath'),'"')."/".$orignalName;

       chmod($path, 0777);

       $data = array ("message" => 'File Uploaded successfully',"file_name"=>$orignalName,"file_path" => $path );
       $response = Response::json($data,200);
       return $response;
   
    }



   function _invgetpath(Request $request,$project_id){
    $project = Project::where('project_id',$project_id)->first();
    $property_id = $project->property_id;
    $unit_id = $project->unit_id;




    $propertyname = Properties::where('property_id',$property_id)->pluck('property_name')->first();
    $prop =  public_path("/property/$propertyname/property");
    $hideName = array('.','..','.DS_Store');
   $arr = [];
    if (file_exists($prop)) {
        $prope = scandir($prop);
    foreach ($prope as &$value) {
        if(!in_array($value, $hideName)){
            $arr['property']['name'][] = $propertyname;
            $arr['property']['docs'][] = $value;
            $arr['property']['url'][] = $prop."/".$value;
            $arr['property']['info'][] = pathinfo($prop."/".$value, PATHINFO_EXTENSION);

        }
    }
    }

    $_units = Units::where(['property_id'=>$property_id,'unit_id'=>$unit_id])->first();
    $floor_id = $_units->floor_id;
        $floorno = Floor::where('property_id',$property_id)->where('floor_id',$floor_id)->pluck('floor_no')->first();
        $floorpaths =  public_path("/property/$propertyname/floor$floorno/floor");
        $hideName = array('.','..','.DS_Store');
         if (file_exists($floorpaths)) {
             $_floorpaths = scandir($floorpaths);
         foreach ($_floorpaths as &$value) {
             if(!in_array($value, $hideName)){
                 $arr['floor']['name'][] = "floor$floorno";
                 $arr['floor']['docs'][] = $value;
                 $arr['floor']['url'][] = $floorpaths."/".$value;
                 $arr['floor']['info'][] = pathinfo($floorpaths."/".$value, PATHINFO_EXTENSION);
             }
         }
         } else {
            $arr['floor'] = [];
         }



         $unitname = $_units->unit_name;
            $floorno = Floor::where('property_id',$property_id)->where('floor_id',$floor_id)->pluck('floor_no')->first();
            $pathunits =  public_path("/property/$propertyname/units/floor$floorno/$unitname");
            $hideName = array('.','..','.DS_Store');
             if (file_exists($pathunits)) {
                 $unitprop = scandir($pathunits);
             foreach ($unitprop as &$value) {
                 if(!in_array($value, $hideName)){
                     $arr['unit']['name'][] = $unitname;
                     $arr['unit']['docs'][] = $value;
                     $arr['unit']['url'][] = $pathunits."/".$value;
                     $arr['unit']['info'][] = pathinfo($pathunits."/".$value, PATHINFO_EXTENSION);
                 }
             }
             } else {
                $arr['unit']=[];
             }

  return $arr;
   }


   function _invgetpath_bn(Request $request,$project_id){
    $project = Project::where('project_id',$project_id)->first();
    $property_id = $project->property_id;
    $unit_id = $project->unit_id;




    $propertyname = Properties::where('property_id',$property_id)->pluck('property_name')->first();
    $prop =  public_path("/property/$propertyname/property");
    $hideName = array('.','..','.DS_Store');
   $arr = [];
    if (file_exists($prop)) {
        $prope = scandir($prop);
    foreach ($prope as &$value) {
        if(!in_array($value, $hideName)){
            $arr['property']['docs'][] = $value;
            $arr['property']['url'][] = $prop."/".$value;
            $arr['property']['info'][] = pathinfo($prop."/".$value, PATHINFO_EXTENSION);

        }
    }
    }



    $_units = Units::where(['property_id'=>$property_id,'unit_id'=>$unit_id])->first();
    $floor_id = $_units->floor_id;
        $floorno = Floor::where('property_id',$property_id)->where('floor_id',$floor_id)->pluck('floor_no')->first();
        $floorpaths =  public_path("/property/$propertyname/floor$floorno/floor");
        $hideName = array('.','..','.DS_Store');
         if (file_exists($floorpaths)) {
             $_floorpaths = scandir($floorpaths);
         foreach ($_floorpaths as &$value) {
             if(!in_array($value, $hideName)){
                 $arr['floor']['docs'][] = $value;
                 $arr['floor']['url'][] = $floorpaths."/".$value;
                 $arr['floor']['info'][] = pathinfo($floorpaths."/".$value, PATHINFO_EXTENSION);
             }
         }
         } else {
            $arr['floor'] = [];
         }



         $unitname = $_units->unit_name;
            $floorno = Floor::where('property_id',$property_id)->where('floor_id',$floor_id)->pluck('floor_no')->first();
            $pathunits =  public_path("/property/$propertyname/units/floor$floorno/$unitname");
            $hideName = array('.','..','.DS_Store');
             if (file_exists($pathunits)) {
                 $unitprop = scandir($pathunits);
             foreach ($unitprop as &$value) {
                 if(!in_array($value, $hideName)){
                     $arr['unit']['docs'][] = $value;
                     $arr['unit']['url'][] = $pathunits."/".$value;
                     $arr['unit']['info'][] = pathinfo($pathunits."/".$value, PATHINFO_EXTENSION);

                 }
             }
             } else {
                $arr['unit']=[];

             }

  return $arr;





   }



    function _invgetpath_org(Request $request,$project_id){
        $project = Project::where('project_id',$project_id)->first();
        $property_id = $project->property_id;
        $unit_id = $project->unit_id;
    
    
        $propertyname = Properties::where('property_id',$property_id)->pluck('property_name')->first();
        $prop =  public_path("/property/$propertyname/property");
        $hideName = array('.','..','.DS_Store');
       $arr = [];
        if (file_exists($prop)) {
            $prope = scandir($prop);
        foreach ($prope as &$value) {
            if(!in_array($value, $hideName)){
                $arr['property'][$propertyname]['docs'][] = $value;
                $arr['property'][$propertyname]['url'][] = $prop."/".$value;
                $arr['property'][$propertyname]['info'][] = pathinfo($prop."/".$value, PATHINFO_EXTENSION);
    
            }
        }
        }
    
    
        $_units = Units::where(['property_id'=>$property_id,'unit_id'=>$unit_id])->pluck('floor_id')->toArray();
    
        $floorno = Floor::where(['property_id'=>$property_id,'floor_id'=> $_units])->select('floor_id','floor_no')->get()->toArray();
        $f=0;
        $ccount = count($floorno);
     for($a=1; $a<=$ccount; $a++){
         $floorpath =  public_path("/property/$propertyname/floor$a/floor");
         //floor
         if (file_exists($floorpath)) {
             $floor = scandir($floorpath);
                 foreach ($floor as &$value) {
                             if(!in_array($value, $hideName)){
                                 $arr['floor']["floor$a"]['docs'][] = $value;
                                 $arr['floor']["floor$a"]['url'][] = $floorpath."/".$value;
                                 $arr['floor']["floor$a"]['info'][] = pathinfo($floorpath."/".$value, PATHINFO_EXTENSION);
    
                             }
                         }
    
                          //units
    
                          $flid = $floorno[$f]['floor_id'] ?? 0;
    
              $_units = Units::where(['property_id'=>$property_id,'floor_id'=>$flid])->pluck('unit_name')->toArray();
    
    
             foreach ($_units as $v) {
                 $floornos = Floor::where('floor_id',$flid)->pluck('floor_no')->first();
    
                 $unitpath = public_path("/property/$propertyname/units/floor$floornos/$v");
                 if (file_exists($unitpath)) {
                     $unitdir = scandir($unitpath);
                     foreach ($unitdir as &$values) {
                         if(!in_array($values, $hideName)){
                             $arr['units']["floor$a"][$v]['docs'][] = $values;
                           //  $arr['units']["floor$a"][$v]['url'][] = $unitpath;
                             $arr['units']["floor$a"][$v]['url'][] = $unitpath."/".$values;
                             $arr['units']["floor$a"][$v]['info'][] = pathinfo($unitpath."/".$values, PATHINFO_EXTENSION);
    
                         }
                     }
                 }
             }
             }
             $f++;
     }
    return $arr;
    
    
    
       }

       
       
    // public function propertyfileupload(Request $request){

    //     $path = public_path('/property');
    //     if(!File::isDirectory($path)){
    //         File::makeDirectory($path, 0777, true, true);
    //     }
    // }



   function investorgetpath(Request $request,$property_id,$floor_id,$unit_id){

    $propertyname = Properties::where('property_id',$property_id)->pluck('property_name')->first();
    $prop =  public_path("/property/$propertyname/property");
    $hideName = array('.','..','.DS_Store');
   $arr = [];
    if (file_exists($prop)) {
        $prope = scandir($prop);
    foreach ($prope as &$value) {
        if(!in_array($value, $hideName)){
            $arr['property']['docs'][] = $value;
            $arr['property']['url'][] = $prop."/".$value;
            $arr['property']['info'][] = pathinfo($prop."/".$value, PATHINFO_EXTENSION);

        }
    }
    }

    if($floor_id != "0"){
        $floorno = Floor::where('property_id',$property_id)->where('floor_id',$floor_id)->pluck('floor_no')->first();
        $floorpaths =  public_path("/property/$propertyname/floor$floorno/floor");
        $hideName = array('.','..','.DS_Store');
         if (file_exists($floorpaths)) {
             $_floorpaths = scandir($floorpaths);
         foreach ($_floorpaths as &$value) {
             if(!in_array($value, $hideName)){
                 $arr['floor']['docs'][] = $value;
                 $arr['floor']['url'][] = $floorpaths."/".$value;
                 $arr['floor']['info'][] = pathinfo($floorpaths."/".$value, PATHINFO_EXTENSION);

             }
         }
         } else {

            $arr['floor'] = [];

         }
    }

    // return $unit_id;

        if($unit_id != "0"){

            $floorno = Floor::where('property_id',$property_id)->where('floor_id',$floor_id)->pluck('floor_no')->first();
            $pathunits =  public_path("/property/$propertyname/units/floor$floorno/$unit_id");
            $hideName = array('.','..','.DS_Store');
             if (file_exists($pathunits)) {
                 $unitprop = scandir($pathunits);
             foreach ($unitprop as &$value) {
                 if(!in_array($value, $hideName)){
                     $arr['unit']['docs'][] = $value;
                     $arr['unit']['url'][] = $pathunits."/".$value;
                     $arr['unit']['info'][] = pathinfo($pathunits."/".$value, PATHINFO_EXTENSION);

                 }
             }
             } else {
                $arr['unit']=[];

             }
        }
  return $arr;

   }


    function investorgetpath123(Request $request,$property_id){
        $propertyname = Properties::where('property_id',$property_id)->pluck('property_name')->first();
        $prop =  public_path("/property/$propertyname/property");
        $hideName = array('.','..','.DS_Store');
       $arr = [];
        if (file_exists($prop)) {
            $prope = scandir($prop);
        foreach ($prope as &$value) {
            if(!in_array($value, $hideName)){
                $arr['property'][$propertyname]['docs'][] = $value;
                $arr['property'][$propertyname]['url'][] = $prop."/".$value;
                $arr['property'][$propertyname]['info'][] = pathinfo($prop."/".$value, PATHINFO_EXTENSION);

            }
        }
        }



        $floorno = Floor::where('property_id',$property_id)->select('floor_id','floor_no')->get()->toArray();
           $f=0;
           $ccount = count($floorno);
        for($a=1; $a<=$ccount; $a++){
            $floorpath =  public_path("/property/$propertyname/floor$a/floor");
            //floor
            if (file_exists($floorpath)) {
                $floor = scandir($floorpath);
                    foreach ($floor as &$value) {
                                if(!in_array($value, $hideName)){
                                    $arr['floor']["floor$a"]['docs'][] = $value;
                                    $arr['floor']["floor$a"]['url'][] = $floorpath."/".$value;
                                    $arr['floor']["floor$a"]['info'][] = pathinfo($floorpath."/".$value, PATHINFO_EXTENSION);

                                }
                            }

                             //units

                             $flid = $floorno[$f]['floor_id'] ?? 0;

                 $_units = Units::where(['property_id'=>$property_id,'floor_id'=>$flid])->pluck('unit_name')->toArray();


                foreach ($_units as $v) {
                    $floornos = Floor::where('floor_id',$flid)->pluck('floor_no')->first();

                    $unitpath = public_path("/property/$propertyname/units/floor$floornos/$v");
                    if (file_exists($unitpath)) {
                        $unitdir = scandir($unitpath);
                        foreach ($unitdir as &$values) {
                            if(!in_array($values, $hideName)){
                                $arr['units']["floor$a"][$v]['docs'][] = $values;
                              //  $arr['units']["floor$a"][$v]['url'][] = $unitpath;
                                $arr['units']["floor$a"][$v]['url'][] = $unitpath."/".$values;
                                $arr['units']["floor$a"][$v]['info'][] = pathinfo($unitpath."/".$values, PATHINFO_EXTENSION);

                            }
                        }
                    }


                }


                }

                $f++;

        }



return $arr;

    }

    function downloadbob(Request $request){
                    $file = $request->path;
                    $docs = $request->docs;
                    $info = $request->info;

            $headers = [
                'Content-Type' => "application/$info",
            ];
            return response()->download($file, $docs, $headers);
    }



    function getpath(Request $request,$project_id,$property_id){
        $project = Project::where('project_id',$project_id)->first();
        $property_id = $project->property_id;
        $propertyname = Properties::where('property_id',$property_id)->pluck('property_name')->first();
        $unit_id     = $project->unit_id;
        $unit = Units::where('unit_id',$unit_id)->first();
        $unitname = $unit->unit_name;
        $floor_id = $unit->floor_id;
        $floorno = Floor::where('floor_id',$floor_id)->pluck('floor_no')->first();

        $arr = [
            'property' => public_path("/property/$propertyname/property"),
            'floor'    => public_path("/property/$propertyname/floor$floorno/floor"),
            'units'    => public_path("/property/$propertyname/units/$unitname")
        ];


        $property = scandir($arr['property']);
        $floor = scandir($arr['floor']);
        $units = scandir($arr['units']);



        $hideName = array('.','..','.DS_Store');
        foreach ($property as &$value) {
            if(!in_array($value, $hideName)){
                $f['property'][] = $value;
            }
        }

        foreach ($floor as &$value) {
            if(!in_array($value, $hideName)){
                $f['floor'][] = $value;
            }
        }

        foreach ($units as &$value) {
            if(!in_array($value, $hideName)){
                $f['units'][] = $value;
            }
        }

        $arr1 = [

            'property' => "https://rdd-qa.tamdeenmalls.com/api/public/property/$propertyname/property",
            'floor'    => "https://rdd-qa.tamdeenmalls.com/api/public/property/$propertyname/floor$floorno/floor",
            'units'    => "https://rdd-qa.tamdeenmalls.com/api/public/property/$propertyname/units/$unitname"
        ];


        $senddata['file'] = $f;
        $senddata['path'] = $arr1;


        return $senddata;

    }


    function propertyfileupload(Request $request){

        $propertyname = $request->input('propertyname');
        $count = $request->input('count');
        $_name = $request->input('_name');

        switch ($_name)
            {
                case 'property':
                    $path = public_path("/property/$propertyname/property");
                    break;
                case 'floor':
                    $propery_id         = $request->input('propertyid');
                    $floor_id           = $request->input('_floor_id');
                    $floorno           = $request->input('floorno');
                    $propertyname       = Properties::where('property_id',$propery_id)->pluck('property_name')->first();
                   // $floor_name         = Floor::where('floor_id',$floor_id)->pluck('floor_name')->first();
                     $path = public_path("/property/$propertyname/floor$floorno/floor");
                    break;
                case 'units':
                    $propery_id         = $request->input('propertyid');
                    $floor_id           = $request->input('_floor_id');
                    $unit_name           = $request->input('unit_name');
                    $propertyname       = Properties::where('property_id',$propery_id)->pluck('property_name')->first();
                    $floorno         = Floor::where('floor_id',$floor_id)->pluck('floor_no')->first();
                 //   $path = public_path("/property/$propertyname/floor$floorno/units/$unit_name");
                      $path = public_path("/property/$propertyname/units/floor$floorno/$unit_name");

                    break;
             }

                if(!File::isDirectory($path)){
                    File::makeDirectory($path, 0777, true, true);
                    chmod($path,0777);
                        for($a=0;$a<$count;$a++){
                            $file = "file$a";
                            $orignalName = $request->$file->getClientOriginalName();
                            $fileName = time().'.'.$orignalName.'.'.$request->$file->extension();
                            $request->$file->move($path, $fileName);
                        }
                } else {
                    chmod($path,0777);
                    for($a=0;$a<$count;$a++){
                        $file = "file$a";
                        $orignalName = $request->$file->getClientOriginalName();
                        $fileName = time().'.'.$orignalName.'.'.$request->$file->extension();
                        $request->$file->move($path, $fileName);
                    }
                }





        echo json_encode($path);
    }



 
 
     function getfloor_by_property(Request $request,$property_id){
        //$property_id = $request->property_id;
         $floor_name         = Floor::where('property_id',$property_id)->get();

         foreach($floor_name as $k=>$v){
            if($v->floor_name){
             $floor_name[$k]['floor_no'] = $v->floor_name;
            } else {
             $floor_name[$k]['floor_no'] = "Floor $v->floor_no";
            }
         }

         
         return response()->json(["result" => $floor_name ],200);
     }
 
     function getunit_by_floor(Request $request , $floor_id , $property_id){
         $units         = Units::where('floor_id',$floor_id)->where('property_id',$property_id)->get();
         return response()->json(["result" => $units ],200);
     }
 


    function propertyfileupload123(Request $request){

        $propertyname = $request->input('propertyname');
        $count = $request->input('count');
        $_name = $request->input('_name');

        switch ($_name)
            {
                case 'property':
                    $path = public_path("/property/$propertyname");
                    break;
                case 'floor':
                    $propery_id         = $request->input('propertyid');
                    $floor_id           = $request->input('_floor_id');
                    $floorno           = $request->input('floorno');
                    $propertyname       = Properties::where('property_id',$propery_id)->pluck('property_name')->first();
                   // $floor_name         = Floor::where('floor_id',$floor_id)->pluck('floor_name')->first();
                     $path = public_path("/property/$propertyname/floor$floorno");
                    break;
                case 'units':
                    $propery_id         = $request->input('propertyid');
                    $floor_id           = $request->input('_floor_id');
                    $unit_name           = $request->input('unit_name');
                    $propertyname       = Properties::where('property_id',$propery_id)->pluck('property_name')->first();
                    $floorno         = Floor::where('floor_id',$floor_id)->pluck('floor_no')->first();
                //    $path = public_path("/property/$propertyname/floor$floorno/units/$unit_name");
                    $path = public_path("/property/$propertyname/units/floor$floorno/$unit_name");

                    break;
             }

                if(!File::isDirectory($path)){
                    File::makeDirectory($path, 0777, true, true);
                    chmod($path,0777);
                        for($a=0;$a<$count;$a++){
                            $file = "file$a";
                            $orignalName = $request->$file->getClientOriginalName();
                            $fileName = time().'.'.$orignalName.'.'.$request->$file->extension();
                            $request->$file->move($path, $fileName);
                        }
                }





        echo json_encode($path);
    }



    function multipledocUpload(Request $request)
    {


        if(isset($request->project_id) && $request->input('project_id')){

            $count = $request->input('count');
            $project = Project::where('project_id',$request->project_id)->first();
             $dbcount =count(json_decode($project->fif_upload_path));


            $property_id = $project->property_id;
            $propertyname = Properties::where('property_id',$property_id)->pluck('property_name')->first();
            $renameimage = $propertyname."-".$project->investor_brand;
           $path =  $request->input('docpath');
           $workspace_path = $path."/fif_docs/";

                $dbcount++;
                        chmod($workspace_path,0777);
                        $p = [];
                        for($a=0;$a<$count;$a++){
                            $file = "file$a";
                           // $orignalName = $request->$file->getClientOriginalName();
                           // $fileName = time().'.'.$orignalName.'.'.$request->$file->extension();
                           $to_doc_path =  $workspace_path."".$renameimage."-"."v0$dbcount";
                            $request->$file->move($workspace_path, $to_doc_path);
                            $dbcount++;
                            $p[] = $to_doc_path;
                        }


            return response()->json(['response'=>$p], 200);

        }

        

       $file_path = [];
       $file_path = $this->uploadFile($request,0,$file_path);
       return response()->json(['response'=>$file_path], 200);
    
    }
    function uploadFile(Request $request,$index,$file_path)
    {
        if($request->hasfile('file'.$index))
        {
            $docPath = explode("/uploads",$request->input('docpath'));
            $fileIndex = "file".$index;
            $orignalName = $request->$fileIndex->getClientOriginalName();
            if(!File::isDirectory(trim($request->input('docpath'),'"'))){
            File::makeDirectory($request->input('docpath'), 0777, true, true);
            chmod($request->input('docpath'),0777);
            }
            $request->$fileIndex->move(trim($request->input('docpath'),'"'), $orignalName); 
            $path = trim($request->input('docpath'),'"')."/".$orignalName;
            chmod($path,0777);
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

    function generateUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'project_id' => 'required',
            'file' => 'required|mimes:pdf,docx,png,jpg,jpeg',
            'docpath' => 'required',
            'type' => 'required'
       ]);
       if ($validator->fails()) { 
           return response()->json(['error'=>$validator->errors()], 401);            
       }
       $updated_at = date('Y-m-d H:i:s');
       $type=$request->input('type');
       //type 1-HOC,2-FCC,3-FDR
       if($type==1)
       {
        $destination_path = trim($request->input('docpath'))."/hoc/uploaded";
        if(!File::isDirectory($destination_path)){
            File::makeDirectory($destination_path, 0777, true, true);
            }
        $orignalName = $request->file->getClientOriginalName();  

        $fileName = $orignalName;  
   
        $request->file->move($destination_path, $fileName);
 
        $path = $destination_path."/".$orignalName;
        $hocEntrycount = Handovercertificate::where('project_id',$request->input('project_id'))->where('isDeleted',0)->count();
        if($hocEntrycount==0)
        {
            $hoc = new Handovercertificate();
            $hoc->project_id = $request->input('project_id');
            $hoc->doc_type = 'Handover Certificate';
            $hoc->created_at = $created_at;
            $hoc->updated_at = $updated_at;
            $hoc_entry = $hoc->save();
        }
        Handovercertificate::where('project_id',$request->input('project_id'))->where('isDeleted',0)->update(
            array(
                "updated_at"=>$updated_at,
                "generated_path"=>$path
            )
        );
       }

       if($type==2)
       {
        $destination_path = trim($request->input('docpath'))."/uploaded";
        if(!File::isDirectory($destination_path)){
            File::makeDirectory($destination_path, 0777, true, true);
            }
        $orignalName = $request->file->getClientOriginalName();  

        $fileName = $orignalName;  
   
        $request->file->move($destination_path, $fileName);
 
        $path = $destination_path."/".$orignalName;

        FitoutCompletionCertificates::where('project_id',$request->input('project_id'))->where('isDeleted',0)->update(
            array(
                "updated_at"=>$updated_at,
                "generated_path"=>$path
            )
        );
       }

       if($type==3)
       {
        $destination_path = trim($request->input('docpath'))."/uploaded";
        if(!File::isDirectory($destination_path)){
            File::makeDirectory($destination_path, 0777, true, true);
            }
        $orignalName = $request->file->getClientOriginalName();  
        $fileName = $orignalName;  
        $request->file->move($destination_path, $fileName);
        $path = $destination_path."/".$orignalName;
        FitoutDepositrefund::where('project_id',$request->input('project_id'))->where('isDeleted',0)->update(array(
            "isdrfGenerated" => 1,
            "generated_path"=>$path,
            "updated_at" => $updated_at
        ));
       }
       $data = array ("message" => 'File Uploaded successfully',"file_name"=>$orignalName,"file_path" => $path );
       $response = Response::json($data,200);
       return $response;
    }

}
