<?php

	class Subcategory extends Eloquent {
	
		protected $table = 'subcategories';
		
		# Geeft het aantal topics van de subcategorie terug
		public function getAmountOfTopics()
		{
			$amountOfTopics = Topic::where('subcategories_id', '=', $this->id)->count();
			if (isset($amountOfTopics)) {
				return $amountOfTopics;
			}
		}
		
		# Geeft het aantal replies van de subcategorie terug
		public function getAmountOfReplies() 
		{
			$amountOfReplies = 0;
			$topics = Topic::where('subcategories_id', '=', $this->id)->get();
			foreach ($topics as $topic) {
				$amountOfReplies = $amountOfReplies + Reply::where('topics_id', '=', $topic->id)->count();
			}
			if (isset($amountOfReplies)) {
				return $amountOfReplies;
			}
		}
		
		# Geeft het de datum van de laatste reply van de subcategorie terug
		public function getLastReply() 
		{
			$lastReply = 0;
			$topics = Topic::where('subcategories_id', '=', $this->id)->get();
			foreach ($topics as $topic) {
				$replies = Reply::where('topics_id', '=', $topic->id)->get();
				foreach ($replies as $reply) {
					$curReply = $reply->created_at->format('Y-m-d H:i:s');;
					if ($curReply > $lastReply) {
						$lastReply = $curReply;
					}
				}
			}
			if (isset($lastReply) & $lastReply != 0) {
				return date("d-m-Y H:i", strtotime($lastReply));
			}
		}
		
	}

?>