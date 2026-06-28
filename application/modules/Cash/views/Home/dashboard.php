<div class="main-content">
<div class="page-content">
                    <div class="container-fluid">
                       
    <div class="row">
        <?php  foreach($allTills as $allTill) {  ?>
      <div class="col-md-6 col-lg-3">
        <div class="card custom-card-tills">
                                                       <?php if($this->ion_auth->get_users_groups()->row()->name != 'Staff'){ ?>
                                                          <div class="col text-end dropdown" style="text-align: center !important;">
                                                                    <a href="javascript:void(0);" id="dropdownMenuLink3" data-bs-toggle="dropdown" aria-expanded="false">
                                                                        <i class="ri-more-fill fs-17"></i>
                                                                    </a>
                                                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuLink3">
                                                                        <li><a href="<?php echo base_url(); ?>Cash/CashD/<?php echo $allTill['id']; ?>" class="dropdown-item badge badge-outline-success text-black">Register History</a></li>
                                                                        <!--<li><a href="<?php echo base_url(); ?>Cash/BankDeposit/<?php echo $allTill['id']; ?>" class="dropdown-item badge badge-outline-success">Today Banking</a></li>-->
                                                                    </ul>
                                                                </div>
                                                                <?php } ?>
          <div class="card-body">
            <h5 class="card-title"><?php echo $allTill['till_name']; ?></h5>
            <div class="btn-group" role="group">
                 <?php if($allTill['shiftStarted']) {  ?>
                        <a href="<?php echo base_url(); ?>Cash/cashdadd/<?php echo $allTill['id']; ?>" class="btn btn-success waves-effect waves-light  view-btn">Shift Started</a>
                      <?php } else {  ?>
                      <a href="<?php echo base_url(); ?>Cash/cashdadd/<?php echo $allTill['id']; ?>" class="btn bg-orange waves-effect waves-lighte view-btn"><i class=" ri-alarm-line label-icon align-middle fs-14 me-2"></i>Start Shift</a>
                      <?php } ?>
       <?php if($this->ion_auth->get_users_groups()->row()->name == 'Staff' && $allTill['IsStafffinalSubmissionDone'] == 'yes'){ ?>
       <a href="<?php echo base_url(); ?>Cash/endshift/<?php echo $allTill['id']; ?>" class="btn btn-success waves-effect waves-light view-btn" style="margin-left:10px">Shift Ended</a>  
      
     <?php }else if($allTill['IsManagerfinalSubmissionDone'] == 'yes') {  ?>
     <a href="<?php echo base_url(); ?>Cash/endshift/<?php echo $allTill['id']; ?>" class="btn btn-success waves-effect waves-light view-btn" style="margin-left:10px">Shift Ended</a>  
    <?php }else if($allTill['shiftStarted']) {  ?>
     <a href="<?php echo base_url(); ?>Cash/endshift/<?php echo $allTill['id']; ?>" class="btn bg-orange waves-effect waves-light view-btn" style="margin-left:10px"><i class=" ri-timer-fill label-icon align-middle fs-14 me-2"></i>End Shift</a>  
    <?php } else { ?>
    <a href="#" onclick="alert('Please start shift before ending shift.')" class="btn bg-orange waves-effect waves-light view-btn" style="margin-left:10px"><i class=" ri-timer-fill label-icon align-middle fs-14 me-2"></i>End Shift</a>                                                         
    <?php } ?>
                                                              
             
            </div>
          </div>
        </div>
      </div>
      <?php } ?>
     
    </div>
  </div>
 </div>
<footer>
   <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 200">
    <path class="wave-layer" d="M0,150 C240,190,480,90,720,90 C960,90,1200,190,1440,150 V200 H0 V150 Z"></path>
    <path class="wave-layer" fill="#4D529B" d="M0,120 C240,160,480,60,720,60 C960,60,1200,160,1440,120 V200 H0 V120 Z"></path>
    <path class="wave-layer" fill="#8898DC" d="M0,90 C240,130,480,30,720,30 C960,30,1200,130,1440,90 V200 H0 V90 Z"></path>
  </svg>
</footer>
  </div>
<style>
 .wave-layer {
      fill: #1a2f52;
    }
    
    
    .custom-card-tills {
      /*width: 300px;*/
      height: 250px;
      border-radius: 30px;
      background: linear-gradient(to bottom, #272A55, #272A55);
      position: relative;
      overflow: hidden;
      margin-bottom: 20px;
      box-shadow: 0 5px 4px rgba(0, 0, 0, 0.2);
      color: #ffffff !important;
    }
    .custom-card-tills::before {
      content: "";
      position: absolute;
      top: 0;
      left: 60%;
      width: 100%;
      height: 100%;
      background: linear-gradient(to right,#1e2046,#272A55);
      transform: skewX(51deg);
      transform-origin: left top;
    }
    .custom-card-tills .card-body {
      text-align: center;
      color: #fff;
      padding-bottom: 15px;
      position: relative;
      z-index: 1;
      
      display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100%;
    }
    .custom-card-tills .card-title {
      margin-bottom: 0;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .custom-card-tills .btn-group {
      display: flex;
      justify-content: center;
      margin-top: 40px;
    }
   
    @media (max-width: 991.98px) {
      .custom-card-tills {
        margin-right: 10px;
      }
    }
    .card-title{
        color:white !important;
            font-size: 20px !important;
    }
   .badge-outline-success {
           
    border: 1px solid #45CB86;
  
    }
    footer {
  position: absolute;
  width: 100%;
  height: 100px;
  bottom: 0;
  overflow: hidden;
}
  </style>

