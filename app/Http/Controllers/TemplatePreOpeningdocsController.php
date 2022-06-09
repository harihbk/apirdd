<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TemplatePreOpeningdocs;
use Response;
use Validator;


class TemplatePreOpeningdocsController extends Controller
{
    function store(Request $request)
    {
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $validator = Validator::make($request->all(), [ 
            'docs.*.id' => 'required',
            'docs.*.template_id' => 'required', 
            'docs.*.title' => 'required'
        ]);

        $docs = $request->get('docs');
        $docsData = array();

        for($j=0;$j<count($docs);$j++) 
        {
            if($docs[$j]['id']==0)
            {
                $docsData[] = [
                'template_id' => $docs[$j]['template_id'],
                'title' => $docs[$j]['title'],
                "created_at" => $created_at,
                "updated_at" => $updated_at,
                'created_by' => $docs[$j]['user_id']
             ];
            }
            else
            {
                TemplatePreOpeningdocs::where('template_id',$docs[$j]['template_id'])->where('id',$docs[$j]['id'])->update(
                array(
                    'title'=>$docs[$j]['title'],
                    'is_deleted'=>$docs[$j]['is_deleted'],
                    'updated_at'=>$updated_at
                )
                );
            } 
        }

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        if(TemplatePreOpeningdocs::insert($docsData))
        {
            $returnData = TemplatePreOpeningdocs::where('template_id',$docs[0]['template_id'])->get();
            $data = array ("message" => 'Docs Added successfully',"data" => $returnData );
            $response = Response::json($data,200);
            return $response;
        }
    }
    function retrieveList($id)
    {
        $returnData = TemplatePreOpeningdocs::where('template_id',$id)->where('is_deleted',0)->get();
        return $returnData;
    }
}
