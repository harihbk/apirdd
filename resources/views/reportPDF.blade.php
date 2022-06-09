<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Property Report</title>
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
			/* padding:10px;s */
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
        .fs-11
        {
            font-size:11px;
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
            $y = $pdf->get_height() - 20;
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
                        <p class="text-danger header-content">Property Report- {{$property_name}}</p>
                    </div>
                </div>
                <div style="position:absolute; right:-50pt;top:-10px; width:172pt;">
                   <img style="height:50pt; width:50pt;" class="logo-img" src ="{{ public_path($property_logo) }}" alt="no_image">
                </div>
        </header>
        <section class="page-1">
            @foreach($result as $master_key=>$master_value)
            <table class="pt-10" style="width:100%; border:1px solid black;">
             <thead>
                    <tr>
                        <th style="width:75%; height:20px; text-align:left; font-size:16px; padding:5px;" class="header-content"><span class="header-value"> Unit Name:{{$master_value->unit_name}}</span></th>
                        @if($master_value->project_status==1)
                        <th style="width:25%; height:20px; font-size:16px;" class="header-content">Status:<span style="color:green;"> Completed</span></span></th>
                        @endif
                        @if($master_value->project_status==0)
                        <th style="width:25%; height:20px; font-size:16px;" class="header-content">Status:<span style="color:#e6b800;"> In Progress</span></span></th>
                        @endif
                    </tr>
             </thead>
            </table>
            <table class=" table-borderless compliance-table  pt-10" style="width:100%;">
              <tbody>
                <tr>
                    <td style="width:30%; padding:5px; font-size:14px;" class="td-width"><span class="header-value"><u>WorkSpace Details</u></span></td>
                    <td style="width:35%; padding-left:35px;" class="td-width"><span class="header-value"></span></td>
                    <td style="width:35%; padding:5px;" class="td-width"><span class="header-value"></span></td>
                </tr> 
                <tr style="margin-top:5px;">
                        <td style="width:50%; padding:5px;" class="td-width">Project Name: <span class="header-value">{{$master_value->project_name}}</span></td>
                        <td style="width:50%; padding-left:35px;" class="td-width">Investor Company: <span class="header-value">{{$master_value->company_name}}</span></td>
                        <td class="td-width"><span class="header-value"></span></td>
                    </tr>
                    <tr>
                        <td style="width:45%; padding:5px;" class="td-width">Leasing Area : <span class="header-value"> {{$master_value->unit_area!=null?$master_value->unit_area:'-'}}</span></td>
                        <td style="width:45%; padding-left:35px;" class="td-width">Type : <span class="header-value"> {{$master_value->type_name}}</span></td>
                        <td style="width:5%; padding:5px;" class="td-width"><span class="header-value"></span></td>
                    </tr>
                    <tr>
                        <td style="width:45%; padding:5px;" class="td-width">Brand : <span class="header-value">{{$master_value->investor_brand}}</span></td>
                        <td style="width:45%; padding-left:35px;" class="td-width">Fitout Deposit : <span class="header-value">{{$master_value->fitout_deposit_status==2?'Not Paid':($master_value->fitout_deposit_status==16?'Paid':'Not Applicable')}}</span></td>
                        <td style="width:5%; padding:5px;" class="td-width"><span class="header-value"></span></td>
                    </tr>
                    <tr>
                        <td style="width:45%; padding:5px; border-bottom:1px solid black;" class="td-width">Insurance Expiry : <span class="header-value">{{$master_value->insurance_validity_date!='0000-00-00'?date('d-m-Y',strtotime($master_value->insurance_validity_date)):'-'}}</span></td>
                        <td style="width:45%; padding-left:35px; border-bottom:1px solid black;" class="td-width">Fitout Period : <span class="header-value">{{$master_value->fitout_period}}</span></td>
                        <td style="width:5%; padding:5px; border-bottom:1px solid black;" class="td-width"><span class="header-value"></span></td>
                    </tr>
                    <tr style="margin-top:5px;">
                        <td style="width:30%; padding:5px; font-size:14px;" class="td-width"><span class="header-value"><u>MileStone Details</u></span></td>
                        <td style="width:35%; padding-left:35px;" class="td-width"><span class="header-value"></span></td>
                        <td style="width:35%; padding:5px;" class="td-width"><span class="header-value"></span></td>
                    </tr>
                    @foreach($master_value->milestone_data as $milestone_key=>$milestone_value)
                        <tr style="margin-top:5px;">
                            <td style="width:50%; padding:5px;" class="td-width"><span class="header-value"> V{{$milestone_value->version}}</span></td>
                            <td style="width:50%; padding-left:35px;" class="td-width"></td>
                            <td class="td-width"><span class="header-value"></span></td>
                        </tr>  
                         <tr>
                        <td style="width:30%; padding:5px;" class="td-width">Concept Submission:<span class="header-value">{{date('d-m-Y',strtotime($milestone_value->concept_submission))}}</span></td>
                        <td style="width:40%; padding:5px;" class="td-width">Detailed Design Submission: <span class="header-value">{{date('d-m-Y',strtotime($milestone_value->detailed_design_submission))}}</span></td>
                        <td style="width:40%; padding:5px;" class="td-width">Unit Handover Date: <span class="header-value">{{date('d-m-Y',strtotime($milestone_value->unit_handover))}}</span></td>
                        </tr>
                        <tr>
                            <td style="width:30%; padding:5px;" class="td-width">Fitout Start Date: <span class="header-value">{{date('d-m-Y',strtotime($milestone_value->fitout_start))}}</span></td>
                            <td style="width:40%; padding:5px;" class="td-width">Fitout Completion Date: <span class="header-value">{{date('d-m-Y',strtotime($milestone_value->fitout_completion))}}</span></td>
                            <td style="width:40%; padding:5px;" class="td-width">Store Opening Date: <span class="header-value">{{date('d-m-Y',strtotime($milestone_value->store_opening))}}</span></td>
                        </tr>
                    @endforeach
                    <tr style="margin-top:5px;">
                        <td style="width:30%; padding:5px; font-size:14px;" class="td-width"><span class="header-value"><u>Investor Planned Dates</u></span></td>
                        <td style="width:35%; padding-left:35px;" class="td-width"><span class="header-value"></span></td>
                        <td style="width:35%; padding:5px;" class="td-width"><span class="header-value"></span></td>
                    </tr>
                    @foreach($master_value->investor_dates as $investor_dates_key=>$investor_dates_value)
                        <tr style="margin-top:5px;">
                            <td style="width:50%; padding:5px;" class="td-width"><span class="header-value"> V{{$investor_dates_value->version}}</span></td>
                            <td style="width:50%; padding-left:35px;" class="td-width"></td>
                            <td class="td-width"><span class="header-value"></span></td>
                        </tr>  
                        <tr>
                        <td style="width:30%; padding:5px;" class="td-width">Concept Submission:<span class="header-value">{{date('d-m-Y',strtotime($investor_dates_value->concept_submission))}}</span></td>
                        <td style="width:40%; padding:5px;" class="td-width">Detailed Design Submission: <span class="header-value">{{date('d-m-Y',strtotime($investor_dates_value->detailed_design_submission))}}</span></td>
                        <td style="width:30%; padding:5px;" class="td-width"><span class="header-value"></span></td>
                        </tr>
                        <tr>
                            <td style="width:40%; padding:5px;" class="td-width">Fitout Start Date: <span class="header-value">{{date('d-m-Y',strtotime($investor_dates_value->fitout_start))}}</span></td>
                            <td style="width:40%; padding:5px;" class="td-width">Fitout Completion Date: <span class="header-value">{{date('d-m-Y',strtotime($investor_dates_value->fitout_completion))}}</span></td>
                            <td style="width:30%; padding:5px;" class="td-width"><span class="header-value"></span></td>
                        </tr>
                    @endforeach
                     <tr style="margin-top:5px;">
                        <td style="width:30%; padding:5px; border-top:1px solid black; font-size:14px;" class="td-width"><span class="header-value"><u>Contact Details</u></span></td>
                        <td style="width:35%; padding-left:45px; border-top:1px solid black;" class="td-width"><span class="header-value"></span></td>
                        <td style="width:35%; padding:5px; border-top:1px solid black;" class="td-width"><span class="header-value"></span></td>
                    </tr>
                         @for ($i=0;$i<$master_value->rdd_contact_data->count();$i++)
                                <tr>
                                    <td style="width:30%; padding:5px;" class="td-width">{{$master_value->rdd_contact_data[$i]['designation_name']}}: <span class="header-value">{{$master_value->rdd_contact_data[$i]['first_name']}} {{$master_value->rdd_contact_data[$i]['last_name']}}</span></td>
                                    @if($i!=4)
                                    <td style="width:40%;  padding-left:45px;" class="td-width">{{$master_value->rdd_contact_data[$i+1]['designation_name']}}: <span class="header-value">{{$master_value->rdd_contact_data[$i+1]['first_name']}} {{$master_value->rdd_contact_data[$i+1]['last_name']}}</span></td>
                                    <td style="width:40%; padding:5px;" class="td-width"></td>
                                    @endif
                                    @if($i==4)
                                    <td style="width:40%; padding-left:45px;" class="td-width">{{$master_value->investor_contact_data[0]['designation_name']}}: <span class="header-value">{{$master_value->investor_contact_data[0]['first_name']}} {{$master_value->investor_contact_data[0]['last_name']}}</span></td>
                                    <td style="width:40%; padding:5px;" class="td-width"></td>
                                    @endif
                                </tr>
                            {{$i=$i+1}}
                         @endfor
                         <tr>
                             <td style="width:40%; padding:5px;" class="td-width">{{$master_value->investor_contact_data[1]['designation_name']}}: <span class="header-value">{{$master_value->investor_contact_data[1]['first_name']}} {{$master_value->investor_contact_data[1]['last_name']}}</span></td>
                            <td style="width:40%; padding:5px;" class="td-width"></td>
                        </tr>
                    <tr style="margin-top:5px;">
                        <td style="width:30%; padding:5px; font-size:14px; border-top:1px solid black;" class="td-width"><span class="header-value"><u>Phase Details</u></span></td>
                        <td style="width:35%; padding-left:35px; border-top:1px solid black;" class="td-width"><span class="header-value"></span></td>
                        <td style="width:35%; padding:5px; border-top:1px solid black;" class="td-width"><span class="header-value"></span></td>
                    </tr>
                    <tr>
                        <td style="width:30%; padding:5px; color:brown;" class="td-width"><span class="header-value">Startup Phase</span></td>
                        <td style="width:35%; padding-left:35px;" class="td-width"><span class="header-value"></span></td>
                        <td style="width:35%; padding:5px;" class="td-width"><span class="header-value"></span></td>
                    </tr>
                    @foreach($master_value->startup_phase as $startup_key=>$startup_value)
                    <tr>
                         <td style="width:40%; padding:5px; border-bottom:1px solid black;" class="td-width"><span class="header-value">{{$startup_value->activity_desc}}
                         @if($startup_value->task_status==1)
                        <span style="color:green; font-weight:bolder;">[Completed]</span></span></td>
                        @else
                        <span style="color:#e6b800; font-weight:bolder;">[In Progress]</span></span></td>
                        @endif
                        <td style="width:30%; padding:5px; border-bottom:1px solid black;" class="td-width">Actual Date: <span class="header-value">{{$startup_value->actual_date!=null?date('d-m-Y', strtotime($startup_value->actual_date)):'-'}}</span></td>
                        <td style="width:40%;  padding-left:45px; border-bottom:1px solid black;" class="td-width">Planned Date:<span class="header-value">{{date('d-m-Y', strtotime($startup_value->planned_date))}}</span></td>
                    </tr>
                    @endforeach
                     <tr>
                        <td style="width:30%; padding:5px; color:brown;" class="td-width"><span class="header-value">Design Phase</span></td>
                        <td style="width:35%; padding-left:35px;" class="td-width"><span class="header-value"></span></td>
                        <td style="width:35%; padding:5px;" class="td-width"><span class="header-value"></span></td>
                    </tr>
                    @foreach($master_value->design_phase as $master_design_key=>$master_design_value)
                    <tr>
                        <td style="width:30%; padding:5px; color:#4000ff;" class="td-width"><span class="header-value">{{$master_design_key}}</span></td>
                        <td style="width:40%;  padding-left:45px;" class="td-width"></td>
                        <td style="width:40%; padding:5px;" class="td-width"></td>
                    </tr>
                    @foreach($master_design_value as $design_key=>$design_value)
                    <tr>
                        <td style="width:60%; padding:5px; border:1px solid black;" class="td-width"><span class="header-value">{{$design_value->doc_title!=''?$design_value->doc_title:'-'}}
                         @if($design_value->doc_status==8)
                        <span style="color:green; font-weight:bolder;">[Completed]</span></span></td>
                        <td style="width:5%; padding:5px; border:1px solid black;" class="td-width">Actual Date: <span class="header-value">{{date('d-m-Y',strtotime($design_value->updated_at))}}</span></td>
                        <td style="width:5%;  padding-left:45px; border:1px solid black;" class="td-width">Planned Date:<span class="header-value">{{date('d-m-Y',strtotime($design_value->due_date))}}</span></td>
                        @else
                        <span style="color:#e6b800; font-weight:bolder;">[In Progress]</span></span></td>
                        <td style="width:5%; padding:5px; border:1px solid black;" class="td-width">Actual Date: <span class="header-value">-</span></td>
                        <td style="width:5%;  padding-left:45px; border:1px solid black;" class="td-width">Planned Date:<span class="header-value">{{date('d-m-Y',strtotime($design_value->due_date))}}</span></td>
                        @endif
                    </tr>
                    @endforeach
                    @endforeach
                    <tr>
                        <td style="width:30%; padding:5px; border-top:1px solid black; color:brown;" class="td-width"><span class="header-value">Fitout Phase</span></td>
                        <td style="width:35%; padding-left:35px; border-top:1px solid black;" class="td-width"><span class="header-value"></span></td>
                        <td style="width:35%; padding:5px; border-top:1px solid black;" class="td-width"><span class="header-value"></span></td>
                    </tr>
                    @foreach($master_value->fitout_phase as $fitout_key=>$fitout_value)
                    <tr>
                        <td style="width:40%; padding:5px;" class="td-width"><span class="header-value">{{$fitout_value->activity_desc}}
                        @if($fitout_value->task_status==1)
                        <span style="color:green; font-weight:bolder;">[Completed]</span></span></td>
                        @else
                        <span style="color:#e6b800; font-weight:bolder;">[In Progress]</span></span></td>
                        @endif
                        <td style="width:30%; padding:5px;" class="td-width">Actual Date: <span class="header-value">{{$fitout_value->actual_date!=null?date('d-m-Y', strtotime($fitout_value->actual_date)):'-'}}</span></td>
                        <td style="width:40%;  padding-left:45px;" class="td-width">Planned Date:<span class="header-value">{{date('d-m-Y', strtotime($fitout_value->planned_date))}}</span></td>
                    </tr>
                    @endforeach
                    <tr>
                         <td style="width:40%; padding:5px; color:#4000ff;" class="td-width"><span class="header-value">Inspection Report</span></td>
                        <td style="width:30%; padding:5px;" class="td-width"></td>
                        <td style="width:40%;  padding-left:45px;" class="td-width"></td>
                    </tr>
                    @foreach($master_value->inspection_data as $inspection_key=>$inspection_value)
                     <tr>
                         <td style="width:40%; padding:5px; border:1px solid black;" class="td-width"><span class="header-value">{{$inspection_value->template_name}}
                         @if($inspection_value->report_status==2)
                         <span style="color:green; font-weight:bolder;">[Completed]</span></span></td>
                         <td style="width:30%; padding:5px; border:1px solid black;" class="td-width">Actual Date: <span class="header-value">{{date('d-m-Y', strtotime($inspection_value->updated_at))}}</span></td>
                        <td style="width:40%;  padding-left:45px; border:1px solid black;" class="td-width">Planned Date: <span class="header-value">{{date('d-m-Y', strtotime($inspection_value->requested_time))}}</span></td>
                         @elseif($inspection_value->report_status==3)
                         <span style="color:red; font-weight:bolder;">[Rejected]</span></span></td>
                         <td style="width:30%; padding:5px; border:1px solid black;" class="td-width">Actual Date: <span class="header-value">{{date('d-m-Y', strtotime($inspection_value->updated_at))}}</span></td>
                        <td style="width:40%;  padding-left:45px; border:1px solid black;" class="td-width">Planned Date: <span class="header-value">{{date('d-m-Y', strtotime($inspection_value->requested_time))}}</span></td>
                         @else
                         <span style="color:#e6b800; font-weight:bolder;">[In Progress]</span></span></td>
                         <td style="width:30%; padding:5px; border:1px solid black;" class="td-width">Actual Date: <span class="header-value">-</span></td>
                        <td style="width:40%;  padding-left:45px; border:1px solid black;" class="td-width">Planned Date: <span class="header-value">{{date('d-m-Y', strtotime($inspection_value->requested_time))}}</span></td>
                        @endif
                    </tr>
                    @endforeach
                    <tr>
                        <td style="width:30%; padding:5px; border-top:1px solid black; color:brown;" class="td-width"><span class="header-value">Completion Phase</span></td>
                        <td style="width:35%; padding-left:35px; border-top:1px solid black;" class="td-width"><span class="header-value"></span></td>
                        <td style="width:35%; padding:5px; border-top:1px solid black;" class="td-width"><span class="header-value"></span></td>
                    </tr>
                    @foreach($master_value->fcc_data as $fcc_key=>$fcc_value)
                    <tr>
                         <td style="width:40%; padding:5px;" class="td-width"><span class="header-value">Generate FCC
                         @if($fcc_value->isGenerated==1)
                         <span style="color:green; font-weight:bolder;">[Completed]</span></span></td>
                         @else
                         <span style="color:#e6b800; font-weight:bolder;">[In Progress]</span></span></td>
                         @endif
                        <td style="width:30%; padding:5px;" class="td-width">Actual Date: <span class="header-value">{{$fcc_value->actual_date!=null?date('d-m-Y', strtotime($fcc_value->actual_date)):'-'}}</span></td>
                        <td style="width:40%;  padding-left:45px;" class="td-width">Planned Date: <span class="header-value">{{date('d-m-Y', strtotime($fcc_value->planned_date))}}</span></td>
                    </tr>
                    @endforeach
                    @foreach($master_value->fdr_data as $fdr_key=>$fdr_value)
                    <tr>
                         <td style="width:40%; padding:5px;" class="td-width"><span class="header-value">Generate FDR
                         @if($fcc_value->isdrfGenerated==1)
                         <span style="color:green; font-weight:bolder;">[Completed]</span></span></td>
                         @else
                         <span style="color:#e6b800; font-weight:bolder;">[In Progress]</span></span></td>
                         @endif
                        <td style="width:30%; padding:5px;" class="td-width">Actual Date: <span class="header-value">{{$fdr_value->actual_date!=null?date('d-m-Y', strtotime($fdr_value->actual_date)):'-'}}</span></td>
                        <td style="width:40%;  padding-left:45px;" class="td-width">Planned Date: <span class="header-value">{{date('d-m-Y', strtotime($fdr_value->planned_date))}}</span></td>
                    </tr>
                    @endforeach
               </tbody>
               <hr class="seperator">
            </table>
            @endforeach
            </section>
        <footer>
             <p class="page"> </p>
        </footer>
	</body>
</html>
