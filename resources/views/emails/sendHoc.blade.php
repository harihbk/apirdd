<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Project FCC Report</title>
        <style>
		body
		{
		 line-height:1.7em;
		 font-size:18px;
		}
        </style>
    </head>
    <body>

 


        <h3>Dear {{$tenant_name}},</h3>
        <!-- <div>With reference to the Final inspection meeting that took place on date</div>
        <div>please find the attached signed {{$unit_name}}- Brand Handover Certificate for your reference and use.</div><br/>
         -->
        <div>With reference to the Handover Meeting that took place on {{ $hoc_date }},Please find the attached {{$unit_name}}-{{ $investor_brand }} Handover Certificate for your reference and use</div>
        <br>
        <div>Regards,</div>
        <!-- <div>{{$rdd_manager_name}}</div> -->
        <div>This is system generated mail, Please do not reply.  </div>

    </body>
</html>