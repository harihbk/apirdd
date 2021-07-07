<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>TMM - Project Meeting-Notification</title>
    </head>
    <body>
    <h3>Dear {{$tenant_name}} {{$tenant_last_name}},</h3>
        @if($meetingType==1)
        <div><p>Trust this find you well,</p></div>
		<div><p>We would like to invite you for an introduction meeting to discuss the store’s Design and fitout process in detail and clarify any queries you may have.</p></div>
		<div><p>We propose on {{$meeting_date}} [ {{$meeting_start_time}} - {{$meeting_end_time}}]</p></div>
		<div><p>Please let us know if it is convenient by accepting the sent invitation, or else contact the project manager for rescheduling.</p></div><br/>
        <div><p>Many thanks.</p></div>
        <div><p>RDD Team.</p></div>
        @endif
        @if($meetingType==2)
        <div><p>We would like to invite you for a handover meeting on site to receive your premises as agreed in the Investment agreement.</p></div>
        <div><p>The meeting shall be on {{$meeting_date}} [ {{$meeting_start_time}} - {{$meeting_end_time}}].</p></div>
		<div><p>Please confirm your/ your representative availability by accepting the invitation, or else contact the project manager for rescheduling.</p></div>
        <div><p>Many thanks.</p></div>
        <div><p>RDD Team.</p></div>
        @endif
        @if($meetingType==3)
        <div><p>Trust this find you well,</p></div>
		<div><p>We would like to invite you/ your contractor for induction meeting to discuss the store’s fitout process in detail along with our Operations department.</p></div>
        <div><p>We propose on {{$meeting_date}} [ {{$meeting_start_time}} - {{$meeting_end_time}}]</p></div>
        <div><p>Please let us know if it is convenient by accepting the sent invitation, or else contact the project manager for rescheduling.</div><br/>
        <div><p>Many thanks.</p></div>
        <div><p>RDD Team.</p></div>
        @endif
		</body>
</html>








