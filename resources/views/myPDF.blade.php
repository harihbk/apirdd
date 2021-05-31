<style>
#mytable {
  margin: 0;
  padding: 0;
  background-color: white;
  font: 12pt "Tahoma";
}

table {
  border-collapse: collapse;
}

* {
  box-sizing: border-box;
  -moz-box-sizing: border-box;
}

.page {
  width: auto;
  min-height: 100%;
  padding: 10px;
  margin: 1cm auto;
  border: 1px #d3d3d3 solid;
  border-radius: 5px;
  background: white;
}

@page {
  size: 21cm 29.7cm;
  margin: 30mm 45mm 30mm 45mm;
}

.subpage1 {
  padding: 1cm;

  height: 100%;
  width: auto;
}
.subpage2 {
  padding: 1cm;

  height: 100%;
  width: auto;
}

@page {
  size: A4;
  margin: 0;
}

@media print {
  .page {
    margin: 0;
    border: initial;
    border-radius: initial;
    width: initial;
    min-height: initial;
    box-shadow: initial;
    background: white;
    page-break-after: always;
  }
}

.border-line input {
  border-bottom: 2px solid black !important;
  border-bottom-left-radius: -25px;
  border-bottom-right-radius: 0px;
}

.border-bottom:focus {
  box-shadow: unset !important;
}

div.ex0 {
  width: 100%;
  padding: 25px;
  box-sizing: border-box;
}

div.ex1 {
  width: 100%;
  padding: 25px;
  box-sizing: border-box;
}

div.ex2 {
  width: 100%;
  padding: 25px;
  box-sizing: border-box;
}

div.ex3 {
  width: 100%;
  padding: 25px;
  box-sizing: border-box;
}

div.ex4 {
  width: 100%;
  padding: 25px;
  box-sizing: border-box;
}

div.ex5 {
  width: 100%;
  padding: 25px;
  box-sizing: border-box;
}

div.ex6 {
  width: 100%;
  padding: 25px;
  box-sizing: border-box;
}

div.ex9 {
  width: 100%;
  padding: 25px;
  box-sizing: border-box;
}

input {
  width: 100%;
  border: 0;
  outline: 0;
  background: white;
  border-bottom: 1px solid black;
}
</style>

