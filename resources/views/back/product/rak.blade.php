<tr>
	<td>
		<a href="{{URL::to(Crypt::decrypt($setting->admin_url) . '/rak/' . $rak->id)}}" style="color: blue;">
			{{$rak->name}}
		</a>
	</td>
	<td>
		{{$rak->code_id}}
		{!! Form::hidden('raks[]', $rak->id) !!}
	</td>
	<td class="rak-delete rak{{$rak->id}}" dataid="{{$rak->id}}">
		Delete
	</td>
</tr>