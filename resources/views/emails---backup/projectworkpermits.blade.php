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
        @if($type==1)
        <h3>Dear {{$rdd_manager}},</h3>
        <div>Kindly review the {{$permit_type}} requuest for the above mentioned unit as detailed in the attached form/below.</div>
        <p><a target="_blank" href="https://rdd.tamdeenmalls.com">click here to Follow!</a></p><br/>
        @endif
        @if($type==2)
        <h3>Dear Operations Team,</h3>
        Kindly issue a {{$permit_type}} for the above mentioned unit as detailed in the attached form/below
        @endif
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
        <tr>
            <td>Description: </td>
            <td>{{$description}}</td>
        </tr>
        
        </table><br/>
        <div>Many thanks.</div><br />
        @if($type==1)
        <div>Regards,</div>
        <div>{{$tenant_name}} {{$tenant_last_name}}</div>
        @endif
        @if($type==2)
        <div>Regards,</div>
        <div>{{$rdd_manager}}</div>
        @endif
    </body>
</html>