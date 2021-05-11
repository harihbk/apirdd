<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Project Kicoff Details</title>
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
        <p>Retail Design and Delivery team welcomes {{$investor_brand}} to {{$property_name}}</p>
        <div>
        <p>Our aim is to work closely with you and your team to ensure a smooth approval process of the design submissions and provide the full support and coordination throughout the fitout phase to ensure the delivery of a high quality store.</p>
        </div>
        <p>Listed below are the dates agreed in the Investment Agreement:</p>
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
        <div>
        Your main point of contact will be {{$mem_name}} {{$mem_last_name}}, {{$email}}, who will lead and discuss in detail the Design & Fitout process and clarify any queries you may have along the way.
        We look forward to working with you and your team to ensure a successful opening of the store.
        </div>
    </body>
</html>