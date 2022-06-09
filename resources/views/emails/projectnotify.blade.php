<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Project Date Update Notification </title>
        <style>
        table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
		 font-size:16px;
        }

        td, th {
        text-align: left;
        padding: 8px;
        }
		body
		{
		 line-height:1.7em;
		 font-size:18px;
		}
        </style>
    </head>
    <body>
        <h3>Dear {{$tenant_name}} {{$tenant_last_name}},</h3>
        <p>This is to notify Milestone dates for {{$investor_brand}} on property {{$property_name}}</p>
        <p>Listed below are the dates revised:</p>
        <table>
        <tr>
            <td>Concept Submission: </td>
            <td>{{$concept_submission}}</td>
        </tr>
        <tr>
            <td>Detail Design Submission: </td>
            <td>{{$detailed_design_submission}}</td>
        </tr>
        <tr>
            <td>Unit Handover: </td>
            <td>{{$unit_handover}}</td>
        </tr>
        <tr>
            <td>Fitout Completion: </td>
            <td>{{$fitout_completion}}</td>
        </tr>
        <tr>
            <td>Opening Day: </td>
            <td>{{$store_opening}}</td>
        </tr>
        </table>
        {{-- <div>
        Your main point of contact will be {{$mem_name}} {{$mem_last_name}}, {{$email}}
        </div> --}}
        <div>
            Regards,
            <br>
            <p>RDD</p>
            <div>This is system generated mail, Please do not reply.  </div>
           </div>
    </body>
</html>
