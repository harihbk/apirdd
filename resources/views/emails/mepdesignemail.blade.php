<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Meeting Notification</title>
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
        <h3>Dear {{$rdd_manager}},</h3>
        <p>Kindly note that we have completed the MEP design review related to the above-mentioned unit. This is for your action please</p>
        <p>To access your design submission, please use the below</p>
        <p><a target="_blank" href="http://rdd.octasite.com/rdd_portal/login">click here to Follow!</a></p>
        <p>Regards,</p>
        <p>RDD Team</p>
    </body>
</html>