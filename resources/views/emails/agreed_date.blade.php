<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Investor Credentials</title>
    </head>
    <body>
        <h3>Dear {{ $tenant_name }}</h3>
        <p>Based on the new milestone dates submitted by your team, kindly find below the updated/agreed milestones for your reference, record and future use purpose:
        </p>

        <table>

        <tr>
            <td>Concept Submission: </td>
            <td>{{$concept_submission}}</td>
        </tr>
        <tr>
            <td>Detail Design Submission: </td>
            <td>{{$detailed_design_submission}}</td>
        </tr>
        {{-- <tr>
            <td>Unit Handover: </td>
            <td>{{$unit_handover}}</td>
        </tr> --}}
        <tr>
            <td>Fitout Completion: </td>
            <td>{{ $fitout_completion}}</td>
        </tr>
        <tr>
            <td>Fitout Start: </td>
            <td>{{ $fitout_start}}</td>
        </tr>

        </table><br/>

        <p>Regards,</p>
        <div><i>This is system generated mail, Please do not reply.</i> </div>

    </body>
</html>
