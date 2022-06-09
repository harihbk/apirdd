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
        <p>Based on the new milestone dates submitted by your team, kindly find below the updated/agreed milestones for your reference, record and future use purpose:</p>
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
            <td>Opening Date: </td>
            <td>{{$store_opening}}</td>
        </tr>
        </table>
        <br/>
        <div>
         Regards,
         <p>RDD Team</p>
        </div>
    </body>
</html>