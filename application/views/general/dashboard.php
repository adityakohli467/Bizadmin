<?php
    $currentHour = date('H');
    if ($currentHour >= 5 && $currentHour < 12) {
        $greeting = 'Good Morning';
        $greetingIcon = 'bx bxs-sun';
    } elseif ($currentHour >= 12 && $currentHour < 18) {
        $greeting = 'Good Afternoon';
        $greetingIcon = 'bx bxs-sun';
    } else {
        $greeting = 'Good Evening';
        $greetingIcon = 'bx bxs-moon';
    }
    $currentLocationId = $this->session->userdata('location_id');
    $totalLocations    = !empty($userLocations) ? count($userLocations) : 0;
    // rotating accent palette for the cards
    $palette = [
        ['#06b6a4', '#0ea5b7'], // teal
        ['#6366f1', '#8b5cf6'], // indigo/violet
        ['#f59e0b', '#f97316'], // amber
        ['#ec4899', '#d946ef'], // pink
        ['#3b82f6', '#06b6d4'], // blue
        ['#10b981', '#22c55e'], // green
    ];
?>

<div id="locationDashboard">

    <!-- Hero -->
    <div class="ld-hero">
        <div class="ld-hero-inner">
            <div class="ld-greeting">
                <span class="ld-greeting-icon"><i class="<?php echo $greetingIcon; ?>"></i></span>
                <h1>
                    <?php echo $greeting; ?>,
                    <span class="ld-name"><?php echo htmlspecialchars($this->session->userdata('username'), ENT_QUOTES, 'UTF-8'); ?></span>
                </h1>
            </div>
            <p class="ld-subtitle">Please select a location to get started</p>
            <span class="ld-pill">
                <i class="bx bxs-map"></i>
                <?php echo $totalLocations; ?> location<?php echo $totalLocations == 1 ? '' : 's'; ?> available
            </span>
        </div>
    </div>

    <?php if($totalLocations >= 4) { ?>
    <!-- Search / filter -->
    <div class="ld-search-wrap">
        <div class="ld-search">
            <i class="bx bx-search"></i>
            <input type="text" id="ldSearchInput" placeholder="Search locations..." autocomplete="off">
        </div>
    </div>
    <?php } ?>

    <!-- Cards -->
    <div class="ld-cards-wrap">
        <div class="ld-section-label">Your locations</div>
        <div class="row g-3" id="ldCardGrid">
            <?php if(!empty($userLocations)) { ?>
                <?php $i = 0; foreach($userLocations as $loc_id => $location_name) {
                    $encoded_params = custom_encode($loc_id, $location_name);
                    $url            = base_url("auth/checklist/{$encoded_params}");
                    $colors         = $palette[$i % count($palette)];
                    $initial        = strtoupper(mb_substr(trim($location_name), 0, 1));
                    $isCurrent      = ($currentLocationId != '' && $currentLocationId == $loc_id);
                    $i++;
                ?>
                <div class="col-6 col-sm-6 col-md-4 col-lg-3 ld-card-col" data-name="<?php echo htmlspecialchars(strtolower($location_name), ENT_QUOTES); ?>">
                    <a href="<?php echo $url; ?>" class="ld-card <?php echo $isCurrent ? 'is-current' : ''; ?>"
                       style="--c1: <?php echo $colors[0]; ?>; --c2: <?php echo $colors[1]; ?>;">

                        <?php if($isCurrent) { ?>
                        <span class="ld-badge"><i class="bx bx-check"></i> Current</span>
                        <?php } ?>

                        <div class="ld-card-icon"><?php echo $initial; ?></div>

                        <div class="ld-card-name"><?php echo htmlspecialchars($location_name, ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="ld-card-sub"><i class="bx bxs-map"></i> Tap to open</div>

                        <div class="ld-card-arrow">
                            <i class="bx bx-right-arrow-alt"></i>
                        </div>
                    </a>
                </div>
                <?php } ?>
            <?php } else { ?>
                <div class="col-12">
                    <div class="ld-empty">
                        <i class="bx bx-map-alt"></i>
                        <p>No locations have been assigned to your account yet.</p>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="ld-no-results" id="ldNoResults" style="display:none;">
            <i class="bx bx-search-alt"></i>
            <p>No locations match your search.</p>
        </div>
    </div>

</div>

<style>
#locationDashboard{
    position:relative;
    min-height:calc(100vh - 70px);
    margin-top:60px;
    padding-bottom:120px;
    overflow:hidden;
}

/* Hero */
#locationDashboard .ld-hero{position:relative;padding:2.5rem 1.5rem 1rem;}
#locationDashboard .ld-hero::before{content:'';position:absolute;top:-120px;right:-60px;width:380px;height:380px;background:radial-gradient(circle,rgba(99,102,241,.10) 0%,transparent 70%);pointer-events:none;}
#locationDashboard .ld-hero::after{content:'';position:absolute;bottom:-80px;left:-60px;width:300px;height:300px;background:radial-gradient(circle,rgba(6,182,164,.10) 0%,transparent 70%);pointer-events:none;}
#locationDashboard .ld-hero-inner{max-width:1040px;margin:0 auto;position:relative;z-index:1;}
#locationDashboard .ld-greeting{display:flex;align-items:center;gap:.6rem;margin-bottom:.25rem;}
#locationDashboard .ld-greeting-icon{width:42px;height:42px;border-radius:12px;display:inline-grid;place-items:center;background:linear-gradient(135deg,#1a2f52,#2a3a82);color:#ffd166;font-size:1.3rem;flex-shrink:0;}
#locationDashboard .ld-greeting h1{font-size:clamp(1.5rem,3.5vw,2rem);font-weight:700;color:#1a2f52;margin:0;letter-spacing:-.02em;}
#locationDashboard .ld-greeting .ld-name{background:linear-gradient(135deg,#6366f1,#06b6a4);-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;}
#locationDashboard .ld-subtitle{color:#6b7280;font-size:clamp(.9rem,2vw,1.05rem);margin:.15rem 0 0 3.4rem;}
#locationDashboard .ld-pill{display:inline-flex;align-items:center;gap:.45rem;margin:.85rem 0 0 3.4rem;background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:.45rem .9rem;font-size:.82rem;font-weight:500;color:#475569;box-shadow:0 1px 3px rgba(0,0,0,.04);}
#locationDashboard .ld-pill i{color:#06b6a4;font-size:1rem;}

/* Search */
#locationDashboard .ld-search-wrap{max-width:1040px;margin:1.5rem auto 0;padding:0 1.5rem;}
#locationDashboard .ld-search{display:flex;align-items:center;gap:.6rem;background:#fff;border:2px solid #e5e7eb;border-radius:14px;padding:.65rem 1rem;transition:border-color .2s,box-shadow .2s;}
#locationDashboard .ld-search:focus-within{border-color:#6366f1;box-shadow:0 0 0 4px rgba(99,102,241,.10);}
#locationDashboard .ld-search i{color:#9ca3af;font-size:1.25rem;}
#locationDashboard .ld-search input{border:0;outline:0;flex:1;font-size:.92rem;color:#1a2f52;background:transparent;}

/* Cards */
#locationDashboard .ld-cards-wrap{max-width:1040px;margin:1.75rem auto 0;padding:0 1.5rem;position:relative;z-index:1;}
#locationDashboard .ld-section-label{font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:#9ca3af;margin-bottom:.85rem;}

#locationDashboard .ld-card{
    position:relative;display:block;height:100%;background:#fff;border:2px solid transparent;
    border-radius:18px;padding:1.4rem;text-decoration:none;overflow:hidden;
    transition:transform .25s cubic-bezier(.4,0,.2,1),box-shadow .25s,border-color .25s;
    box-shadow:0 1px 4px rgba(16,24,64,.04);
}
#locationDashboard .ld-card::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,var(--c1),var(--c2));transform:scaleX(0);transform-origin:left;transition:transform .3s;}
#locationDashboard .ld-card:hover{transform:translateY(-4px);box-shadow:0 12px 32px rgba(16,24,64,.12);border-color:#eef0ff;}
#locationDashboard .ld-card:hover::before{transform:scaleX(1);}
#locationDashboard .ld-card.is-current{border-color:var(--c1);background:linear-gradient(135deg,#ffffff 0%,#f7f8ff 100%);}
#locationDashboard .ld-card.is-current::before{transform:scaleX(1);}

