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
        <div>Many thanks.</div><br />
        <div>Regards,</div>
        <div>{{$mem_name}} {{$mem_last_name}}</div>
    </body>
</html>