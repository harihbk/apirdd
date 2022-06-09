<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Project Design Submission - {{ $doc_header }}</title>
    </head>  
    <body>
        @if($type==1)
        <h3>Dear {{$tenant_name}} {{$tenant_last_name}},</h3>
        <p>This is to confirm receiving the drawings you have submitted on {{$pre_date}} as listed below:</p>
        @foreach ($uploaded_doc_titles as $doc)
            <div>{{ $loop->index+1 }}.{{ $doc }}</div>
        @endforeach
        <p>The submitted drawings will be  reviewed and we shall get back to you with our comments very soon.</p><br/>
        @endif
        @if($type==2)
        <h3>Dear {{$rdd_manager_name}},</h3>
        <p>Kindly find the link below for the new drawings submission done by the above mentioned unit for your review and comments:</p>
         @foreach ($uploaded_doc_titles as $doc)
            <div>{{ $loop->index+1 }}.{{ $doc }}</div>
        @endforeach
        <p><a target="_blank" href="https://rdd.tamdeenmalls.com">click here to Follow!</a></p>
        @endif
        @if($type==3)
        <h3>Dear {{$tenant_name}} {{$tenant_last_name}},</h3>
        <p>Please find the below link for our comments and approval status on the drawings you have submitted so far for your reference and action please.</p>
        <p>To access your design submission, please use the below</p>
        <p><a target="_blank" href="https://rdd.tamdeenmalls.com">click here to Follow!</a></p>
        @endif
        <p>Regards,</p>
        <br>
        <p>RDD Team</p>
        <div>This is system generated mail, Please do not reply.  </div>
    </body>
</html>
