<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Handover Certificate</title>
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
        .compliance-table tr td 
        {
            border: 1px solid black;
            padding-left: 5px;
        }
        .authorization-table tr td 
        {
            border: 1px solid black;
            padding-left: 5px;
        }
        .authorization-table tr th 
        {
            border: 1px solid black;
        }
        .inner-header-content
        {
            background: #cacaca;
            font-weight: bolder;
        }
        ul.copy-lists {
            list-style-type: none;
            margin: 0;
            padding-left: 20px;     
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
                        <p class="text-danger header-content">Handover Certificate</p>
                    </div>
                </div>
        </header>
        <section class="page-1">
        <table class="table-borderless pt-50" style="width:100%;">
            <tbody>
                <tr>
                    <td class="td-width">
                        <dl>
                        <dt>Premises Numbers : </dt>
                        <dd class="header-value">Mercedes Benz002</dd>
                        </dl>
                    </td>
                    <td class="td-width">
                        <dl>
                        <dt>Premises location : </dt>
                        <dd class="header-value">UNIT01UNIT01UNIT01UNIT02 </dd>
                        </dl>
                    </td>
                </tr>
                <tr>
                <td class="td-width">
                        <dl>
                        <dt>Investor Brand Name  : </dt>
                        <dd class="header-value">UNIT01UNIT01UNIT01UNIT02</dd>
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
                        <dt>Handover Inspection Date  : </dt>
                        <dd class="header-value">UNIT01UNIT01UNIT01UNIT02</dd>
                        </dl>
                    </td>
                <td class="td-width">
                        <dl>
                        <dt>Handover Date (Actual) : </dt>
                        <dd class="header-value">-</dd>
                        </dl>
                </td>
            </tr>
            <tr>
                <td class="td-width">
                        <dl>
                        <dt>Investor Company Name  : </dt>
                        <dd class="header-value">UNIT01UNIT01UNIT01UNIT02</dd>
                        </dl>
                    </td>
            </tr>
            </tbody>
            </table>
            </section>
            <hr class="seperator">
            <section>
            <table class="compliance-table  pt-10" style="width:100%;">
                    <tr>
                        <th colspan="2" style="width:80%; height:20px; text-align:left; font-size:14px;" class="header-content"><span class="header-value">Owner’s Works Inspection Checklist </span></th>
                        <th colspan="1" style="width:20%; height:20px; font-size:14px;" class="header-content">Complete<br/>YES / NO</th>
                    </tr>
                    <tr>
                        <td colspan="3" class="inner-header-content" style="width:100%;height:30px; font-size:10px; text-align:left;" class="header-content">Structure</td>
                    </tr>
                    <tr>
                        <td style="width:20%;height:30px; font-size:10px;">Floor</td>
                        <td style="width:65%;height:30px; font-size:10px;">Unfinished reinforced concrete or structural steel (10 cm set down)</td>
                        <td style="width:15%;height:30px; font-size:10px;"></td>
                    </tr>
                    <tr>
                        <td style="width:20%;height:30px; font-size:10px;">Ceiling</td>
                        <td style="width:65%;height:30px; font-size:10px;">Unfinished reinforced concrete or structural steel</td>
                        <td style="width:15%;height:30px; font-size:10px;"></td>
                    </tr>
                    <tr>
                        <td style="width:20%;height:30px; font-size:10px;">Columns</td>
                        <td style="width:65%;height:30px; font-size:10px;">Unfinished reinforced concrete</td>
                        <td style="width:15%;height:30px; font-size:10px;"></td>
                    </tr>
                    <tr>
                        <td style="width:20%;height:30px; font-size:10px;">Walls</td>
                        <td style="width:65%;height:30px; font-size:10px;">Unfinished 15 cm concrete block work or steel stud with plasterboard finish (floor to underside of slab unless otherwise noted)
                        </td>
                        <td style="width:15%;height:30px; font-size:10px;"></td>
                    </tr>      
                    <tr>
                        <td colspan="3" class="inner-header-content" style="width:100%;height:30px; font-size:10px; text-align:left;" class="header-content">MEP</td>
                    </tr>  
                    <tr>
                        <td style="width:20%;height:30px; font-size:10px;">Fire Fighting</td>
                        <td style="width:65%;height:30px; font-size:10px;">A water point at a Owner nominated location at the boundary of the Premises.</td>
                        <td style="width:15%;height:30px; font-size:10px;"></td>
                    </tr>
                    <tr>
                        <td style="width:20%;height:30px; font-size:10px;">Electrical</td>
                        <td style="width:65%;height:30px; font-size:10px;">Three phase power supply to a shop isolator (isolator provided by Owner at Investor’s expense).Electrical load of the three phase power supply as detailed on the POD for the Premises.</td>
                        <td style="width:15%;height:30px; font-size:10px;"></td>
                    </tr>
                    <tr>
                        <td style="width:20%;height:30px; font-size:10px;">IT</td>
                        <td style="width:65%;height:30px; font-size:10px;">Provision of three 1 inch pipes which can accommodate 2 UTP cables per pipe of IT services, each connected to the Owner’s main computer room (MCCR).</td>
                        <td style="width:15%;height:30px; font-size:10px;"></td>
                    </tr>
                    <tr>
                        <td style="width:20%;height:30px; font-size:10px;">Mechanical</td>
                        <td style="width:65%;height:30px; font-size:10px;">Provision of chilled water pipework to an Owner nominated point within or on the boundary of the Premises. Capacity of the chilled water as nominated on the POD for the Premises.</td>
                        <td style="width:15%;height:30px; font-size:10px;"></td>
                    </tr>
                    <tr>
                        <td style="width:20%;height:30px; font-size:10px;">Mechanical</td>
                        <td style="width:65%;height:30px; font-size:10px;">Provision of chilled water pipework to an Owner nominated point within or on the boundary of the Premises. Capacity of the chilled water as nominated on the POD for the Premises.</td>
                        <td style="width:15%;height:30px; font-size:10px;"></td>
                    </tr>
                    <tr>
                        <td style="width:20%;height:30px; font-size:10px;"></td>
                        <td style="width:65%;height:30px; font-size:10px;">Provision of a fresh air supply duct to an Owner nominated point within or on the boundary of the Premises. Capacity of the subpply duct as nominated on the POD for the Premises.</td>
                        <td style="width:15%;height:30px; font-size:10px;"></td>
                    </tr>
                    <tr>
                        <td style="width:20%;height:30px; font-size:10px;"></td>
                        <td style="width:65%;height:30px; font-size:10px;">Provision of a common system kitchen exhausts connection point, to an Owner nominated point within or on the boundary of the Premises. Capacity of the kitchen exhaust as nominated on the POD for the premises. Maximum dimension of the duct is 60cm x 6cm.</td>
                        <td style="width:15%;height:30px; font-size:10px;"></td>
                    </tr>
                    <tr>
                        <td style="width:20%;height:30px; font-size:10px;"></td>
                        <td style="width:65%;height:30px; font-size:10px;">Air handling Units (as specified on the POD’s for the Premises)</td>
                        <td style="width:15%;height:30px; font-size:10px;"></td>
                    </tr>
                    <tr>
                        <td style="width:20%;height:30px; font-size:10px;">Gas</td>
                        <td style="width:65%;height:30px; font-size:10px;">Provision of gas supply pipe work to an Owner nominated point within or on the boundary of the Premises – to nominated Premises only, as detailed on the POD for the Premises.</td>
                        <td style="width:15%;height:30px; font-size:10px;"></td>
                    </tr>
                    <tr>
                        <td style="width:20%;height:30px; font-size:10px;">Plumbing</td>
                        <td style="width:65%;height:30px; font-size:10px;">The provision of a cold water supply and drainage oulet to an Owner nominated point within or on the boundary of the Premises.</td>
                        <td style="width:15%;height:30px; font-size:10px;"></td>
                    </tr>
                    <tr>
                        <td style="width:20%;height:30px; font-size:10px;"></td>
                        <td style="width:65%;height:30px; font-size:10px;">Supply and installation of a 10cm sewer drain in all restaurant premises in excess of 200 m2 in size (up to 10cm above the structural slab). This includes floor slab penetrations with all setout by the Investor.</td>
                        <td style="width:15%;height:30px; font-size:10px;"></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="inner-header-content" style="width:100%;height:30px; font-size:10px; text-align:left;" class="header-content">Shopfront</td>
                    </tr>  
                    <tr>
                        <td style="width:20%;height:30px; font-size:10px;">Blade signage armyature</td>
                        <td style="width:65%;height:30px; font-size:10px;">A wall or ceiling mounted whichever the case mayby armyature for a blade Sign – in those Premises and locations as nominated by the Owner.</td>
                        <td style="width:15%;height:30px; font-size:10px;"></td>
                    </tr>
            </table>
            </section>
            <hr class="seperator">
            <section>
            <table class="compliance-table  pt-10" style="width:100%;">
                    <tr>
                        <th style="width:70%; height:20px; text-align:left font-size:12px;" class="header-content"><span class="header-value">Uncompleted / Defective Works (If any) </span></th>
                        <th style="width:30%; height:20px; font-size:14px;" class="header-content">Completion Date</th>
                    </tr>
                    <tr>
                        <td style="width:70%;height:30px; font-size:10px;"></td>
                        <td style="width:30%;height:30px;font-size:10px;"></td>
                    </tr>
                    <tr>
                        <td style="width:70%;height:30px; font-size:10px;"></td>
                        <td style="width:30%;height:30px;font-size:10px;"></td>
                    </tr>
                    <tr>
                        <td style="width:70%;height:30px; font-size:10px;"></td>
                        <td style="width:30%;height:30px;font-size:10px;"></td>
                    </tr>
                    <tr>
                        <td style="width:70%;height:30px; font-size:10px;"></td>
                        <td style="width:30%;height:30px;font-size:10px;"></td>
                    </tr>
                    <tr>
                        <td style="width:70%;height:30px; font-size:10px;"></td>
                        <td style="width:30%;height:30px;font-size:10px;"></td>
                    </tr>
            </table>
            </section>
            <hr class="seperator">
            <section>
                <p style="font-size:12px;"><b>Acceptance of Handover of Premises</b></p>
                <ul class="fs-11">
                    <li>They have vested authority of the Investor to accept handover of the premises as shown on this form.</li>
                    <li>They have fully inspected the premises on the Inspection Date shown and confirm that all Owner’s works as specified in the Investor’s Fitout & Design Guideline have been completed in full, and / or will be completed in full by the completion date as shown.</li>
                    <li>They confirm formal handover acceptance of the Premises effective from the Handover Date shown in the right hand corner of the front page of this form, and acknowledge that no works of any description may occur within the Premises without the prior written approval of the Owne.</li>
                </ul>
            </section>
            <hr class="seperator">
            <table class="authorization-table  pt-10" style="width:100%;">
                    <tr>
                        <th style="width:35%;height:30px; font-size:12px; font-weight:900; text-align:center;"><b>Authorization</b></th>
                        <th style="width:40%;height:30px;font-size:12px; font-weight:900; text-align:center;"><b>Name</b></th>
                        <th style="width:15%;height:30px;font-size:12px; font-weight:900; text-align:center;"><b>Signature</b></th>
                        <th style="width:10%;height:30px;font-size:12px; font-weight:900; text-align:center;"><b>Date</b></th>
                    </tr>
                    <tr>
                        <td style="width:35%;height:30px; font-size:12px; font-weight:900; text-align:center;">Investor’s Authorized Representative</td>
                        <td style="width:40%;height:30px;font-size:12px; font-weight:900; text-align:center;"></td>
                        <td style="width:15%;height:30px;font-size:12px; font-weight:900; text-align:center;"></td>
                        <td style="width:10%;height:30px;font-size:12px; font-weight:900; text-align:center;"></td>
                    </tr>
                    <tr>
                        <td style="width:35%;height:30px; font-size:12px; font-weight:900; text-align:center;">Investor Project Manager</td>
                        <td style="width:40%;height:30px;font-size:12px; font-weight:900; text-align:center;"></td>
                        <td style="width:15%;height:30px;font-size:12px; font-weight:900; text-align:center;"></td>
                        <td style="width:10%;height:30px;font-size:12px; font-weight:900; text-align:center;"></td>
                    </tr>
                    <tr>
                        <td style="width:35%;height:30px; font-size:12px; font-weight:900; text-align:center;">RDD Project Manager</td>
                        <td style="width:40%;height:30px;font-size:12px; font-weight:900; text-align:center;"></td>
                        <td style="width:15%;height:30px;font-size:12px; font-weight:900; text-align:center;"></td>
                        <td style="width:10%;height:30px;font-size:12px; font-weight:900; text-align:center;"></td>
                    </tr>
            </table>
            <section class="pt-50">
                <p>cc:</p>
                <ul class="copy-lists">
                    <li>Finance Dept</li>
                    <li>Centre Manager</li>
                    <li>GM – Leasing</li>
                    <li>GM - Marketing & Operations</li>
                    <li>Chief Operating Officer</li>
                </ul>
            </section>
        <footer>
             <p class="page"> </p>
        </footer>
	</body>
</html>
