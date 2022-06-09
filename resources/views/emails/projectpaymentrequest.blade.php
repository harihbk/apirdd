<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Project Schedule Meeting</title>
        <style>
		body
		{
		 line-height:1.7em;
		 font-size:18px;
		}
        </style>
    </head>
    <body>
        <h3>Dear Finance Team,</h3>
        <div>We would like to request a payment update for {{$brand_name}}-{{$property}} on the below</div>
        <div><ul>
        <li>IVR ({{$ivr_amt}}) </li>
        <li>Owners Work ({{$owner_work_amt}})</li>
        <li>fitout_deposit_amt ({{$fitout_deposit_amt}})</li>
        </ul></div>
        <div>Appreciate if the receipt could be provided in case of payment.</div><br/>
        <div>Many thanks.</div><br/>
        <div>Regards,</div>
        <br>
        <div>This is system generated mail, Please do not reply.  </div>
        {{-- <div>{{$mem_name}}</div> --}}
    </body>
</html>
