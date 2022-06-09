<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Project Work Permit</title>
        <style>
		body
		{
		 line-height:1.7em;
		 font-size:18px;
		}
        </style>
    </head>
    <body>
        <h3>Dear {{$recipient}} Team,</h3>
        <div>Kindly issue the {{$permit_type}} permit as requested in the attached file.</div><br/>
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
    <td>Contracting Company: </td>
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
<!-- <tr>
    <td>Site Supervisor: </td>
    <td></td>
</tr> -->

</table><br/>
        <div>Many thanks.</div><br />
        <div>Regards,</div>
        <div>{{$mem_name}} {{$mem_last_name}}</div>
    </body>
</html>