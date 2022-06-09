<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Investor Credentials</title>
    </head>
    <body>
        <h3>Dear {{ $to }}</h3>
        <p>Kindly issue a {{ $permit_type }} for the above mentioned unit as detailed below. </p>
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

        <p>Regards,</p>
        <div><i>This is system generated mail, Please do not reply.</i> </div>

    </body>
</html>
