<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bizadmin | Caf&eacute; Management Software for Australian Businesses</title>
    <meta name="description" content="Replace Deputy, Tanda, and spreadsheets with one platform built for Australian cafes. HR, rostering, HACCP temps, supplier orders, and Fair Work compliance - all in one login. 30-day free trial.">
    <link rel="stylesheet" href="<?php echo base_url(""); ?>theme-assets/css/tailwind.min.css">
    <?php $this->load->view('general/tailwind_common_assets'); ?>
     <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Gelasio:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
  :root {
    --color-navy: #1A2942;
    --color-orange: #F05D5E;
    --color-coral: #FF7A59;
    --color-cream: #FFF9F2;
    --color-sand: #F7EBDD;
    --color-gray: #4A5568;
    --color-lightgray: #F3F4F6;
  }

  body { font-family: 'Inter', sans-serif; }
  h1, h2, h3, h4, h5, h6 { font-family: 'Gelasio', serif; }
  .bg-gradient-hero { background: linear-gradient(135deg, #1A2942 0%, #2A3B5A 100%); }

  ::-webkit-scrollbar { display: none; }
  html, body { -ms-overflow-style: none; scrollbar-width: none; }

  #timeline img { height: 320px !important; width: 320px !important; }

  #mobileMenu { transform: translateX(100%); transition: transform 300ms ease-in-out; }
  #mobileMenu.is-open { transform: translateX(0); }

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
   
<body class="bg-[#FFF9F2] text-[#1A2942] antialiased overflow-x-hidden">
    <?php $this->load->view('general/MainWebsitePages/navbar'); ?>