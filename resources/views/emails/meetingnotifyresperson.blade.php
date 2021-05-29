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
        <h3>Dear Team,</h3>
        <p>{{ $meeting_topic }} for Project {{ $project_name }} hase been approved on the scheduled time.</p>
        <table>
        <tr>
            <td>Meeting Date: </td>
            <td>{{$meeting_date}}</td>
        </tr>
        <tr>
            <td>Duration: </td>
            <td>{{$meeting_start_time}} - {{$meeting_end_time}}</td>
        </tr>
        </table>
        <p>Regards,</p>
        <p>RDD Team</p>
    </body>
</html>