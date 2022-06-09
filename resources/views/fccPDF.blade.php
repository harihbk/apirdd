<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" >
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate</title>
    <style>
        body{
            font-family: sans-serif;
        }
        .header {
            text-transform: uppercase;
            border: 1px solid #ccc;
            background: linear-gradient(to bottom, #fff, #dedced);
            padding: 10px;
            text-align: center;
            color: #d05677;
            font-weight: bold;
            margin-bottom: 31px;
        }
        span.small-text {
            font-size: 12px;
            padding-left: 10px;
        }
        td.th_heading {
            vertical-align: bottom;
            font-size:12px;
        }
        th.attendes_title {
            text-transform: uppercase;
            text-align: left;
            font-weight: bold;
            padding-bottom: 4px;
        }
        .header_form_data {
            text-align: left;
            padding-bottom: 20px;
            font-weight: 800;
            padding-top: 21px;
        }
        span.smalltext {
            font-size: 13px;
            line-height: 16px;
            font-weight: 100;
        }
        .value{
            font-size: 12px;
        }
        table {
            font-size: 10px;
        }
    </style>
</head>
<body class="">

        <table class="cert_table" style="width: 100%;" id="fullpage">
            <td style="width: 20%;">
           
                            <img width="50" height="50" class="logo-img" src = "images/tamdeen-logo.png" alt="no_image">
                      
                <!-- <img src="images/tamdeen-logo.png" alt=""> -->
            </td>
            <td>
                <div class="header header1">
                    fitout completion certificate
                </div>
            </td>
            <td  style="width: 20%;    text-align: center;">
                        <img width="50" height="50" class="logo-img" src ="{{ $property_name }}" alt="no_image">
            </td>
        </table>

    <table style="table-layout: fixed;width: 100%;border-bottom: 4px groove #000000a6;padding-bottom: 8px;margin-bottom: 8px;">
        <tr>
            <td class="th_heading">
                Premises Number(s)
            </td>
            <td class="value value_underline" style="border-bottom: 1px solid #000;">
            {{$unit_name}}
            </td>
            <td class="th_heading">
                Premises Location(s)
            </td>
            <td class="value value_underline" style="border-bottom: 1px solid #000;">
            Main Level
            </td>
            <td class="th_heading">
                Concept Zone<br>
                <span class="small-text">( if applicable )</span>
            </td>
            <td class="value value_underline" style="border-bottom: 1px solid #000;">

            </td>
        </tr>

        <tr>
            <td class="th_heading">
                Investor Brand name
            </td>
            <td class="value value_underline" style="border-bottom: 1px solid #000;">
            {{$investor_brand}}
            </td>
            <td class="th_heading">
                Inspection Date
            </td>
            <td class="value value_underline" style="border-bottom: 1px solid #000;">

            </td>
            <td class="th_heading" style="padding-top: 6px;">
                Fitout Completion<br>
                <span class="small-text"> Date(Actual)</span>
            </td>
            <td class="value value_underline" style="border-bottom: 1px solid #000;">

            </td>
        </tr>

        <tr>
            <td class="th_heading" style="padding-top: 13px;">
               Invester Company Name
            </td>
            <td class="value value_underline"  colspan="5" style="border-bottom: 1px solid #000;">
            {{$company_name}}
            </td>
        </tr>
    </table>

    <table class="attendees_table" style="width: 100%;border-bottom: 4px groove #000000a6;padding-bottom: 8px">
        <tr>
            <th class="attendes_title" >
                attendees
            </th>
        </tr>
        <tr >
            <td class="th_heading" style="width: 220px;vertical-align: bottom;padding-top: 15px;">
                RDD Project Manager
            </td>
            <td class="value value_underline"  colspan="5" style="border-bottom: 1px solid #000;">

            </td>
        </tr>
        <tr>
            <td class="th_heading" style="width: 220px;vertical-align: bottom;padding-top: 15px;">
                TMM MEP Consultant
            </td>
            <td class="value value_underline"  colspan="5" style="border-bottom: 1px solid #000;">

            </td>
        </tr>
        <tr>
            <td class="th_heading" style="width: 220px; padding-top: 15px;">
                Invester's Authorized Signatory
            </td>
            <td class="value value_underline"  colspan="5" style="border-bottom: 1px solid #000;">

            </td>
        </tr>
        <tr>
            <td class="th_heading" style="width: 220px; padding-top: 15px;">
                Investor's Shop fitter
            </td>
            <td class="value value_underline"  colspan="5" style="border-bottom: 1px solid #000;">
            </td>
        </tr>
    </table>

    <div class="form_data">
        <div class="header_form_data">
            Inspection Checklist <span class="smalltext">( in strict compilance with the investor Fitout & Design Guidelines and the aproved Detailed Design Drawings )</span>
        </div>

        <table class="formdata_table" style="width: 100%;border-collapse: collapse;" border="1" cellspacing="0" cellpadding="8">
            <tr>
                <th class="th_title" style="width: 23%;">
                    Architectural
                </th>
                <th class="th_title" style="width: 23%;">
                    Comments/Defects
                </th>
                <th class="th_title" style="width: 23%;">
                    Action
                </th>
                <th class="th_title" style="width: 23%;">
                    Finish Date<br>
                    ( By Invester )
                </th>
            </tr>

            <tr>
                <td class="title_left" style="width: 23%;">
                    Shopfront
                </td>
                <td class="value td_value" style="width: 23%;">
                    
                </td>
                <td class="value td_value" style="width: 23%;">
                    
                </td>
                <td class="value td_value" style="width: 23%;">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left" style="width: 23%;">
                    Signage
                </td>
                <td class="value td_value" style="width: 23%;">
                   
                </td>
                <td class="value td_value" style="width: 23%;">
                   
                </td>
                <td class="value td_value" style="width: 23%;">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left" style="width: 23%;">
                    Ceiling
                </td>
                <td class="value td_value" style="width: 23%;">
                   
                </td>
                <td class="value td_value" style="width: 23%;">
                   
                </td>
                <td class="value td_value" style="width: 23%;">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left" style="width: 23%;">
                    Walls
                </td>
                <td class="value td_value" style="width: 23%;">
                   
                </td>
                <td class="value td_value" style="width: 23%;">
                   
                </td>
                <td class="value td_value" style="width: 23%;">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left" style="width: 23%;">
                    Floors & Waterproofing
                </td>
                <td class="value td_value" style="width: 23%;">
                   
                </td>
                <td class="value td_value" style="width: 23%;">
                   
                </td>
                <td class="value td_value" style="width: 23%;">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left" style="width: 23%;">
                    Shop Fittings
                </td>
                <td class="value td_value" style="width: 23%;">
                   
                </td>
                <td class="value td_value" style="width: 23%;">
                   
                </td>
                <td class="value td_value" style="width: 23%;">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left" style="width: 23%;">
                    Exit & Emergency Lighting
                </td>
                <td class="value td_value" style="width: 23%;">
                   
                </td>
                <td class="value td_value" style="width: 23%;">
                   
                </td>
                <td class="value td_value" style="width: 23%;">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left" style="width: 23%;">
                    Fire Exit Doors<br> ( if applicable )
                </td>
                <td class="value td_value" style="width: 23%;">
                   
                </td>
                <td class="value td_value" style="width: 23%;">
                   
                </td>
                <td class="value td_value" style="width: 23%;">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left" style="width: 23%;">
                    Other
                </td>
                <td class="value td_value" style="width: 23%;">
                   
                </td>
                <td class="value td_value" style="width: 23%;">
                   
                </td>
                <td class="value td_value" style="width: 23%;">
                   
                </td>
            </tr>

            <!-- heading  -->
            <tr>
                <th class="th_title" style="width:25%">
                    MEP
                </th>
                <th class="th_title" style="width:25%">
                    Comments/Defects
                </th>
                <th class="th_title" style="width:25%">
                    Action
                </th>
                <th class="th_title" style="width:25%">
                    Finish Date<br>
                    ( By Invester )
                </th>
            </tr>

            <tr>
                <td class="title_left">
                    MDB Installation & Electricity Connected
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left">
                    Electromechanical meter(s) <br>
                    ( Electricity & Water )
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
            </tr>


            <tr>
                <td class="title_left">
                   Airconditiong ( AHU's / Fan Coil Units )
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left">
                   Thermostats
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
            </tr>


  
         

            <tr>
                <td class="title_left">
                   Fire Fighting Detectors & Smoke Alarms, Extinguishers
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
            </tr>

            <tr>
                <th class="th_title" style="width:23%">
                    MEP
                </th>
                <th class="th_title" style="width:23%">
                    Comments/Defects
                </th>
                <th class="th_title" style="width:23%">
                    Action
                </th>
                <th class="th_title" style="width:23%">
                    Finish Date<br>
                    ( By Invester )
                </th>
            </tr>

            <tr>
                <td class="title_left">
                   Lighting Layout
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left">
                   Celling Access Panels
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left">
                  Gas and detector system installed and connected
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left">
                  Waterproofing, drainage and plumbing
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left">
                  Grase Traps and / or slower
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left">
                  Kitchen & Toilet Exhust
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
                <td class="value td_value" style="width:23%">
                   
                </td>
            </tr>
        </table>

        <table style="width: 100%;margin-top: 20px;margin-bottom: 10px;">
            <td>
               <span style="font-weight: bold;"> Fitout Completion Date Compilance</span> <span>(by Investor)</span>
            </td>
            <td style="width: 280px; text-align: center;font-weight: bold;">
                YES / NO
            </td>
        </table>
        
        <table  style="width: 100%;border-collapse: collapse;" border="1" cellspacing="0" cellpadding="8">
            <tr>
                <td>
                    Payment of all / any outstanding monies owed to TMM and / or Owner
                </td>
                <td style="width: 280px;">

                </td>
            </tr>

            <tr>
                <td>
                   Hard Copy submission of all applicable fitout related approvals (Kuwait Municipalty, KFD, etc)
                </td>
                <td style="width: 280px;">

                </td>
            </tr>

            <tr>
                <td>
                   Hard and soft copy of all 'as built' drawings
                </td>
                <td style="width: 280px;">

                </td>
            </tr>

            <tr>
                <td>
                   Investor or Investor's Authorised Signatory
                </td>
                <td style="width: 280px;">

                </td>
            </tr>
        </table>

        <table  style="width: 100%;border-collapse: collapse;margin-top: 30px" border="1" cellspacing="0" cellpadding="8">
            <tr>
                <th style="width: 33%;">Authorization</th>
                <th>Name</th>
                <th style="width: 33%;">Signature</th>
                <th style="width: 33%;">Date</th>
            </tr>

            <tr>
                <td style="width: 33%;">RDD Project Manager</td>
                <td style="width: 33%;"></td>
                <td style="width: 33%;"></td>
                <td style="width: 33%;"></td>
            </tr>
            <tr>
                <td style="width: 33%;">Investor's Authorised Signatory</td>
                <td style="width: 33%;"> </td>
                <td style="width: 33%;"></td>
                <td style="width: 33%;"></td>
            </tr>
            <tr>
                <td style="width: 33%;">Investor's Shop filter</td>
                <td style="width: 33%;"> </td>
                <td style="width: 33%;"></td>
                <td style="width: 33%;"></td>
            </tr>
            <tr>
                <td style="width: 33%;">Owner's Project Manager</td>
                <td style="width: 33%;"> </td>
                <td style="width: 33%;"></td>
                <td style="width: 33%;"></td>
            </tr>
        </table>
    </div>
    
 
</body>
</html>