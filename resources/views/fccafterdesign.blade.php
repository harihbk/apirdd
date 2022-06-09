<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
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
            font-size: 7px;
            padding-left: 10px;
        }
        td.th_heading {
            vertical-align: bottom;
        }
        th.attendes_title {
            text-transform: uppercase;
            text-align: left;
            font-weight: bold;
            padding-bottom: 6px;
        }
        .header_form_data {
            text-align: left;
            padding-bottom: 10px;
            font-weight: 800;
        }
        span.smalltext {
            font-size: 13px;
            line-height: 16px;
            font-weight: 100;
        }
        table {
            font-size: 7px;
        }
        .smalltexts{
            font-size: 7px;
        }
    </style>
</head>
<body class="" style="max-width: 90%;margin: 0 auto">
<script type='text/php'>
        if ( isset($pdf) ) { 
            $font = $fontMetrics->get_font('helvetica', 'bolder');
            $size = 8;
            $y = $pdf->get_height() - 24;
            $x = $pdf->get_width() - 300 - $fontMetrics->get_text_width('1/1', $font, $size);
            $pdf->page_text($x, $y, 'Page {PAGE_NUM} of {PAGE_COUNT}', $font, $size);
        } 
        </script>
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

    <table style="table-layout: fixed;width: 100%;border-bottom: 4px groove #000000a6;padding-bottom: 4px;">
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
            <td class="th_heading" >
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
            <td class="th_heading" style="padding-top: 4px;">
                Fitout Completion<br>
                <span class="small-text"> Date(Actual)</span>
            </td>
            <td class="value value_underline" style="border-bottom: 1px solid #000;">

            </td>
        </tr>


        <tr>
            <td class="th_heading" style="padding-top: 8px;">
               Invester Company Name
            </td>
            <td class="value value_underline"  colspan="5" style="border-bottom: 1px solid #000;">
            {{$company_name}}
            </td>
        </tr>
    </table>

    <table class="attendees_table" style="table-layout: fixed;width: 100%;border-bottom: 4px groove #000000a6;    margin-top: 4px;">
        <tbody>
            <tr>
                <th class="attendes_title" >
                    attendees
                </th>
            </tr>
            <tr>
                <td class="th_heading"  >
                    RDD Project Manager
                </td>
                <td class="value "  style="border-bottom: 1px solid #000;">
    
                </td>
            </tr>
            <tr>
                <td class="th_heading" style="width: 220px;vertical-align: bottom;">
                    TMM MEP Consultant
                </td>
                <td class="value value_underline" style="border-bottom: 1px solid #000;">
    
                </td>
            </tr>
            <tr>
                <td class="th_heading" style="width: 220px;">
                    Invester's Authorized Signatory
                </td>
                <td class="value value_underline" style="border-bottom: 1px solid #000;">
    
                </td>
            </tr>
            <tr>
                <td class="" >
                    Investor's Shop fitter
                </td>
                <td class="" style="border-bottom: 1px solid #000;">
                    
                </td>
            </tr>
        </tbody>
       
    </table>    
      
        

    <div class="form_data" style="margin-top:5px">
        <div class="header_form_data">
            Inspection Checklist <span class="smalltext smalltexts" style="font-size:7px">( in strict compilance with the investor Fitout & Design Guidelines and the aproved Detailed Design Drawings )</span>
        </div>

        <table class="formdata_table" style="table-layout: fixed;width: 100%;border-collapse: collapse;" border="1" cellspacing="0" cellpadding="5">
            <tr>
                <th class="th_title" style="width: 300px;">
                    Architectural
                </th>
                <th class="th_title">
                    Comments/Defects
                </th>
                <th class="th_title" style="width: 200px;">
                    Action
                </th>
                <th class="th_title" style="width: 280px;">
                    Finish Date<br>
                    ( By Invester )
                </th>
            </tr>

            <tr>
                <td class="title_left">
                    Shopfront
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left">
                    Signage
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left">
                    Walls
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left">
                    Floors & Waterproofing
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left">
                    Shop Fittings
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left">
                    Exit & Emergency Lighting
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left">
                    Fire Exit Doors<br> ( if applicable )
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left">
                    Other
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
            </tr>

            <!-- heading  -->
            <tr>
                <th class="th_title">
                    MEP
                </th>
                <th class="th_title">
                    Comments/Defects
                </th>
                <th class="th_title">
                    Action
                </th>
                <th class="th_title">
                    Finish Date<br>
                    ( By Invester )
                </th>
            </tr>

            <tr>
                <td class="title_left">
                    MDB Installation & Electricity Connected
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left">
                    Electromechanical meter(s) <br>
                    ( Electricity & Water )
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left">
                   Airconditiong ( AHU's / Fan Coil Units )
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left">
                   Thermostats
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left">
                   Fire Fighting Detectors & Smoke Alarms, Extinguishers
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left">
                   Lighting Layout
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left">
                   Celling Access Panels
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left">
                  Gas and detector system installed and connected
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left">
                  Waterproofing, drainage and plumbing
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left">
                  Grase Traps and / or slower
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
            </tr>

            <tr>
                <td class="title_left">
                  Kitchen & Toilet Exhust
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
                <td class="value td_value">
                   
                </td>
            </tr>
        </table>

        <table style="width: 100%;margin-top: 5px;margin-bottom: 2px;">
            <td>
               <span style="font-weight: bold;" class="smalltexts"> Fitout Completion Date Compilance</span> <span class="smalltexts">(by Investor)</span>
            </td>
            <td style="width: 280px; text-align: center;font-weight: bold;">
                YES / NO
            </td>
        </table>
        
        <table  style="width: 100%;border-collapse: collapse;" border="1" cellspacing="0" cellpadding="5">
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

        <table  style="table-layout: fixed;width: 100%;border-collapse: collapse;margin-top: 4px" border="1" cellspacing="0" cellpadding="5">
            <tr>
                <th style="width: 300px;">Authorization</th>
                <th>Name</th>
                <th style="width: 200px;">Signature</th>
                <th style="width: 280px;">Date</th>
            </tr>

            <tr>
                <td>RDD Project Manager</td>
                <td>Invetor's Authorised Signatory</td>
                <td>Investor's Shop filter</td>
                <td>Owner's Project Manager</td>
            </tr>
        </table>


        <table  style="table-layout: fixed;width: 100%;border-collapse: collapse;margin-top: 5px" border="0" cellspacing="0" cellpadding="5" >
            <tr>
                <th style="width: 300px;text-align:left">Copy to</th>  
            </tr>
            <tr>
                <th style="width: 300px;text-align:left">Chief operating officer</th>
            </tr>
            <tr>
                <th style="width: 300px;text-align:left">General Manager - Leasing</th>
            </tr>
            <tr>
                <th style="width: 300px;text-align:left">General Manager - OPerations & Marketing</th>    
            </tr>
            <tr>
                <th style="width: 300px;text-align:left">General Manager - Retail Design & Delivery</th>
            </tr>
            <tr>
                <th style="width: 300px;text-align:left">Center Manager</th>
            </tr>

           
        </table>
    </div>

    
</body>
</html>