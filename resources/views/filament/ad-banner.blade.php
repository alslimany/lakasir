<div id="filament-ad-container" class="filament-ad-container" style="text-align:center;padding:1rem;margin-top:1rem;">
  {{-- Ad container for tenant panel. Replace markup with your ad provider's required code. --}}
  <div id="tenant-ad-slot" style="max-width:728px;margin:0 auto;position:relative;">
    <script type="text/javascript">
      atOptions = {
        'key': 'fbed4dce51d3c0564e10a2ea020827b0',
        'format': 'iframe',
        'height': 90,
        'width': 728,
        'params': {}
      };
    </script>
    <script type="text/javascript" src="https://www.highperformanceformat.com/fbed4dce51d3c0564e10a2ea020827b0/invoke.js">
    </script>
  </div>
</div>
<div id="filament-ad-container" class="filament-ad-container" style="text-align:center;padding:1rem;margin-top:1rem;">
  {{-- Ad container for tenant panel. Replace markup with your ad provider's required code. --}}
  <div id="tenant-ad-slot" style="max-width:728px;margin:0 auto;">

  </div>

  {{-- Load ad script if `app.ad_script_url` config is set (set in .env or config/app.php) --}}
  @if (config('app.ad_script_url'))
    <script async src="{{ config('app.ad_script_url') }}"></script>
  @else
  @endif
</div>