<div id="mytable">
	<div class="book book col-8 mx-auto">
		<section class="page-1">
			<div class="page">
                <div class="subpage 1">
                    <div class="row">
						<div class="col-3 d-flex align-items-center">
							<div class="logo-img">
								<img class="w-50" src="../assets/tamdeen-logo/tamdeen-logo.png" alt="">
							</div>
						</div>
						<div class="col-6 d-flex align-items-center">
							<div class="border border-secondary">
								<h2 class="text-danger">Handover Certificate</h2>
							</div>
						</div><br/><br/>
						<h5>
                        <div class="ex0">
							<table>
									<tr>
										<td>Premises Numbers</td>
										<td><input /></td>
										<td>Premises location</td>
										<td><input /></td>
										<td>Concept Zone <br /> (if applicable)</td>
										<td><input /></td>
									</tr>
									<tr>
										<td>Investor Brand Name</td>
										<td><input /></td>
										<td>Handover Inspection Date</td>
										<td><input /></td>
										<td>Handover Date<br />(Actual)</td>
										<td colspan="2"><input /></td>
									</tr>
									<tr>
										<td>Investor Company Name</td>
										<td colspan="5"><input /></td>
									</tr>
							</table>
                            <br>
                            <DIV STYLE="background-color:#000000; height:5px; width:100%;">
                            </DIV>
                        </div>
                        </h5> <br>
						 <h5>
							<div class="ex1">
								 <p>Owner’s Work Inspection Checklist</p>
								 <table class="table table-bordered"  style="width: 100%" >
									 <thead class="thead-light" >
										<tr>
											<th scope="col"  style="width: 10%">Structure</th>
											<th scope="col"  style="width: 80%">Structure</th>
											<th scope="col"  style="width: 10%">Complete YES/NO</th>

										</tr>
									</thead>
									<tbody>
										<tr>
										<td scope="row">Floor</td>
										<td scope="row">Unfinished reinforced concrete or structural steel (10 cm set
											down)</td>
										<td></td>
										</tr>
										<tr>
											<td scope="row">Ceiling</td>
											<td>Unfinished reinforced concrete or structural steel</td>
											<td></td>
										</tr>
										<tr>
											<td scope="row">Coloumns</td>
											<td>Unfinished reinforced concrete</td>
											<td></td>
										</tr>
										<tr>
											<td scope="row">Walls</td>
											<td>Unfinished 15 cm concrete block work or steel stud with plasterboard
												finish (floor to underside of slab unless otherwise noted)
											</td>
											<td></td>
										</tr>
									</tbody>
									<thead class="thead-light">
                                        <tr>
                                            <th scope="col" style="width: 10%">MEP</th>
                                            <th scope="col"  style="width: 80%">MEP</th>
                                            <th scope="col"  style="width: 10%">Complete YES/NO</th>
                                        </tr>
                                    </thead>
									<tbody>
                                    <tr>
                                        <td scope="row">Fire Fighting Detectors & Smoke</td>
                                        <td>A water point at a Owner nominated location at the boundary of the Premises
                                        </td>
                                        <td></td>

                                    </tr>
                                    <tr>
                                        <td scope="row">Electrical</td>
                                        <td>Three phase power supply to a shop isolator (isolator provided by Owner at
                                            Investor’s expense).Electrical load of the three phase power supply as
                                            detailed on the POD for the Premises.</td>
                                        <td></td>

                                    </tr>
                                    <tr>
                                        <td scope="row">IT</td>
                                        <td>Provision of three 1 inch pipes which can accommodate 2 UTP cables per pipe
                                            of IT services, each connected to the Owner’s main computer room (MCCR).
                                        </td>
                                        <td></td>

                                    </tr>
                                    <tr>
                                        <td scope="row">Mechanical</td>
                                        <td>Provision of chilled water pipework to an Owner nominated point within or on
                                            the boundary of the Premises. Capacity of the chilled water as nominated on
                                            the POD for the Premises.</td>
                                        <td></td>

                                    </tr>
                                    <tr>
                                        <td scope="row"></td>
                                        <td>Provision of a fresh air supply duct to an Owner nominated point within or
                                            on the boundary of the Premises. Capacity of the subpply duct as nominated
                                            on the POD for the Premises.</td>
                                        <td></td>

                                    </tr>
                                    <tr>
                                        <td scope="row"></td>
                                        <td>Provision of a common system kitchen exhausts connection point, to an Owner
                                            nominated point within or on the boundary of the Premises. Capacity of the
                                            kitchen exhaust as nominated on the POD for the premises. Maximum dimension
                                            of the duct is 60cm x 6cm.</td>
                                        <td></td>

                                    </tr>
                                    <tr>
                                        <td scope="row"></td>
                                        <td>Air handling Units (as specified on the POD’s for the Premises)</td>
                                        <td></td>

                                    </tr>
                                    <tr>
                                        <td scope="row">Gas</td>
                                        <td>Provision of gas supply pipe work to an Owner nominated point within or on
                                            the boundary of the Premises – to nominated Premises only, as detailed on
                                            the POD for the Premises.</td>
                                        <td></td>

                                    </tr>
                                    <tr>
                                        <td scope="row">Plumbing</td>
                                        <td>The provision of a cold water supply and drainage oulet to an Owner
                                            nominated point within or on the boundary of the Premises.</td>
                                        <td></td>

                                    </tr>
                                    <tr>
                                        <td scope="row"></td>
                                        <td>Supply and installation of a 10cm sewer drain in all restaurant premises in
                                            excess of 200 m2 in size (up to 10cm above the structural slab). This
                                            includes floor slab penetrations with all setout by the Investor.</td>
                                        <td></td>
                                    </tr>
									</tbody>
								 </table>
							</div>
						</h5>
					</div>
				</div>
			</div>
		</section>
		</div><!---->
	<h5>
    <br>
    <br>
    <br>
    <br>
    <p style="page-break-after: always;">&nbsp;</p>
	 <!-- Second Page starts from here -->
    <div class="book book col-8 mx-auto">
        <section class="page-2">
            <div class="page">
                <div class="subpage 2">
                    <div class="row">
						<div class="col-3 d-flex align-items-center">
						<div class="logo-img">
						<img class="w-50" src="../assets/tamdeen-logo/tamdeen-logo.png" alt="">
						</div>
				    </div>
					<div class="col-6 d-flex align-items-center">
						<div class="border border-secondary">
						<h2 class="text-danger">Handover Certificate</h2>
						</div>
					</div>
					<div class="ex2">
						<div class="container">
							<table class="table table-bordered">
								<thead class="thead-light">
									<tr>
										<th scope="col">Shopfront</th>
										<th scope="col">Shopfront</th>
										<th scope="col">Complete Yes/No</th>

									</tr>
								</thead>
								<tbody>
									<tr>
										<td scope="row">Blade correction and armyature</td>
										<td>A wall or ceiling mounted whichever the case mayby armyature for a blade
											Sign – in those Premises
											and locations as nominated by the Owner.</td>
										<td></td>
									</tr>
								</tbody>
							</table>
						</div>
						<br>
						<DIV STYLE="background-color:#000000; height:5px; width:100%;">
						</DIV>
                     </div>
					 <div class="ex3">
                            <div class="date-complete">
                                <div class="row">
                                    <div class="col-10">
                                        <div class="uncompleted / Defective Works (If any)">
                                            <div class="title">
                                                <h5>
                                                    Uncompleted / Defective Works (If any)
                                                </h5>
                                            </div>
                                            <div class="border-line">
                                                <div class="form-group">

                                                    <input type="text" class="form-control border-bottom">
                                                </div>
                                                <div class="form-group">

                                                    <input type="text" class="form-control border-bottom">
                                                </div>
                                                <div class="form-group">

                                                    <input type="text" class="form-control border-bottom">
                                                </div>
                                                <div class="form-group">

                                                    <input type="text" class="form-control border-bottom">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="completetion Date">
                                            <div class="title">
                                                <h5>
                                                    completetion Date
                                                </h5>
                                            </div>
                                            <div class="border-line">
                                                <div class="form-group">

                                                    <input type="text" class="form-control border-bottom">
                                                </div>
                                                <div class="border-line">

                                                    <div class="form-group">

                                                        <input type="text" class="form-control border-bottom">
                                                    </div>

                                                    <div class="form-group">

                                                        <input type="text" class="form-control border-bottom">
                                                    </div>

                                                    <div class="form-group">

                                                        <input type="text" class="form-control border-bottom">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <DIV STYLE="background-color:#000000; height:5px; width:100%;">
                            </DIV>
                        </div>
						<div class="ex4">
                            <h4>Acceptance of Handover of Premises</h4>
                            <br>
                            <h5>
                                <p>The undersigned Investor’s authorised representative herby confirms:</p>
                                <br>
                                <ul>
                                    <li>They have vested authority of the Investor to accept handover of the premises as
                                        shown on this form.</li>
                                    <br>
                                    <li>They have fully inspected the premises on the Inspection Date shown and confirm
                                        that all Owner’s works as specified in the Investor’s
                                        Fitout & Design Guideline have been completed in full, and / or will be
                                        completed in full by the completion date as
                                        shown </li>
                                    <br>
                                    <li>They confirm formal handover acceptance of the Premises effective from the
                                        Handover Date shown in the right hand corner
                                        of the
                                        front page of this form, and acknowledge that no works of any description may
                                        occur within the Premises without the
                                        prior written
                                        approval of the Owner.</li>
                                </ul>
                            </h5>
                            <DIV STYLE="background-color:#000000; height:5px; width:100%;">
                            </DIV>
                        </div>
                        <br>
						<div class="ex5">
                        <div class="container">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th scope="col">Authorization</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Signature</th>
                                        <th scope="col">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td scope="row">Investor’s Authorized Representative</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>

                                    </tr>
                                    <tr>
                                        <td scope="row">Investor Project Manager</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>

                                    </tr>
                                    <tr>
                                        <td scope="row">RDD Project Manager</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>

                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        </div>
                        <br>
                        <br>
						<div class="ex6">
                            <p>CC: Finance Dept</p>
                            <p>Centre Manager</p>
                            <p> GM – Leasing</p>
                            <p> GM - Marketing & Operations</p>
                            <p> Chief Operating Officer</p>
                        </div>
					</div><!--end of row-->
				</div>
			</div>
		</section>
	</div>
	</h5>
</div> <!--final->