<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-18329612204"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'AW-18329612204');
    </script>
    <!-- Event snippet for Page view conversion page -->
    <script>
      function gtag_report_conversion(url) {
        var callback = function () {
          if (typeof(url) != 'undefined') {
            window.location = url;
          }
        };
        gtag('event', 'conversion', {
            'send_to': 'AW-18329612204/ge7ZCKa1_tEcEKzfnqRE',
            'value': 1.0,
            'currency': 'INR',
            'event_callback': callback
        });
        return false;
      }
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>BizAdmin</title>
    <?php $tw_css = FCPATH . 'theme-assets/css/tailwind.min.css'; $tw_ver = is_file($tw_css) ? filemtime($tw_css) : time(); ?>
    <link rel="stylesheet" href="<?php echo base_url(''); ?>theme-assets/css/tailwind.min.css?v=<?php echo $tw_ver; ?>">
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
  
   #mobileMenu {
            transform: translateX(100%);
            transition: transform 300ms ease-in-out;
        }
        #mobileMenu.is-open {
            transform: translateX(0);
        }
  </style>
  
  </head>
  
  <html>
   
<body class="font-inter text-gray-800 ">
    <?php $this->load->view('general/MainWebsitePages/navbar'); ?>