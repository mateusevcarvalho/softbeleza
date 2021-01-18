<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{url('images/softbeleza-logo-azul.png')}}" alt="Softbeleza Logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
