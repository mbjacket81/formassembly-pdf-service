<small>RESPONSE {{(isset($metaFields['response_id']) ? "#".$metaFields['response_id'] : "")}}
	{{(isset($metaFields['date_submitted']) ? (" - SUBMITTED ON ".$metaFields['date_submitted']) : "")}}
</small>
<br/>
@if(!empty($formTitle))
	<h1>{!! $formTitle !!}</h1>
@endif
@if(!empty($fieldSets))
	@foreach( $fieldSets as $field_set )
		<h3>{!! $field_set->label !!}</h3>
		<hr>
		<table>";
			@foreach ( $field_set->fields as $field )
				<tr><th>{!! $field->label !!}</th>
					@if ( empty( $field->value ) )
						<td style='color:red'><i>No answer given.</i></td>
					@else
						<td>{!! $field->value !!}</td>
					@endif
				</tr>
			@endforeach
		</table>
	@endforeach
@endif
