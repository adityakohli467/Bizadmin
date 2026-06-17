<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bizadmin | Café Management Software for Australian Businesses</title>
    <meta name="description" content="Replace Deputy, Tanda, and spreadsheets with one platform built for Australian cafés. HR, rostering, HACCP temps, supplier orders, and Fair Work compliance — all in one login. 30-day free trial.">
    <link rel="stylesheet" href="<?php echo base_url(""); ?>theme-assets/css/tailwind.min.css">
    <?php $this->load->view('general/tailwind_common_assets'); ?>
     <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    
    <style>
  :root {
    --color-navy: #0D1B35;
    --color-orange: #F2690D;
    --color-orange-light: #FFF0E6;
    --color-white: #FFFFFF;
    --color-grey-light: #F7F8FA;
    --color-grey-text: #6B7280;
    --color-success: #16A34A;
    --section-padding: 5rem 0;
    --container-max: 1200px;
    --border-radius-card: 12px;
    --border-radius-btn: 50px;
    --shadow-card: 0 4px 24px rgba(0,0,0,0.08);
    --shadow-card-hover: 0 8px 40px rgba(0,0,0,0.14);
  }

  ::-webkit-scrollbar {
    display: none;
  }

  html, body {
    -ms-overflow-style: none;
    scrollbar-width: none;
  }
  #timeline img{
      height:320px !important;
      width : 320px !important;
  }
  .laptopScreen img{
    border-radius: 8px;
    border: 8px solid black;
  }
  
   #mobileMenu {
            transform: translateX(100%);
            transition: transform 300ms ease-in-out;
        }
        #mobileMenu.is-open {
            transform: translateX(0);
        }

  /* FAQ Accordion */
  .faq-answer { max-height: 0; overflow: hidden; transition: max-height 0.3s ease; }
  .faq-item.active .faq-answer { max-height: 500px; }
  .faq-item.active .faq-chevron { transform: rotate(180deg); }
  .faq-chevron { transition: transform 0.3s ease; }

  /* Comparison table mobile scroll */
  .table-scroll-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
  </style>
  
  </head>
  
  <html>
   
<body class="font-inter text-gray-800 ">
    <?php $this->load->view('general/MainWebsitePages/navbar'); ?>