#locationDashboard .ld-badge{position:absolute;top:1rem;right:1rem;display:inline-flex;align-items:center;gap:3px;background:var(--c1);color:#fff;font-size:.6rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;padding:3px 9px;border-radius:20px;}
#locationDashboard .ld-badge i{font-size:.8rem;}

#locationDashboard .ld-card-icon{width:54px;height:54px;border-radius:14px;display:grid;place-items:center;font-size:1.4rem;font-weight:700;color:#fff;background:linear-gradient(135deg,var(--c1),var(--c2));margin-bottom:1rem;box-shadow:0 6px 16px -4px var(--c1);}
#locationDashboard .ld-card-name{font-size:1.05rem;font-weight:700;color:#1a2f52;line-height:1.3;word-break:break-word;}
#locationDashboard .ld-card-sub{font-size:.75rem;color:#94a3b8;margin-top:.3rem;display:flex;align-items:center;gap:4px;}
#locationDashboard .ld-card-sub i{font-size:.95rem;}

#locationDashboard .ld-card-arrow{position:absolute;right:1.1rem;bottom:1.1rem;width:32px;height:32px;border-radius:10px;background:#f1f5f9;display:grid;place-items:center;transition:background .2s,transform .2s;}
#locationDashboard .ld-card-arrow i{font-size:1.2rem;color:#94a3b8;transition:color .2s;}
#locationDashboard .ld-card:hover .ld-card-arrow{background:linear-gradient(135deg,var(--c1),var(--c2));transform:translateX(2px);}
#locationDashboard .ld-card:hover .ld-card-arrow i{color:#fff;}

