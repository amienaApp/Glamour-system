<?php
// Admin Header with Font Awesome fixes
?>
<!-- Font Awesome Icons - Multiple CDN sources for reliability -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer">
<!-- Fallback Font Awesome CDN -->
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.0.0/css/all.css" crossorigin="anonymous">
<!-- Additional fallback -->
<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v6.0.0/css/all.css" crossorigin="anonymous">

<style>
/* Font Awesome Fallback Styles */
.fas, .fa {
    font-family: "Font Awesome 6 Free", "Font Awesome 6 Pro", "FontAwesome", sans-serif !important;
    font-weight: 900;
    font-style: normal;
    font-variant: normal;
    text-rendering: auto;
    line-height: 1;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* Fallback icons when Font Awesome fails to load */
.fas.fa-gem::before { content: "💎"; }
.fas.fa-tachometer-alt::before { content: "📊"; }
.fas.fa-plus::before { content: "➕"; }
.fas.fa-eye::before { content: "👁️"; }
.fas.fa-boxes::before { content: "📦"; }
.fas.fa-folder::before { content: "📁"; }
.fas.fa-shopping-cart::before { content: "🛒"; }
.fas.fa-credit-card::before { content: "💳"; }
.fas.fa-users::before { content: "👥"; }
.fas.fa-user-shield::before { content: "🛡️"; }
.fas.fa-store::before { content: "🏪"; }
.fas.fa-sign-out-alt::before { content: "🚪"; }
.fas.fa-chart-pie::before { content: "📈"; }
.fas.fa-activity::before { content: "⚡"; }
.fas.fa-box::before { content: "📦"; }
.fas.fa-tags::before { content: "🏷️"; }
.fas.fa-user-check::before { content: "✅"; }
.fas.fa-plus-circle::before { content: "➕"; }
.fas.fa-list::before { content: "📋"; }
.fas.fa-bars::before { content: "☰"; }
.fas.fa-edit::before { content: "✏️"; }
.fas.fa-trash::before { content: "🗑️"; }
.fas.fa-save::before { content: "💾"; }
.fas.fa-search::before { content: "🔍"; }
.fas.fa-filter::before { content: "🔽"; }
.fas.fa-download::before { content: "⬇️"; }
.fas.fa-upload::before { content: "⬆️"; }
.fas.fa-cog::before { content: "⚙️"; }
.fas.fa-home::before { content: "🏠"; }
.fas.fa-arrow-left::before { content: "←"; }
.fas.fa-arrow-right::before { content: "→"; }
.fas.fa-check::before { content: "✓"; }
.fas.fa-times::before { content: "✗"; }
.fas.fa-exclamation-triangle::before { content: "⚠️"; }
.fas.fa-info-circle::before { content: "ℹ️"; }

/* Hide fallback icons when Font Awesome loads properly */
.fa-loaded .fas.fa-gem::before,
.fa-loaded .fas.fa-tachometer-alt::before,
.fa-loaded .fas.fa-plus::before,
.fa-loaded .fas.fa-eye::before,
.fa-loaded .fas.fa-boxes::before,
.fa-loaded .fas.fa-folder::before,
.fa-loaded .fas.fa-shopping-cart::before,
.fa-loaded .fas.fa-credit-card::before,
.fa-loaded .fas.fa-users::before,
.fa-loaded .fas.fa-user-shield::before,
.fa-loaded .fas.fa-store::before,
.fa-loaded .fas.fa-sign-out-alt::before,
.fa-loaded .fas.fa-chart-pie::before,
.fa-loaded .fas.fa-activity::before,
.fa-loaded .fas.fa-box::before,
.fa-loaded .fas.fa-tags::before,
.fa-loaded .fas.fa-user-check::before,
.fa-loaded .fas.fa-plus-circle::before,
.fa-loaded .fas.fa-list::before,
.fa-loaded .fas.fa-bars::before,
.fa-loaded .fas.fa-edit::before,
.fa-loaded .fas.fa-trash::before,
.fa-loaded .fas.fa-save::before,
.fa-loaded .fas.fa-search::before,
.fa-loaded .fas.fa-filter::before,
.fa-loaded .fas.fa-download::before,
.fa-loaded .fas.fa-upload::before,
.fa-loaded .fas.fa-cog::before,
.fa-loaded .fas.fa-home::before,
.fa-loaded .fas.fa-arrow-left::before,
.fa-loaded .fas.fa-arrow-right::before,
.fa-loaded .fas.fa-check::before,
.fa-loaded .fas.fa-times::before,
.fa-loaded .fas.fa-exclamation-triangle::before,
.fa-loaded .fas.fa-info-circle::before {
    content: "" !important;
}
</style>

<script>
// Font Awesome Load Detection
function checkFontAwesomeLoaded() {
    const testIcon = document.createElement('i');
    testIcon.className = 'fas fa-gem';
    testIcon.style.position = 'absolute';
    testIcon.style.left = '-9999px';
    testIcon.style.fontSize = '16px';
    document.body.appendChild(testIcon);
    
    const computedStyle = window.getComputedStyle(testIcon);
    const fontFamily = computedStyle.getPropertyValue('font-family');
    
    document.body.removeChild(testIcon);
    
    if (fontFamily.includes('Font Awesome')) {
        document.body.classList.add('fa-loaded');
        console.log('Font Awesome loaded successfully');
    } else {
        console.log('Font Awesome not loaded, using fallback icons');
    }
}

// Check when page loads
window.addEventListener('load', checkFontAwesomeLoaded);

// Also check after a delay
setTimeout(checkFontAwesomeLoaded, 2000);
</script>

