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
        @if($status == 2)
        <h3>Dear {{$inv_name}},</h3>
        <div>Please note that the requested {{ $inspection_name }} is approved.
        </div>
        <br>

       

       <div> Regards,</div>
        <br>
        <div><i>This is system generated mail, Please do not reply.</i> </div>
        @endif

        @if($status==3)
        <h3>Dear {{$inv_name}},</h3>
        <div>Please note that the requested {{ $inspection_name }} is rejected. </div>
        <div>  Please see the comments for more clarification. </div>
        {{ $comment }}
        <br>

        @if (isset($description))
           <div>
               <h4>Rejection comments:</h4><p>{{ $description }}</p>

           </div>
       @endif
       <div> Regards,</div>
        <br>
        <div><i>This is system generated mail, Please do not reply.</i> </div>
        @endif


    </body>
</html>