/* Empty / no-results */
#locationDashboard .ld-empty,#locationDashboard .ld-no-results{text-align:center;padding:3rem 1rem;color:#94a3b8;}
#locationDashboard .ld-empty i,#locationDashboard .ld-no-results i{font-size:3rem;display:block;margin-bottom:.5rem;color:#cbd5e1;}
#locationDashboard .ld-empty p,#locationDashboard .ld-no-results p{margin:0;font-size:.95rem;}

@media (max-width:575.98px){
    #locationDashboard .ld-subtitle,#locationDashboard .ld-pill{margin-left:0;}
    #locationDashboard .ld-card{padding:1.1rem;}
    #locationDashboard .ld-card-icon{width:46px;height:46px;font-size:1.2rem;}
}
</style>

<script>
$(document).ready(function(){
    // Clear stored location-scoped state when switching location
    var keys = ["minOrderInfoRead","suppId","dateFrom","dateTo","selectedSiteFoodTempDashBoard",
        "selectedSiteDashBoard","selectedSiteCleanDashBoard","Weekly_from_delivery_date","Weekly_to_delivery_date"];
    keys.forEach(function(k){ localStorage.removeItem(k); });

    // Client-side location filter
    $('#ldSearchInput').on('input', function(){
        var term = $(this).val().toLowerCase().trim();
        var visible = 0;
        $('#ldCardGrid .ld-card-col').each(function(){
            var match = $(this).data('name').indexOf(term) !== -1;
            $(this).toggle(match);
            if(match){ visible++; }
        });
        $('#ldNoResults').toggle(visible === 0);
    });
});
</script>
