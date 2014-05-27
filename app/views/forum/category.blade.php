@extends('layout/main')

@section('content')

	<div class="col-md-12 titleBlock bg-primary">
		{{ $subcategory['name'] }}
	</div>
	
	@if ($openTopics == null && $closedTopics == null)
		<div class="col-md-12 messageBlock">
			There are no topics at the moment.
		</div>
		<div>
			{{ Form::open(array('route' => array('forum-topic-create-get', $name))) }}
				{{ Form::submit('New topic', array('class' => 'btn-primary button')) }}
			{{ Form::close() }}
		</div>
	@else
		<div>
			{{ Form::open(array('route' => array('forum-topic-create-get', $name))) }}
				{{ Form::submit('New topic', array('class' => 'btn-primary button')) }}
			{{ Form::close() }}
		</div>
		<table class="col-sm-12 topicsTable">
			<tr>
				<th class="col-sm-6 topicsTabelNameTD"></th>
				<th class="col-sm-2 tableTD">By</th>
				<th class="col-sm-2 tableTD">Replies</th>
				<th class="col-sm-2 topicsTabelLastReplyTD">Last reply</th>
			</tr>
			@foreach ($openTopics as $infoTopic)
				<tr>
					<td class="col-sm-6 topicsTabelNameTD">{{ link_to_route('forum-topic', $infoTopic['topic']->title, array('id' => $infoTopic['topic']->id )) }}</td>
					<td class="col-sm-2 tableTD">{{ $infoTopic['by'] }}</td>
					<td class="col-sm-2 tableTD">{{ $infoTopic['amountOfReplies'] }}</td>
					@if ($infoTopic['lastReply'] == 0) 
						<td class="col-sm-2 topicsTabelLastReplyTD"> - </td>
					@else
						<td class="col-sm-2 topicsTabelLastReplyTD">{{ $infoTopic['lastReply'] }}</td>
					@endif
				</tr>
			@endforeach
			@foreach ($closedTopics as $infoTopic)
				<tr>
					<td class="col-sm-6 topicsTabelNameTD">{{ link_to_route('forum-topic', $infoTopic['topic']->title, array('id' => $infoTopic['topic']->id )) }}</td>
					<td class="col-sm-2 tableTD">{{ $infoTopic['by'] }}</td>
					<td class="col-sm-2 tableTD">{{ $infoTopic['amountOfReplies'] }}</td>
					@if ($infoTopic['lastReply'] == 0) 
						<td class="col-sm-2 topicsTabelLastReplyTD"> - </td>
					@else
						<td class="col-sm-2 topicsTabelLastReplyTD">{{ $infoTopic['lastReply'] }}</td>
					@endif
				</tr>
			@endforeach
		</table>
	@endif

@stop