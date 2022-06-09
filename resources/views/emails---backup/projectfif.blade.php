<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Project FIF Details</title>
        <style>
        table {
         font-family: arial, sans-serif;
		 font-size:16px;
        }
		table tr td
		{
		  border:1px solid black;
		  text-align:center;
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
        <h3>Dear all,</h3>
        <div>
        <p>With regards to the above mentioned,please find attached UN-PN-FIF for your reference.</p>
        </div>
        <p>Listed below are the dates agreed in the Investment Agreement:</p>
        <table>
        <tr style="font-weight:bolder;">
            <td>CONCEPT SUBMISSION</td>
            <td>DETAILED DESIGN SUBMISSION</td>
            <td>HANDOVER DATE/FITOUT START DATE</td>
            <td>FITOUT COMPLETION DATE</td>
            <td>OPENING DATE</td>
        </tr>
        <tr>
            <td>{{$concept_submission}}</td>
            <td>{{$detailed_design_submission}}</td>
            <td>{{$unit_handover}}</td>
            <td>{{$fitout_completion}}</td>
            <td>{{$store_opening}}</td>
        </tr>
        </table><br/>
        <div><p>Regards,</p></div>
        <div><p>RDD Team.</p></div>
    </body>
</html>