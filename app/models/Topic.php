<?php

	class Topic extends Eloquent {

		public $timestamps = false;
	
		protected $table = 'topics';
		
		public function getAmountOfReplies() 
		{
			$amountOfReplies = Reply::where('topics_id', '=', $topic->id)->count();
			if (isset($amountOfReplies)) {
				return $amountOfReplies;
			}
		}
		
		public function getLastReply() 
		{
			$lastReply = 0;
			$replies = Reply::where('topics_id', '=', $topic->id)->get();
			foreach ($replies as $reply) {
				$curReply = $reply->date;
				if ($curReply > $lastReply) {
					$lastReply = $curReply;
				}
			}
			if (isset($lastReply) & $lastReply != 0) {
				return date("d-m-Y H:i", strtotime($lastReply));
			}
		}
		
	}

?>