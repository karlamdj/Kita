@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{ asset('images/logo-email.png') }}" class="logo" alt="KITA Logo">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
