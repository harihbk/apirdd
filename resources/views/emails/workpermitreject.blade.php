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
        <h3>Dear {{$investorname}},</h3>
        <div>Please note that the requested {{ $permit_type }} on {{ $date }} was rejected. Please see the comments for more clarification.
        </div><br/>
        @if (isset($description))
           <div>
               <h4>Rejection comments:</h4><p>{{ $description }}</p>

           </div>
       @endif

        <div>Regards,</div>
      <br>
        <div><i>This is system generated mail, Please do not reply.</i> </div>

    </body>
</html>
