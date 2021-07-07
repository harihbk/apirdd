<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Project Work Permit</title>
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
        <h3>Dear {{$rdd_manager}},</h3>
        <div>Kindly review the {{$permit_type}} requuest for the above mentioned unit as detailed in the attached form/below.</div><br/>
        <table>
        <tr>
            <td>Start Date: </td>
            <td>{{$start_date}}</td>
        </tr>
        <tr>
            <td>End Date: </td>
            <td>{{$end_date}}</td>
        </tr>
        <tr>
            <td>Company Name: </td>
            <td>{{$company_name}}</td>
        </tr>
        <tr>
            <td>Contact Name: </td>
            <td>{{$contact_name}}</td>
        </tr>
        <tr>
            <td>Contact Number: </td>
            <td>{{$contact_no}}</td>
        </tr>
        </table><br/>
        <div>Many thanks.</div><br />
        <div>Regards,</div>
        <div>{{$tenant_name}} {{$tenant_last_name}}</div>
    </body>
</html>