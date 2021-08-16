<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Fitout Completion Certificate</title>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
		<style>
            @page
            {
            /* size: A4 portrait; */
            margin: 90px 25px;
            }
            header {
                    position: fixed;
                    top: -70px;
                    left: 0px;
                    right: 0px;
                    height: 50px;
                    clear: both;
                }

                footer {
                    position: fixed; 
                    bottom: -100px; 
                    left: 0px; 
                    right: 0px;
                    height: 50px; 
                    font-size:11px;
                }
                .logo-img
                    {
                        width:40%;
                        height:auto;
                        padding:10px;
                    }
                .header-content
                {
                    text-align:center;
                    font-weight:bolder;
                    color: #ffffff;
                }
                .header-sec
                {
                    background: #bb5064;
                    height:30px;
                }
                .form-body
                {
                    border:1px solid black;
                }
                .td-width
                {
                    font-size:12px;
                    padding-right:10px;
                }
                .sec-1
                {
                    padding-top:10px;
                }
                table>tbody>tr>td>div>span 
                {
                    padding-left:10px;
                }
                dt {
                    float: left;
                    clear: left;
                    width: auto;
                    font-weight:lighter;
                }
        
                dd {
                    margin: 0 0 0 50px;
                    padding: 0 0 0.5em 0;
                    word-wrap:break-word;
                }
                .seperator {
                    border: 3px solid black;
                    }
                .pt-30 {
                padding-top: 30px;
                }
                .pt-20 {
                padding-top: 20px;
                }
                .pt-50 {
                padding-top: 50px;
                }
                .pl-10
                {
                    padding-left: 25px;
                }
                .header-value
                {
                    font-weight:bolder;
                }
                .fs-11
                {
                    font-size:11px;
                }
                .fitout-table tr td,
                .centre-table tr td,
                .finance-table tr td,
                .deposit-table tr td
                  {
                border: 1px solid black;
                }
                .deposit-last
                {
                    background: #bb5064;
                    color:#ffffff;
                }
        </style>
    </head>
    <body>
    <script type='text/php'>
        if ( isset($pdf) ) { 
            $font = $fontMetrics->get_font('helvetica', 'bolder');
            $size = 9;
            $y = $pdf->get_height() - 24;
            $x = $pdf->get_width() - 300 - $fontMetrics->get_text_width('1/1', $font, $size);
            $pdf->page_text($x, $y, 'Page {PAGE_NUM} of {PAGE_COUNT}', $font, $size);
        } 
        </script>
        <header>
                <div style="clear:both; position:relative;">
                    <div style="position:absolute; left:0pt;top:15pt; width:192pt;">
                        <h2>{{$property_name}}</h2>
                    </div>
                    <div style="position:absolute; right:-60pt; width:192pt;">
                        <img class="logo-img" src = "images/tamdeen-logo.png" alt="">
                    </div>
                </div>
        </header>
        <section class="sec-1">
        <div class="header-sec">
            <p class="header-content">Fitout Deposit Refund Form</p>
        </div>
        </section><br/>
        <div class="form-body">
            <section class="page-1">
            <table class="table-borderless" style="width:100%; padding:15px;">
                <tbody>
                    <tr>
                        <td style="width:50%; padding:5px;" class="td-width">Date: <span class="header-value">{{$pre_date}}</span></td>
                        <td style="width:50%; padding:5px;" class="td-width">Unit No: <span class="header-value">{{$unit_name}}</span></td>
                    </tr>
                    <tr>
                    <td style="width:50%; padding:5px;" class="td-width">Brand Name : <span class="header-value"> Mercedes Benz002</span></td>
                    <td style="width:50%; padding:5px;" class="td-width">Location/Level : <span class="header-value">{{$floor_name}}</span></td>
                    </tr>
                    <tr>
                    <td style="width:50%; padding:5px;" class="td-width">Company Name : <span class="header-value">{{$company_name}}</span></td>
                    <td style="width:50%; padding:5px;" class="td-width">Center : <span class="header-value">{{$property_name}}</span></td>
                    </tr>
                </tbody>
                </table>
                </section>
                <section>
                    <table class="fitout-table  pt-10" style="width:100%; padding:15px;">
                    <tr>
                        <th colspan="2" style="width:100%; height:20px; text-align:left" class="fs-11"><span class="header-value">Fitout Department</span></th>
                    </tr>
                    <tr>
                        <td rowspan="3" style="width:85%; height:60px; text-align:left; vertical-align: top;" class="fs-11"><span class="header-value"></span></td>
                        <td rowspan="1" style="width:15%; height:10px; text-align:center; vertical-align: top; border-bottom:1px solid black;" class="fs-11"><span class="header-value">{{$rdd_manager}}</span></td>
                    </tr>
                    <tr>
                        <td rowspan="1" style="width:15%; height:20px; text-align:center; vertical-align: top; border-bottom:1px solid black;" class="fs-11"><span class="header-value"></span></td>
                    </tr>
                    <tr>
                        <td rowspan="1" style="width:15%; height:30px; text-align:center; vertical-align: top;" class="fs-11"><span class="header-value"></span></td>
                    </tr>
                </table>
                </section>
                <section>
                    <table class="centre-table  pt-10" style="width:100%; padding:15px;">
                        <tr>
                            <th colspan="2" style="width:100%; height:20px; text-align:left" class="fs-11"><span class="header-value">Property Management</span></th>
                        </tr>
                        <tr>
                            <td rowspan="3" style="width:85%; height:60px; text-align:left; vertical-align: top;" class="fs-11"><span class="header-value"></span></td>
                            <td rowspan="1" style="width:15%; height:10px; text-align:center; vertical-align: top; border-bottom:1px solid black;" class="fs-11"><span class="header-value"></span></td>
                        </tr>
                        <tr>
                            <td rowspan="1" style="width:15%; height:20px; text-align:center; vertical-align: top; border-bottom:1px solid black;" class="fs-11"><span class="header-value"></span></td>
                        </tr>
                        <tr>
                            <td rowspan="1" style="width:15%; height:30px; text-align:center; vertical-align: top;" class="fs-11"><span class="header-value"></span></td>
                        </tr>
                     </table>
                </section>
                <section>
                    <table class="finance-table  pt-10" style="width:100%; padding:15px;">
                    <tr>
                            <th colspan="2" style="width:100%; height:20px; text-align:left" class="fs-11"><span class="header-value">Finance Management</span></th>
                        </tr>
                        <tr>
                            <td rowspan="3" style="width:85%; height:60px; text-align:left; vertical-align: top;" class="fs-11"><span class="header-value"></span></td>
                            <td rowspan="1" style="width:15%; height:10px; text-align:center; vertical-align: top; border-bottom:1px solid black;" class="fs-11"><span class="header-value"></span></td>
                        </tr>
                        <tr>
                            <td rowspan="1" style="width:15%; height:20px; text-align:center; vertical-align: top; border-bottom:1px solid black;" class="fs-11"><span class="header-value"></span></td>
                        </tr>
                        <tr>
                            <td rowspan="1" style="width:15%; height:30px; text-align:center; vertical-align: top;" class="fs-11"><span class="header-value"></span></td>
                        </tr>
                    </table>
                </section>
                <section>
                    <table class="deposit-table  pt-10" style="width:50%; padding:15px;">
                        <tr>
                            <td style="width:30%; height:20px; text-align:left" class="fs-11"><span class="header-value">Fitout Deposit Received</span></td>
                            <td style="width:20%; height:20px; text-align:right" class="fs-11"><span class="header-value"></span></td>
                        </tr>
                        <tr>
                            <td style="width:30%; height:20px; text-align:left" class="fs-11"><span class="header-value">Deductions</span></td>
                            <td style="width:20%; height:20px; text-align:right" class="fs-11"><span class="header-value"></span></td>
                        </tr>
                        <tr>
                            <td style="width:30%; height:20px; text-align:left" class="fs-11"><span class="header-value"></span></td>
                            <td style="width:20%; height:20px; text-align:right" class="fs-11"><span class="header-value"></span></td>
                        </tr>
                        <tr>
                            <td style="width:30%; height:20px; text-align:left" class="fs-11"><span class="header-value"></span></td>
                            <td style="width:20%; height:20px; text-align:right" class="fs-11"><span class="header-value"></span></td>
                        </tr>
                        <tr>
                            <td style="width:30%; height:20px; text-align:left" class="fs-11"><span class="header-value"></span></td>
                            <td style="width:20%; height:20px; text-align:right" class="fs-11"><span class="header-value"></span></td>
                        </tr>
                        <tr class="deposit-last">
                            <td style="width:30%; height:20px; text-align:left" class="fs-11"><span class="header-value">Net Refund</span></td>
                            <td style="width:20%; height:20px; text-align:right" class="fs-11"><span class="header-value"></span></td>
                        </tr>
                    </table>
                </section>
        </div>
        <footer>
             <p class="page"> </p>
        </footer>
    </body>
</html>