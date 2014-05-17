<?php

class ReplyController extends BaseController {

	public function addReply($id)
	{		
		$reply = new Reply;
		$reply->content = Input::get('content');
		$reply->date = date("Y-m-d H:i:s");
		$reply->by = Input::get('username'); # Je moet in het formulier het id van de user invullen
		$reply->topics_id = $id;
		$reply->save();
		
		echo "REACTIE IS TOEGEVOEGD !!!";
		
		$allInfoTopic = $this->getTopicInfo($id);
		
		$infoTopic = $allInfoTopic['infoTopic'];
		$infoReplies = $allInfoTopic['infoReplies'];
		
		return View::make('topic')->with('topic', $infoTopic)->with('replies', $infoReplies);
	}

	public function getTopicInfo($id)
	{
		$allInfoTopic = array();
		
		$infoTopic = array();
		$infoTopic['topic'] = Topic::find($id);
		$infoTopic['by'] = User::find($infoTopic['topic']->by);
		
		$infoReplies = array();
		$dbReplies = Reply::where('topics_id', '=', $id)->get();
		foreach ($dbReplies as $reply) {
			$infoReply = array();
			$infoReply['reply'] = $reply;
			$infoReply['by'] = User::find($reply->by);
			array_push($infoReplies, $infoReply);
		}
		
		$allInfoTopic['infoTopic'] = $infoTopic;
		$allInfoTopic['infoReplies'] = $infoReplies;
		
		return $allInfoTopic;
	}
	
	/*public function addReactie($id)
	{	
		$reactie = new Reactie;
		$reactie->inhoud = Input::get('inhoud');
		$reactie->datum_tijd = date("Y-m-d H:i:s");
		$reactie->gebruikersnaam = Input::get('gebruikersnaam');
		$reactie->topic_id = $id;
		$reactie->save();
	
		echo "REACTIE IS TOEGEVOEGD !!!";
		
		$topic = Topic::find($id);
		$reacties = Reactie::where('topic_id', '=', $id)->get();
		return View::make('topic')->with('topic', $topic)->with('reacties', $reacties);
	}*/

}

?>