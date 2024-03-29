<?php

class CategoryController extends BaseController {

	# Maakt de view aan met alle subcategorie�n per hoofdcategorie
	public function getCategories()
	{
		$allCategories = array(); # Bevat dadelijk alle hoofdcategorieen met daaraan gekoppeld de bijbehorende subcategorieen met alle info
		$categories = $this->getMainCategories();
		foreach ($categories as $infoCategory) {
			$infoSubcategories = array(); # Bevat dadelijk alle subcategorieen met alle info van een hoofdcategorie
			foreach ($infoCategory['subcategories'] as $subcategory) {
				$infoSubcategory = array();  # Bevat dadelijk alle subcategorieen met alle info
				$infoSubcategory['id'] = $subcategory->id;
				$infoSubcategory['name'] = $subcategory->name;
				$infoSubcategory['description'] = $subcategory->description;
				$infoSubcategory['amountOfTopics'] = $subcategory->getAmountOfTopics();
				$infoSubcategory['amountOfReplies'] = $subcategory->getAmountOfReplies();
				$infoSubcategory['lastReply'] = $subcategory->getLastReply();
				array_push($infoSubcategories, $infoSubcategory);
			}
			$infoCategory['subcategories'] = $infoSubcategories;
			array_push($allCategories, $infoCategory);
		}		
		return View::make('forum/forum')->with('categories', $allCategories);
	}
	
	# Geeft aan hoofdcategorie�n terug met de bijbehorende subcategorie�n
	public function getMainCategories() {
	
		$allCategories = array(); # Bevat dadelijk alle hoofdcategorieen met daaraan gekoppeld de bijbehorende subcategorieen
	
		$dbCategories = Category::all();
		foreach ($dbCategories as $category) {
			
			$dbSubcategories = Subcategory::where('categories_id','=',$category->id)->get();
			
			$infoCategory = array(); # Bevat dadelijk alle info van een categorie
			$infoCategory['category'] = $category;
			$infoCategory['subcategories'] = $dbSubcategories;
			
			array_push($allCategories, $infoCategory);
		}
		
		return $allCategories;
	}

	# Maakt een view aan waarop alle topics van een subcategorie staan
	public function getCategory($id)
	{
		$subcategory = Subcategory::find($id);		
		if (!isset($subcategory->name))
		{
			return Redirect::route('home')->with('global','This subcategory does not exist.');
		}
		$infoOpenTopics = array();
		$infoClosedTopics = array();
		$topics = $this->getTopicsWithInfo($id); # Deze lijst met alle topics wordt dadelijk gesplist in 2 lijsten (Open topics en gesloten topics)
		foreach ($topics as $infoTopic) {
			if($infoTopic['topic']->open == true) {
				array_push($infoOpenTopics, $infoTopic);
			}
			else {
				array_push($infoClosedTopics, $infoTopic);
			}
		}
		return View::make('forum/category')->with('subcategory', $subcategory)->with('openTopics', $infoOpenTopics)->with('closedTopics', $infoClosedTopics)->with('name', $id);
	}
	
	# Geeft alle topics met de bijbehorende informatie terug
	public function getTopicsWithInfo($id)
	{
		$allTopics = array(); # Bevat dadelijk van alle topics alle info
		
		$dbTopics = Topic::where('subcategories_id','=', $id)->where('sticky','=',NULL)->get();
		foreach ($dbTopics as $topic) {
			$infoTopic = array(); # Bevat dadelijk alle info van een topic
			$infoTopic['topic'] = $topic;
			$user = User::find($topic->by);
			$infoTopic['by'] = $user->username;
			$infoTopic['amountOfReplies'] = $topic->getAmountOfReplies();
			$infoTopic['lastReply'] = $topic->getLastReply();
			array_push($allTopics, $infoTopic);
		}

		usort($allTopics,function ($a,$b)
		{
			return strcmp($b['lastReply'],$a['lastReply']);
		});

		$dbTopics = Topic::where('subcategories_id','=', $id)->where('sticky','=','true')->get();
		foreach ($dbTopics as $topic) {
			$infoTopic = array(); # Bevat dadelijk alle info van een topic
			$infoTopic['topic'] = $topic;
			$user = User::find($topic->by);
			$infoTopic['by'] = $user->username;
			$infoTopic['amountOfReplies'] = $topic->getAmountOfReplies();
			$infoTopic['lastReply'] = $topic->getLastReply();
			array_push($allTopics, $infoTopic);
		}
		
		return $allTopics;
	}

