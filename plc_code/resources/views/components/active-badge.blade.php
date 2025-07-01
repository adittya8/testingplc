@php
  $activaBadgeText = 'Inactive';
  $activeBadgeClass = 'bg-danger';
  if ($active == 1) {
      $activaBadgeText = 'Active';
      $activeBadgeClass = 'bg-success';
  }
@endphp
<span class="badge {{ $activeBadgeClass }}">{{ $activaBadgeText }}</span>
