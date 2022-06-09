<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Investor Credentials</title>
    </head>
    <body>
        <h3>Dear {{$tenant_name}} {{$tenant_last_name}},</h3>
        <p>Your details has been registered as Investor.Kindly find below the details and crendentials for your Investor login.</p>
        <table>
        <tr>
            <td>Name: </td>
            <td>{{$tenant_name}}</td>
        </tr>
        <tr>
            <td>Last Name: </td>
            <td>{{$tenant_last_name}}</td>
        </tr>
        <tr>
            <td>Mobile: </td>
            <td>{{$tenant_mobile}}</td>
        </tr>
        <tr>
            <td>Start Date: </td>
            <td>{{$start_date}}</td>
        </tr>
        <tr>
            <td>End Date: </td>
            <td>{{$end_date}}</td>
        </tr>
        </table><br/>
        <p>Your system generated Password</p>
        <p>Password: <b>{{ $temp_pass }}</b></p><br/> 
         <p>For Logging in as Investors <a target="_blank" href="https://rdd.tamdeenmalls.com">click here</a></p>
        <p>Regards,</p>
        <p>RDD Team</p>
    </body>
</html>