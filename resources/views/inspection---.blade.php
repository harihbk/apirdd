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
                top: -75px;
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
	  	.page {
			  width: 100%;
			  min-height: 100%;
			  padding: 10px;
			  margin: auto;
			  background: white;
			  font-size:0.9rem;
			}
		.header-sec
		{
			background: #cacaca;
            border:1px solid black;
            height:30px;
		}
		.pt-30 {
		  padding-top: 30px;
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
		.pt-10
		{
			padding-top: 10px;
		}
		.header-content
		{
            text-align:center;
            font-weight:bolder;
		}
		.logo-img
		{
			width:40%;
			height:auto;
			padding:10px;
		}
        .td-width
        {
            font-size:12px;
            padding-right:10px;
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
        /* .value-content::before {
                content:" ";
                position: absolute;
                border-bottom:1px solid red;
                width:100%; 
                height:1.1em; 
        } */
        .fs-11
        {
            font-size:11px;
        }
        .checklist-table tr th  {
        border: 1px solid black;
        }
        .checklist-table tr td  {
        border: 1px solid black;
        }
        .compliance-table tr td 
        {
            border: 1px solid black;
            text-indent: 10px;
        }
        .authorization-table tr th  {
        border: 1px solid black;
        }
        .authorization-table tr td  {
        border: 1px solid black;
        }
        ul.copy-lists {
            list-style-type: none;
            margin: 0;
            padding: 0;     
            font-size:11px;        
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
                    <div style="position:absolute; left:0pt; width:192pt;">
                        <img class="logo-img" src = "images/tamdeen-logo.png" alt="">
                    </div>
                </div>
                <div style="margin-left:170pt; width:192pt;" class="pt-30">
                    <div class="header-sec">
                        <p class="text-danger header-content">Inspection Report</p>
                    </div>
                </div>
        </header>
        <section class="page-1">
        <table class="table-borderless pt-50" style="width:100%; padding-bottom:10px;">
            <tbody>
                <tr>
                    <td class="td-width">
                        <dl>
                        <dt>Premises Numbers : </dt>
                        <dd class="header-value">{{$unit_name}}</dd>
                        </dl>
                    </td>
                    <td class="td-width">
                        <dl>
                        <dt>Premises location : </dt>
                        <dd class="header-value">Main Level</dd>
                        </dl>
                    </td>
                </tr>
                <tr>
                <td class="td-width">
                        <dl>
                        <dt>Investor Brand Name  : </dt>
                        <dd class="header-value">{{$investor_brand}}</dd>
                        </dl>
                    </td>
                <td class="td-width">
                        <dl>
                        <dt>Concept Zone(if applicable) : </dt>
                        <dd class="header-value">-</dd>
                        </dl>
                </td>
                </tr>
            <tr>
                <td class="td-width">
                        <dl>
                        <dt>Investor Company Name  : </dt>
                        <dd class="header-value">{{$company_name}}</dd>
                        </dl>
                </td>
                <td class="td-width">
                        <dl>
                        <dt>Inspection Date  : </dt>
                        <dd class="header-value">{{$inspection_date}}</dd>
                        </dl>
                    </td>
            </tr>
			<tr>
                <td class="td-width">
                        <dl>
                        <dt>Fitout Completion : </dt>
                        <dd class="header-value"></dd>
                        </dl>
                </td>
            </tr>
            </tbody>
            </table>
            </section>
            <hr class="seperator">
            <section>
                <div style="clear:both; position:relative;">
                   <h6 class="header-value">ATTENDEES</h6>
                </div>
                <table class="table-borderless pt-10" style="width:100%;">
                        <tbody>
                        <tr>
                            <td class="td-width">
                                <dl>
                                <dt>RDD Project Manager : </dt>
                                <dd class="header-value">{{$rdd_manager}}</dd>
                                </dl>
                            </td>
                        </tr>
                        <tr>
                        <td class="td-width">
                                <dl>
                                <dt>TMM MEP Consultant  : </dt>
                                <dd class="header-value">-</dd>
                                </dl>
                            </td>
                        </tr>
                        <tr>
                            <td class="td-width">
                                    <dl>
                                    <dt>Investor’s Authorized Signatory  : </dt>
                                    <dd class="header-value">-</dd>
                                    </dl>
                                </td>
                        </tr>
                        <tr>
                            <td class="td-width">
                                    <dl>
                                    <dt>Investor’s Shop fitter  : </dt>
                                    <dd class="header-value">-</dd>
                                    </dl>
                            </td>
                        </tr>
                        </tbody>
             </table>
            </section>
            <hr class="seperator">
            <section>
            <p><span class="header-value">Inspection Checklist</span> <span class="fs-11">(in strict compliance with the Investor Fitout & Design Guidelines and the approved Detailed Design drawings)</span></p>
            <table class="checklist-table  pt-10" style="width:100%;">
            @foreach($inspection_data as $master_key=>$master_value)
					<tr>
					<th style="background:#cacaca; font-size:12px; padding:2px; height:20px;" class="header-content">{{$master_key}}</th>
                        <th style="background:#cacaca; font-size:12px; padding:2px; height:20px;" class="header-content">Comments/defects</th>
                        <th style="background:#cacaca; font-size:12px; padding:2px; height:20px;" class="header-content">Action</th>
                        <th style="background:#cacaca; font-size:12px; padding:2px; height:20px;" class="header-content">Finish Date (By Investor)</th>
					</tr>
                @foreach($master_value as $key=>$value)
					<tr>
                        <td style="width:40%;height:35px; font-size:12px;text-align:center">{{$value->checklist_desc}}</td>
                        <td style="width:30%;height:35px; font-size:12px;text-align:center"></td>
                        <td style="width:15%;height:35px; font-size:12px;text-align:center">{{$value->rdd_actuals==1?'Yes':'No'}}</td>
                        <td style="width:15%;height:35px; font-size:12px;text-align:center"></td>
						
                    </tr>
                @endforeach
            @endforeach
            </table>
            </section>
            <!--<hr class="seperator">
            <section>
            <table class="compliance-table  pt-10" style="width:100%;">
                    <tr>
                        <th style="width:80%; height:20px; text-align:left" class="header-content"><span class="header-value">Fitout Completion Date Compliance </span> <span class="fs-11">(by Investor)</span></th>
                        <th style="width:20%; height:20px;" class="header-content">YES / NO</th>
                    </tr>
                    <tr>
                        <td style="width:80%;height:30px; font-size:10px;">Payment of all / any outstanding monies owed to TMM and / or Owner</td>
                        <td style="width:20%;height:30px;font-size:10px;"></td>
                    </tr>
                    <tr>
                        <td style="width:80%;height:30px; font-size:10px;">Hard Copy submission of all applicable fitout related approvals (Kuwait Municipality , KFD, etc)</td>
                        <td style="width:20%;height:30px; font-size:10px;"></td>
                    </tr>
                    <tr>
                        <td style="width:80%;height:30px; font-size:10px;">Hard and soft Copy of all “as built” drawings</td>
                        <td style="width:20%;height:30px; font-size:10px;"></td>
                    </tr>
                    <tr>
                        <td style="width:80%;height:30px; font-size:10px;">Investor or Investor’s Authorised Signatory</td>
                        <td style="width:20%;height:30px; font-size:10px;"></td>
                    </tr>
            </table>
            </section>-->
            <hr class="seperator">
            <section>
                <table class="authorization-table  pt-10" style="width:100%;">
                        <tr>
                            <th style="width:30%; font-size:12px; height:20px;" class="header-content"><span class="header-value">Authorization</span></th>
                            <th style="width:40%; font-size:12px; height:20px;" class="header-content"><span class="header-value">Name</span></th>
                            <th style="width:15%; font-size:12px; height:20px;" class="header-content"><span class="header-value">Signature</span></th>
                            <th style="width:15%; font-size:12px; height:20px;" class="header-content"><span class="header-value">Date</span></th>
                        </tr>
                        <tr>
                            <td style="width:30%;height:30px;"></td>
                            <td style="width:40%;height:30px;"></td>
                            <td style="width:15%;height:30px;"></td>
                            <td style="width:15%;height:30px;"></td>
                        </tr>
                        <tr>
                            <td style="width:30%;height:30px;"></td>
                            <td style="width:40%;height:30px;"></td>
                            <td style="width:15%;height:30px;"></td>
                            <td style="width:15%;height:30px;"></td>
                        </tr>
                        <tr>
                            <td style="width:30%;height:30px;"></td>
                            <td style="width:40%;height:30px;"></td>
                            <td style="width:15%;height:30px;"></td>
                            <td style="width:15%;height:30px;"></td>
                        </tr>
                </table>
            </section>
            <section class="pt-50">
                <ul class="copy-lists">
                    <li>Copy to</li>
                    <li>Chief Operating Office</li>
                    <li>General Manager - Leasing</li>
                    <li>General Manager – Operations & Marketing</li>
                    <li>General Manager – Retail Design & Delivery</li>
                    <li>Centre Manager</li>
                </ul>
            </section>
        <footer>
             <p class="page"> </p>
        </footer>
	</body>
</html>
