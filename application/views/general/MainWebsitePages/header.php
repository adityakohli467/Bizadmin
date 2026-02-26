<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>BizAdmin</title>
    <link rel="stylesheet" href="<?php echo base_url(""); ?>theme-assets/css/tailwind.min.css">
    <?php $this->load->view('general/tailwind_common_assets'); ?>
     <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    
    <style>
  .highlighted-section {
    outline: 2px solid #3F20FB;
    background-color: rgba(63, 32, 251, 0.1);
  }

  .edit-button {
    position: absolute;
    z-index: 1000;
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
  
   #mobile-menu {
            transform: translateX(100%);
        }
        #mobile-menu.is-open {
            transform: translateX(0);
        }
        #mobile-menu {
            transition: transform 300ms ease-in-out;
        }
  </style>
  
  </head>
  
  <html>
   
<body class="font-inter text-gray-800 ">
    <?php $this->load->view('general/MainWebsitePages/navbar'); ?>