	# Maakt een view aan om hoofdcategorie�n en subcategorie�n toe te voegen
	public function getManageCategories()
	{
		$categories = $this->getMainCategories();
		return View::make('settings/categories')->with('categories',$categories);
	}

	# Zet een subcategorie in de database
	public function postSubcategory()
	{
		$validator = Validator::make(Input::all(),
			array(
				'subcategoryname' => 'required',
			)
		);

		if($validator->fails())
		{
			return Redirect::route('categories-manage')->withErrors($validator)->withInput();
		}
		else
		{
			$subcategory = new Subcategory;
			$subcategory->name = Input::get('subcategoryname');
			$subcategory->description = Input::get('subcategorydescription');
			$subcategory->categories_id = Input::get('category');
			$subcategory->save();

			return Redirect::route('categories-manage');
		}
	}

	# Zet een hoofdcategorie in de database
	public function postCategory()
	{
		$validator = Validator::make(Input::all(),
			array(
				'categoryname' => 'required',
			)
		);

		if($validator->fails())
		{
			return Redirect::route('categories-manage')->withErrors($validator)->withInput();
		}
		else
		{
			$category = new Category;
			$category->name = Input::get('categoryname');
			$category->description = Input::get('categorydescription');
			$category->save();

			return Redirect::route('categories-manage');
		}
	}
	
	# Maakt een view aan om een hoofdcategorie te wijzigen
	public function getUpdateSubcategory($id)
	{
		$subcategory = Subcategory::find($id);
		if (!isset($subcategory->name))
		{
			return Redirect::route('home')->with('global','This subcategory does not exist.');
		}
		return View::make('settings/updateSubcategory')->with('subcategory', $subcategory);
	}
	
	# Zet een gewijzigde subcategorie in de database
	public function postUpdateSubcategory()
	{
		$validator = Validator::make(Input::all(),
			array(
				'subcategoryname' => 'required',
			)
		);

		if($validator->fails())
		{
			return Redirect::route('update-subcategory')->withErrors($validator)->withInput();
		}
		else
		{
			$subcategory = Subcategory::find(Input::get('subcategoryID'));
			$subcategory->name = Input::get('subcategoryname');
			$subcategory->description = Input::get('subcategorydescription');
			$subcategory->save();

			return Redirect::route('categories-manage');
		}
	}
	
	# Maakt een view aan om een hoofdcategorie te wijzigen
	public function getUpdateCategory($id)
	{
		$category = Category::find($id);
		if (!isset($category->name))
		{
			return Redirect::route('home')->with('global','This category does not exist.');
		}
		return View::make('settings/updateCategory')->with('category', $category);
	}
	
	# Zet een gewijzigde hoofdcategorie in de database
	public function postUpdateCategory()
	{
		$validator = Validator::make(Input::all(),
			array(
				'categoryname' => 'required',
			)
		);

		if($validator->fails())
		{
			return Redirect::route('update-category')->withErrors($validator)->withInput();
		}
		else
		{
			$category = Category::find(Input::get('categoryID'));
			$category->name = Input::get('categoryname');
			$category->description = Input::get('categorydescription');
			$category->save();

			return Redirect::route('categories-manage');
		}
	}
	
	# Verwijderd een hoofdcategorie uit de database
	public function getDeleteCategory($id)
	{
		$category = Category::find($id);
		if (!isset($category->name))
		{
			return Redirect::route('home')->with('global','This category does not exist.');
		}
		Category::where('id', '=', $id)->delete();
		return Redirect::route('categories-manage')->with('global',$category->name . ' is deleted.');
	}
	
	# Verwijderd een subcategorie uit de database
	public function getDeleteSubcategory($id)
	{
		$category = Subcategory::find($id);
		if (!isset($category->name))
		{
			return Redirect::route('home')->with('global','This subcategory does not exist.');
		}
		Subcategory::where('id', '=', $id)->delete();
		return Redirect::route('categories-manage')->with('global',$category->name . ' is deleted.');
	}
	
}

?>
